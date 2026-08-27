<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ErpStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $todaySales = Invoice::whereDate('invoice_date', today())->where('status', 'completed')->sum('total');
        $lowStock = Product::all()->filter(fn (Product $product): bool => $product->current_stock <= (float) $product->min_stock)->count();
        $stockValue = Product::all()->sum(fn (Product $product): float => $product->current_stock * (float) $product->cost_price);

        return [
            Stat::make('مبيعات اليوم', number_format($todaySales, 2).' ج.م')->description('إجمالي فواتير البيع المكتملة')->color('success'),
            Stat::make('عدد المنتجات', Product::where('is_active', true)->count())->description('المنتجات المتاحة للبيع')->color('primary'),
            Stat::make('قيمة المخزون', number_format($stockValue, 2).' ج.م')->description('بحسب تكلفة الشراء')->color('info'),
            Stat::make('تنبيهات المخزون', $lowStock)->description('منتجات عند حد إعادة الطلب أو أقل')->color($lowStock > 0 ? 'danger' : 'success'),
        ];
    }
}
