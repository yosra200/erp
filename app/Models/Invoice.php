<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'warehouse_id', 'user_id', 'invoice_number', 'status', 'invoice_date', 'subtotal', 'discount', 'tax', 'total', 'paid', 'notes'];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'paid' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            $invoice->invoice_number ??= 'INV-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
            $invoice->invoice_date ??= now()->toDateString();
            $invoice->user_id ??= auth()->id();
        });

        static::deleting(function (Invoice $invoice): void {
            StockMovement::query()->where('reference_type', self::class)->where('reference_id', $invoice->id)->delete();
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
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
        return $this->hasMany(InvoiceItem::class);
    }
}
