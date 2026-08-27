<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PurchaseInvoice extends Model
{
    use HasFactory;

    protected $fillable = ['supplier_id', 'warehouse_id', 'user_id', 'purchase_number', 'status', 'purchase_date', 'subtotal', 'discount', 'tax', 'total', 'paid', 'notes'];

    protected function casts(): array
    {
        return ['purchase_date' => 'date', 'subtotal' => 'decimal:2', 'discount' => 'decimal:2', 'tax' => 'decimal:2', 'total' => 'decimal:2', 'paid' => 'decimal:2'];
    }

    protected static function booted(): void
    {
        static::creating(function (PurchaseInvoice $invoice) {
            $invoice->purchase_number ??= 'PUR-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
            $invoice->purchase_date ??= now()->toDateString();
            $invoice->user_id ??= auth()->id();
        });

        static::deleting(function (PurchaseInvoice $invoice): void {
            StockMovement::query()->where('reference_type', self::class)->where('reference_id', $invoice->id)->delete();
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseInvoiceItem::class);
    }
}
