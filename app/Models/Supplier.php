<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'company_name', 'phone', 'email', 'tax_number', 'address', 'is_active', 'user_id'];

    protected function casts(): array { return ['is_active' => 'boolean']; }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function products(): HasMany { return $this->hasMany(Product::class); }
    public function purchaseInvoices(): HasMany { return $this->hasMany(PurchaseInvoice::class); }
    public function alerts(): HasMany { return $this->hasMany(SupplierStockAlert::class); }
}
