<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('التصنيف')->searchable()->sortable(),
            TextColumn::make('products_count')->label('عدد المنتجات')->counts('products')->sortable(),
            IconColumn::make('is_active')->label('الحالة')->boolean(),
            TextColumn::make('created_at')->label('تاريخ الإضافة')->dateTime('Y-m-d')->sortable(),
        ])->filters([])->recordActions([EditAction::make()])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
