<?php
namespace Tests\Feature;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use App\Notifications\LowStockNotification;
use App\Services\InventoryAlertService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
class InventoryAlertAndRolesTest extends TestCase
{
    use RefreshDatabase;
    public function test_supplier_user_receives_low_stock_notification(): void
    {
        Notification::fake();
        $supplier = Supplier::create(['name' => 'أشرف', 'is_active' => true]);
        $supplierUser = User::factory()->create(['role' => 'supplier', 'supplier_id' => $supplier->id]);
        $supplier->update(['user_id' => $supplierUser->id]);
        $warehouse = Warehouse::create(['name' => 'المخزن', 'code' => 'ALERT']);
        $product = Product::create(['name' => 'منتج تنبيه', 'supplier_id' => $supplier->id, 'cost_price' => 10, 'sale_price' => 20, 'min_stock' => 5, 'reorder_percent' => 50, 'is_active' => true]);
        StockMovement::create(['product_id' => $product->id, 'warehouse_id' => $warehouse->id, 'user_id' => $supplierUser->id, 'type' => 'in', 'quantity' => 4, 'occurred_at' => now()]);
        $alert = InventoryAlertService::check($product, $warehouse->id);
        $this->assertNotNull($alert);
        $this->assertSame('pending', $alert->status);
        Notification::assertSentTo($supplierUser, LowStockNotification::class);
    }
    public function test_user_roles_and_commission_are_saved(): void
    {
        $seller = User::factory()->create(['role' => 'seller', 'commission_rate' => 12.5]);
        $this->assertTrue($seller->isSeller());
        $this->assertSame('12.50', (string) $seller->commission_rate);
    }
}
