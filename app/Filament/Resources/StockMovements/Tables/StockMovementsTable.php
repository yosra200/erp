<?php

namespace App\Filament\Resources\StockMovements\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StockMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('occurred_at')->label('وقت الحركة')->dateTime('Y-m-d H:i')->sortable(),
            TextColumn::make('product.name')->label('المنتج')->searchable(),
            TextColumn::make('warehouse.name')->label('المخزن'),
            TextColumn::make('type')->label('النوع')->badge()->formatStateUsing(fn (string $state): string => match ($state) {
                'in' => 'وارد', 'out' => 'منصرف', 'adjustment' => 'تسوية', default => $state
            }),
            TextColumn::make('quantity')->label('الكمية')->numeric(decimalPlaces: 3),
            TextColumn::make('notes')->label('ملاحظات')->limit(35),
        ])->filters([])->recordActions([EditAction::make()])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
