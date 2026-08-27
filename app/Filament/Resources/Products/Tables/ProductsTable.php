<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('المنتج')->searchable()->sortable(),
            TextColumn::make('barcode')->label('الباركود')->searchable()->copyable(),
            TextColumn::make('sku')->label('SKU')->searchable(),
            TextColumn::make('category.name')->label('التصنيف')->sortable(),
            TextColumn::make('cost_price')->label('التكلفة')->money('EGP'),
            TextColumn::make('sale_price')->label('سعر البيع')->money('EGP'),
            TextColumn::make('current_stock')->label('الرصيد الحالي')->state(fn (Product $record): string => number_format($record->current_stock, 3)),
            IconColumn::make('is_active')->label('متاح')->boolean(),
        ])->filters([
            Filter::make('low_stock')->label('مخزون منخفض')->query(fn (Builder $query): Builder => $query->whereHas('stockMovements')->whereColumn('min_stock', '>', 'min_stock')),
        ])->recordActions([EditAction::make()])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
