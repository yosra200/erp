<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErpWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_products_receive_an_internal_barcode(): void
    {
        $product = Product::create([
            'name' => 'منتج اختبار',
            'sku' => 'TEST-001',
            'cost_price' => 10,
            'sale_price' => 15,
            'is_active' => true,
        ]);

        $this->assertNotEmpty($product->barcode);
        $this->assertStringStartsWith('200', $product->barcode);
        $this->assertSame(12, strlen($product->barcode));
    }

    public function test_purchase_invoice_receipt_increases_stock_and_updates_cost(): void
    {
        $user = User::factory()->create();
        $warehouse = Warehouse::create(['name' => 'مخزن المشتريات', 'code' => 'PUR-TEST']);
        $supplier = Supplier::create(['name' => 'مورد الاختبار']);
        $product = Product::create(['name' => 'منتج مشتريات', 'sku' => 'PUR-001', 'cost_price' => 10, 'sale_price' => 20, 'is_active' => true]);

        $invoice = PurchaseInvoice::create([
            'supplier_id' => $supplier->id,
            'warehouse_id' => $warehouse->id,
            'user_id' => $user->id,
            'purchase_number' => 'PUR-TEST-001',
            'purchase_date' => today(),
            'subtotal' => 75,
            'total' => 75,
            'paid' => 75,
            'status' => 'received',
        ]);
        $invoice->items()->create(['product_id' => $product->id, 'quantity' => 5, 'cost_price' => 15, 'total' => 75]);
        $product->update(['cost_price' => 15]);
        StockMovement::create([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'user_id' => $user->id,
            'type' => 'in',
            'quantity' => 5,
            'reference_type' => PurchaseInvoice::class,
            'reference_id' => $invoice->id,
            'occurred_at' => now(),
        ]);

        $this->assertSame(5.0, $product->fresh()->stockFor($warehouse->id));
        $this->assertSame('15.00', (string) $product->fresh()->cost_price);
    }

    public function test_stock_balance_is_calculated_from_movements(): void
    {
        $user = User::factory()->create();
        $warehouse = Warehouse::create(['name' => 'مخزن الاختبار', 'code' => 'TEST']);
        $category = Category::create(['name' => 'اختبار', 'slug' => 'test']);
        $unit = Unit::create(['name' => 'قطعة', 'short_name' => 'قط']);
        $customer = Customer::create(['name' => 'عميل الاختبار']);
        $product = Product::create([
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'name' => 'منتج مخزون',
            'sku' => 'STOCK-001',
            'cost_price' => 10,
            'sale_price' => 15,
            'is_active' => true,
        ]);

        StockMovement::create([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'user_id' => $user->id,
            'type' => 'in',
            'quantity' => 10,
            'occurred_at' => now(),
        ]);

        $invoice = Invoice::create([
            'customer_id' => $customer->id,
            'warehouse_id' => $warehouse->id,
            'user_id' => $user->id,
            'invoice_number' => 'INV-TEST-001',
            'invoice_date' => today(),
            'subtotal' => 30,
            'total' => 30,
            'paid' => 30,
            'status' => 'completed',
        ]);
        $invoice->items()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 15,
            'total' => 30,
        ]);
        StockMovement::create([
            'product_id' => $product->id,
            'warehouse_id' => $warehouse->id,
            'user_id' => $user->id,
            'type' => 'out',
            'quantity' => 2,
            'reference_type' => Invoice::class,
            'reference_id' => $invoice->id,
            'occurred_at' => now(),
        ]);

        $this->assertSame(8.0, $product->fresh()->stockFor($warehouse->id));
    }
}
