<?php

namespace App\Filament\Resources\PurchaseInvoices\Pages;

use App\Filament\Resources\PurchaseInvoices\PurchaseInvoiceResource;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\StockMovement;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreatePurchaseInvoice extends CreateRecord
{
    protected static string $resource = PurchaseInvoiceResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): Model {
            $items = $data['items'] ?? [];
            unset($data['items']);
            $subtotal = 0;
            $preparedItems = [];

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = (float) ($item['quantity'] ?? 0);
                $costPrice = (float) ($item['cost_price'] ?? $product->cost_price);
                $itemDiscount = (float) ($item['discount'] ?? 0);
                $lineSubtotal = $quantity * $costPrice;
                $lineTotal = max($lineSubtotal - $itemDiscount, 0);
                $subtotal += $lineSubtotal;
                $preparedItems[] = compact('product', 'quantity', 'costPrice', 'itemDiscount', 'lineTotal');
            }

            $discount = (float) ($data['discount'] ?? 0);
            $tax = (float) ($data['tax'] ?? 0);
            $total = max($subtotal - $discount + $tax, 0);
            $data['subtotal'] = $subtotal;
            $data['discount'] = $discount;
            $data['tax'] = $tax;
            $data['total'] = $total;
            $data['paid'] = min((float) ($data['paid'] ?? 0), $total);
            $data['status'] = 'received';

            $invoice = PurchaseInvoice::create($data);

            foreach ($preparedItems as $item) {
                $invoice->items()->create([
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'cost_price' => $item['costPrice'],
                    'discount' => $item['itemDiscount'],
                    'tax' => 0,
                    'total' => $item['lineTotal'],
                ]);

                $item['product']->update(['cost_price' => $item['costPrice']]);
                StockMovement::create([
                    'product_id' => $item['product']->id,
                    'warehouse_id' => $invoice->warehouse_id,
                    'user_id' => auth()->id(),
                    'type' => 'in',
                    'quantity' => $item['quantity'],
                    'reference_type' => PurchaseInvoice::class,
                    'reference_id' => $invoice->id,
                    'notes' => 'توريد تلقائي من فاتورة شراء '.$invoice->purchase_number,
                    'occurred_at' => now(),
                ]);
            }

            return $invoice;
        });
    }
}
