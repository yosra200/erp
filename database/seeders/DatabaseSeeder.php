<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(['email' => 'admin@example.com'], ['name' => 'مدير النظام', 'password' => 'password']);
        $unit = Unit::firstOrCreate(['short_name' => 'قطعة'], ['name' => 'قطعة', 'is_active' => true]);
        $category = Category::firstOrCreate(['slug' => 'عام'], ['name' => 'عام', 'is_active' => true]);
        $warehouse = Warehouse::firstOrCreate(['code' => 'MAIN'], ['name' => 'المخزن الرئيسي', 'address' => 'المقر الرئيسي', 'is_active' => true]);
        Customer::firstOrCreate(['name' => 'عميل نقدي'], ['phone' => null, 'is_active' => true]);

        $products = [
            ['name' => 'منتج تجريبي 1', 'cost_price' => 80, 'sale_price' => 120, 'min_stock' => 5],
            ['name' => 'منتج تجريبي 2', 'cost_price' => 150, 'sale_price' => 220, 'min_stock' => 3],
        ];

        foreach ($products as $data) {
            $product = Product::firstOrCreate(['name' => $data['name']], array_merge($data, ['category_id' => $category->id, 'unit_id' => $unit->id, 'is_active' => true]));
            if (! $product->stockMovements()->exists()) {
                StockMovement::create(['product_id' => $product->id, 'warehouse_id' => $warehouse->id, 'user_id' => $admin->id, 'type' => 'in', 'quantity' => 25, 'notes' => 'رصيد افتتاحي تجريبي', 'occurred_at' => now()]);
            }
        }
    }
}
