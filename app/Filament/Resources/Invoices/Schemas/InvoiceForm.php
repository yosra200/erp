<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Models\Product;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('بيانات الفاتورة')->schema([
                DatePicker::make('invoice_date')->label('تاريخ الفاتورة')->default(now())->required(),
                Select::make('warehouse_id')->label('المخزن')->relationship('warehouse', 'name')->required()->searchable()->preload(),
                Select::make('customer_id')->label('العميل')->relationship('customer', 'name')->searchable()->preload(),
                Textarea::make('notes')->label('ملاحظات')->rows(2)->columnSpanFull(),
            ])->columns(3),
            Section::make('أصناف الفاتورة')->schema([
                Repeater::make('items')->label('الأصناف')->required()->minItems(1)->defaultItems(1)->schema([
                    Select::make('product_id')->label('المنتج')->options(fn (): array => Product::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all())->searchable()->preload()->live()->required()->afterStateUpdated(function ($state, Set $set): void {
                        if ($state) {
                            $set('unit_price', Product::find($state)?->sale_price ?? 0);
                        }
                    })->columnSpan(4),
                    TextInput::make('quantity')->label('الكمية')->numeric()->minValue(0.001)->default(1)->required()->columnSpan(2),
                    TextInput::make('unit_price')->label('سعر الوحدة')->numeric()->minValue(0)->required()->columnSpan(2),
                    TextInput::make('discount')->label('خصم الصنف')->numeric()->minValue(0)->default(0)->columnSpan(2),
                ])->columns(10)->columnSpanFull(),
            ]),
            Section::make('الإجماليات')->schema([
                TextInput::make('discount')->label('الخصم العام')->numeric()->minValue(0)->default(0),
                TextInput::make('tax')->label('الضريبة / رسوم إضافية')->numeric()->minValue(0)->default(0),
                TextInput::make('paid')->label('المدفوع')->numeric()->minValue(0)->default(0),
            ])->columns(3),
        ]);
    }
}
