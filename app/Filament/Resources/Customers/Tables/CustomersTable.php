<?php

namespace App\Filament\Resources\Customers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('العميل')->searchable()->sortable(),
            TextColumn::make('phone')->label('الهاتف')->searchable(),
            TextColumn::make('email')->label('البريد الإلكتروني'),
            TextColumn::make('credit_limit')->label('حد الائتمان')->money('EGP'),
            IconColumn::make('is_active')->label('نشط')->boolean(),
        ])->filters([])->recordActions([EditAction::make()])->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
