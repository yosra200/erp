<?php
namespace App\Filament\Resources\DistributionRules;
use App\Models\DistributionRule;
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
class DistributionRuleResource extends Resource
{
    protected static ?string $model = DistributionRule::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;
    protected static ?string $navigationLabel = 'نسب التوزيع';
    protected static ?string $modelLabel = 'قاعدة توزيع';
    protected static ?string $pluralModelLabel = 'نسب التوزيع';
    protected static string|UnitEnum|null $navigationGroup = 'الإعدادات';
    public static function form(Schema $schema): Schema { return $schema->components([Section::make('قاعدة التوزيع')->schema([
        TextInput::make('name')->label('الاسم')->required(),
        Select::make('type')->label('نوع القيمة')->options(['percentage' => 'نسبة مئوية', 'fixed' => 'مبلغ ثابت'])->required()->default('percentage'),
        TextInput::make('value')->label('القيمة')->numeric()->required()->minValue(0),
        Select::make('beneficiary_id')->label('المستفيد')->relationship('beneficiary', 'name')->searchable()->preload(),
        Select::make('product_id')->label('الصنف المحدد - اختياري')->relationship('product', 'name')->searchable()->preload(),
        Toggle::make('is_active')->label('نشطة')->default(true),
    ])->columns(2)]); }
    public static function table(Table $table): Table { return $table->columns([
        TextColumn::make('name')->label('القاعدة')->searchable(),
        TextColumn::make('type')->label('النوع')->formatStateUsing(fn (string $state): string => $state === 'percentage' ? 'نسبة مئوية' : 'مبلغ ثابت'),
        TextColumn::make('value')->label('القيمة'),
        TextColumn::make('beneficiary.name')->label('المستفيد')->placeholder('عام'),
        IconColumn::make('is_active')->label('نشطة')->boolean(),
    ])->recordActions([\Filament\Actions\EditAction::make()]); }
    public static function getRelations(): array { return []; }
    public static function getPages(): array { return [
        'index' => Pages\ListDistributionRules::route('/'),
        'create' => Pages\CreateDistributionRule::route('/create'),
        'edit' => Pages\EditDistributionRule::route('/{record}/edit'),
    ]; }
}
