<?php

namespace App\Filament\Resources\Units\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UnitsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('الوحدة')->searchable()->sortable(),
            TextColumn::make('short_name')->label('الاختصار'),
            TextColumn::make('products_count')->label('المنتجات')->counts('products'),
            IconColumn::make('is_active')->label('نشطة')->boolean(),
        ])->filters([])->recordActions([EditAction::make()])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
