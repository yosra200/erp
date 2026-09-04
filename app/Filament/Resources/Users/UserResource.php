<?php
namespace App\Filament\Resources\Users;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;
class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;
    protected static ?string $navigationLabel = 'حسابات المستخدمين';
    protected static ?string $modelLabel = 'مستخدم';
    protected static ?string $pluralModelLabel = 'حسابات المستخدمين';
    protected static string|UnitEnum|null $navigationGroup = 'الإعدادات';
    public static function form(Schema $schema): Schema { return $schema->components([Section::make('بيانات الحساب')->schema([
        TextInput::make('name')->label('الاسم')->required(),
        TextInput::make('email')->label('البريد الإلكتروني')->email()->required(),
        TextInput::make('password')->label('كلمة المرور')->password()->revealable()->required(fn (string $operation): bool => $operation === 'create')->dehydrated(fn ($state): bool => filled($state)),
        Select::make('role')->label('نوع الحساب')->options(['admin' => 'مدير', 'seller' => 'بائع', 'supplier' => 'مورد'])->required()->default('seller'),
        Select::make('supplier_id')->label('المورد المرتبط')->relationship('supplier', 'name')->searchable()->preload()->visible(fn ($get): bool => $get('role') === 'supplier'),
        TextInput::make('commission_rate')->label('نسبة العمولة %')->numeric()->minValue(0)->maxValue(100)->default(0),
        Toggle::make('is_active')->label('الحساب نشط')->default(true),
    ])->columns(2)]); }
    public static function table(Table $table): Table { return $table->columns([
        TextColumn::make('name')->label('الاسم')->searchable()->sortable(),
        TextColumn::make('email')->label('البريد')->searchable(),
        TextColumn::make('role')->label('الدور')->badge()->formatStateUsing(fn (string $state): string => ['admin' => 'مدير', 'seller' => 'بائع', 'supplier' => 'مورد'][$state] ?? $state),
        TextColumn::make('commission_rate')->label('العمولة')->suffix('%'),
        IconColumn::make('is_active')->label('نشط')->boolean(),
    ])->recordActions([\Filament\Actions\EditAction::make()]); }
    public static function getRelations(): array { return []; }
    public static function getPages(): array { return ['index' => ListUsers::route('/'), 'create' => CreateUser::route('/create'), 'edit' => EditUser::route('/{record}/edit')]; }
}
