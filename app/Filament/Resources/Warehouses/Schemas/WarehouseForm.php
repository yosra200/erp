<?php

namespace App\Filament\Resources\Warehouses\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WarehouseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('بيانات المخزن')->schema([
                TextInput::make('name')->label('اسم المخزن')->required()->maxLength(120),
                TextInput::make('code')->label('الكود')->required()->unique(ignoreRecord: true)->maxLength(30),
                TextInput::make('address')->label('العنوان')->maxLength(255)->columnSpanFull(),
                Toggle::make('is_active')->label('نشط')->default(true),
            ])->columns(2),
        ]);
    }
}
