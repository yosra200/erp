<?php

namespace App\Filament\Resources\PurchaseInvoices\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PurchaseInvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('purchase_number')->label('رقم فاتورة الشراء')->searchable()->sortable()->copyable(),
            TextColumn::make('purchase_date')->label('التاريخ')->date('Y-m-d')->sortable(),
            TextColumn::make('supplier.name')->label('المورد')->placeholder('بدون مورد'),
            TextColumn::make('warehouse.name')->label('المخزن'),
            TextColumn::make('total')->label('الإجمالي')->money('EGP')->sortable(),
            TextColumn::make('paid')->label('المدفوع')->money('EGP'),
            TextColumn::make('status')->label('الحالة')->badge()->formatStateUsing(fn (string $state): string => match ($state) {
                'received' => 'مستلمة', 'cancelled' => 'ملغاة', default => $state
            }),
        ])->filters([
            SelectFilter::make('status')->label('الحالة')->options(['received' => 'مستلمة', 'cancelled' => 'ملغاة']),
        ])->recordActions([
            Action::make('print')
                ->label('طباعة')
                ->icon('heroicon-o-printer')
                ->url(fn ($record): string => route('purchase-invoices.print', $record))
                ->openUrlInNewTab(),
            Action::make('pdf')
                ->label('تحميل PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn ($record): string => route('purchase-invoices.pdf', $record))
                ->openUrlInNewTab(),
        ])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
