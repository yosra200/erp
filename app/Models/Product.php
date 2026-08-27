<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'unit_id', 'name', 'sku', 'barcode', 'description', 'cost_price', 'sale_price', 'min_stock', 'is_active'];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'min_stock' => 'decimal:3',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            $product->sku ??= 'SKU-'.Str::upper(Str::random(8));
            $product->barcode ??= self::newBarcode();
        });
    }

    public static function newBarcode(): string
    {
        do {
            $barcode = '200'.str_pad((string) random_int(1, 999999999), 9, '0', STR_PAD_LEFT);
        } while (self::where('barcode', $barcode)->exists());

        return $barcode;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function stockFor(?int $warehouseId = null): float
    {
        $query = $this->stockMovements();
        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }

        return (float) $query->get()->sum(function (StockMovement $movement) {
            return match ($movement->type) {
                'in' => (float) $movement->quantity,
                'out' => -(float) $movement->quantity,
                default => (float) $movement->quantity,
            };
        });
    }

    public function getCurrentStockAttribute(): float
    {
        return $this->stockFor();
    }
}
