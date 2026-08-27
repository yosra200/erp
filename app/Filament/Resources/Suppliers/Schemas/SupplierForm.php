<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('بيانات المورد')->schema([
                TextInput::make('name')->label('اسم المسؤول')->required()->maxLength(120),
                TextInput::make('company_name')->label('اسم الشركة')->maxLength(160),
                TextInput::make('phone')->label('الهاتف')->tel()->maxLength(40),
                TextInput::make('email')->label('البريد الإلكتروني')->email()->maxLength(160),
                TextInput::make('tax_number')->label('الرقم الضريبي')->maxLength(80),
                TextInput::make('address')->label('العنوان')->maxLength(255),
                Toggle::make('is_active')->label('نشط')->default(true),
            ])->columns(2),
        ]);
    }
}
