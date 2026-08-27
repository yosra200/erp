<?php

namespace App\Filament\Resources\Units\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('بيانات الوحدة')->schema([
                TextInput::make('name')->label('اسم الوحدة')->required()->maxLength(80),
                TextInput::make('short_name')->label('الاختصار')->required()->maxLength(20),
                Toggle::make('is_active')->label('نشطة')->default(true),
            ])->columns(2),
        ]);
    }
}
