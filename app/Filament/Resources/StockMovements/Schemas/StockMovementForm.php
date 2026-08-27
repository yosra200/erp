<?php

namespace App\Filament\Resources\StockMovements\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StockMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('حركة المخزون')->description('استخدم الوارد للإضافة، والمنصرف للبيع أو السحب، والتسوية لتسجيل فرق موجب أو سالب.')->schema([
                Select::make('product_id')->label('المنتج')->relationship('product', 'name')->searchable()->preload()->required(),
                Select::make('warehouse_id')->label('المخزن')->relationship('warehouse', 'name')->searchable()->preload()->required(),
                Select::make('type')->label('نوع الحركة')->options(['in' => 'وارد / إضافة', 'out' => 'منصرف / خصم', 'adjustment' => 'تسوية'])->required()->native(false),
                TextInput::make('quantity')->label('الكمية')->numeric()->required()->helperText('في التسوية يمكن إدخال كمية سالبة.'),
                DateTimePicker::make('occurred_at')->label('وقت الحركة')->default(now())->required(),
                TextInput::make('notes')->label('ملاحظات')->maxLength(255)->columnSpanFull(),
            ])->columns(2),
        ]);
    }
}
