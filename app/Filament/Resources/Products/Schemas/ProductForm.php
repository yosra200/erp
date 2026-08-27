<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('بيانات المنتج')->schema([
                TextInput::make('name')->label('اسم المنتج')->required()->maxLength(180),
                TextInput::make('sku')->label('كود المنتج SKU')->unique(ignoreRecord: true)->maxLength(80),
                TextInput::make('barcode')->label('الباركود')->unique(ignoreRecord: true)->maxLength(80)->helperText('يتم توليد باركود داخلي تلقائيًا إذا تُرك فارغًا.'),
                Select::make('category_id')->label('التصنيف')->relationship('category', 'name')->searchable()->preload(),
                Select::make('unit_id')->label('الوحدة')->relationship('unit', 'name')->searchable()->preload(),
                Toggle::make('is_active')->label('متاح للبيع')->default(true),
                Textarea::make('description')->label('الوصف')->rows(3)->columnSpanFull(),
            ])->columns(2),
            Section::make('التسعير والمخزون')->schema([
                TextInput::make('cost_price')->label('تكلفة الشراء')->numeric()->default(0)->prefix('ج.م')->required(),
                TextInput::make('sale_price')->label('سعر البيع')->numeric()->default(0)->prefix('ج.م')->required(),
                TextInput::make('min_stock')->label('حد إعادة الطلب')->numeric()->default(0)->minValue(0),
            ])->columns(3),
        ]);
    }
}
