<?php

namespace App\Filament\Resources\Suppliers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SuppliersTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('المورد')->searchable()->sortable(),
            TextColumn::make('company_name')->label('الشركة')->searchable(),
            TextColumn::make('phone')->label('الهاتف'),
            TextColumn::make('email')->label('البريد الإلكتروني'),
            IconColumn::make('is_active')->label('نشط')->boolean(),
        ])->filters([])->recordActions([EditAction::make()])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
