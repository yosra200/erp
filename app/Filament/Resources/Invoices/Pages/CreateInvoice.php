<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\StockMovement;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): Model {
            $items = $data['items'] ?? [];
            unset($data['items']);
            $subtotal = 0;
            $preparedItems = [];

            foreach ($items as $index => $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = (float) ($item['quantity'] ?? 0);
                $unitPrice = (float) ($item['unit_price'] ?? $product->sale_price);
                $itemDiscount = (float) ($item['discount'] ?? 0);
                $stock = $product->stockFor((int) $data['warehouse_id']);

                if ($stock < $quantity) {
                    throw ValidationException::withMessages([
                        "data.items.{$index}.quantity" => "الرصيد المتاح من {$product->name} هو ".number_format($stock, 3).' فقط.',
                    ]);
                }

                $lineSubtotal = $quantity * $unitPrice;
                $lineTotal = max($lineSubtotal - $itemDiscount, 0);
                $subtotal += $lineSubtotal;
                $preparedItems[] = compact('product', 'quantity', 'unitPrice', 'itemDiscount', 'lineTotal');
            }

            $discount = (float) ($data['discount'] ?? 0);
            $tax = (float) ($data['tax'] ?? 0);
            $total = max($subtotal - $discount + $tax, 0);
            $data['subtotal'] = $subtotal;
            $data['discount'] = $discount;
            $data['tax'] = $tax;
            $data['total'] = $total;
            $data['paid'] = min((float) ($data['paid'] ?? 0), $total);
            $data['status'] = 'completed';

            $invoice = Invoice::create($data);

            foreach ($preparedItems as $item) {
                $invoice->items()->create([
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unitPrice'],
                    'discount' => $item['itemDiscount'],
                    'tax' => 0,
                    'total' => $item['lineTotal'],
                ]);

                StockMovement::create([
                    'product_id' => $item['product']->id,
                    'warehouse_id' => $invoice->warehouse_id,
                    'user_id' => auth()->id(),
                    'type' => 'out',
                    'quantity' => $item['quantity'],
                    'reference_type' => Invoice::class,
                    'reference_id' => $invoice->id,
                    'notes' => 'صرف تلقائي من فاتورة بيع '.$invoice->invoice_number,
                    'occurred_at' => now(),
                ]);
            }

            return $invoice;
        });
    }
}
