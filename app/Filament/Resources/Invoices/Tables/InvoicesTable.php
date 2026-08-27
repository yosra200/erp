<?php

namespace App\Filament\Resources\Invoices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('invoice_number')->label('رقم الفاتورة')->searchable()->sortable()->copyable(),
            TextColumn::make('invoice_date')->label('التاريخ')->date('Y-m-d')->sortable(),
            TextColumn::make('customer.name')->label('العميل')->placeholder('عميل نقدي'),
            TextColumn::make('warehouse.name')->label('المخزن'),
            TextColumn::make('total')->label('الإجمالي')->money('EGP')->sortable(),
            TextColumn::make('paid')->label('المدفوع')->money('EGP'),
            TextColumn::make('status')->label('الحالة')->badge()->formatStateUsing(fn (string $state): string => match ($state) {
                'completed' => 'مكتملة', 'cancelled' => 'ملغاة', default => $state
            }),
        ])->filters([
            SelectFilter::make('status')->label('الحالة')->options(['completed' => 'مكتملة', 'cancelled' => 'ملغاة']),
        ])->recordActions([])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
