<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class DistributionRule extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'type', 'value', 'beneficiary_type', 'beneficiary_id', 'product_id', 'is_active'];
    protected function casts(): array { return ['value' => 'decimal:4', 'is_active' => 'boolean']; }
    public function beneficiary(): BelongsTo { return $this->belongsTo(User::class, 'beneficiary_id'); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function amount(float $base): float { return $this->type === 'percentage' ? $base * ((float) $this->value / 100) : (float) $this->value; }
}
