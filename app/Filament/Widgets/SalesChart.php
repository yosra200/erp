<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Widgets\ChartWidget;

class SalesChart extends ChartWidget
{
    protected ?string $heading = 'مبيعات آخر 7 أيام';

    protected static ?int $sort = 2;

    protected ?string $pollingInterval = null;

    protected function getData(): array
    {
        $dates = collect(range(6, 0))->map(fn (int $days): string => now()->subDays($days)->toDateString());
        $sales = Invoice::query()->where('status', 'completed')->whereBetween('invoice_date', [$dates->first(), $dates->last()])->get()->groupBy(fn (Invoice $invoice): string => $invoice->invoice_date->toDateString());

        return [
            'datasets' => [[
                'label' => 'المبيعات بالجنيه المصري',
                'data' => $dates->map(fn (string $date): float => (float) ($sales->get($date)?->sum('total') ?? 0))->all(),
                'borderColor' => '#2563eb',
                'backgroundColor' => 'rgba(37, 99, 235, 0.15)',
                'fill' => true,
            ]],
            'labels' => $dates->map(fn (string $date): string => date('d/m', strtotime($date)))->all(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
