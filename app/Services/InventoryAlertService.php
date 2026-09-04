<?php
namespace App\Services;
use App\Models\Product;
use App\Models\SupplierStockAlert;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LowStockNotification;
class InventoryAlertService
{
    public static function check(Product $product, int $warehouseId): ?SupplierStockAlert
    {
        if (! $product->supplier_id) return null;
        $stock = $product->stockFor($warehouseId);
        $threshold = (float) $product->min_stock;
        if ($threshold <= 0) return null;
        if ($stock > $threshold) return null;
        $alert = SupplierStockAlert::firstOrCreate([
            'supplier_id' => $product->supplier_id,
            'product_id' => $product->id,
            'warehouse_id' => $warehouseId,
            'status' => 'pending',
        ], [
            'current_stock' => $stock,
            'threshold_stock' => $threshold,
            'notified_at' => now(),
        ]);
        $alert->update(['current_stock' => $stock, 'threshold_stock' => $threshold, 'notified_at' => $alert->notified_at ?? now()]);
        if ($alert->wasRecentlyCreated && $alert->supplier?->user) {
            $alert->supplier->user->notify(new LowStockNotification($alert));
        }
        return $alert;
    }
}
