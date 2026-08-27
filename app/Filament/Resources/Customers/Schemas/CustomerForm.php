<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('بيانات العميل')->schema([
                TextInput::make('name')->label('اسم العميل')->required()->maxLength(120),
                TextInput::make('phone')->label('الهاتف')->tel()->maxLength(40),
                TextInput::make('email')->label('البريد الإلكتروني')->email()->maxLength(160),
                TextInput::make('tax_number')->label('الرقم الضريبي')->maxLength(80),
                TextInput::make('credit_limit')->label('حد الائتمان')->numeric()->default(0)->prefix('ج.م'),
                Toggle::make('is_active')->label('نشط')->default(true),
                Textarea::make('address')->label('العنوان')->rows(2)->columnSpanFull(),
            ])->columns(2),
        ]);
    }
}
