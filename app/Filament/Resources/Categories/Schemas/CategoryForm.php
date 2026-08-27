<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('بيانات التصنيف')->schema([
                TextInput::make('name')->label('اسم التصنيف')->required()->maxLength(120),
                TextInput::make('slug')->label('الرابط المختصر')->maxLength(120),
                Textarea::make('description')->label('الوصف')->rows(3)->columnSpanFull(),
                Toggle::make('is_active')->label('نشط')->default(true),
            ])->columns(2),
        ]);
    }
}
