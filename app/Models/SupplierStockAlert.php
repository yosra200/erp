<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class SupplierStockAlert extends Model
{
    use HasFactory;
    protected $fillable = ['supplier_id', 'product_id', 'warehouse_id', 'current_stock', 'threshold_stock', 'status', 'notified_at', 'notes'];
    protected function casts(): array { return ['current_stock' => 'decimal:3', 'threshold_stock' => 'decimal:3', 'notified_at' => 'datetime']; }
    public function supplier(): BelongsTo { return $this->belongsTo(Supplier::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function warehouse(): BelongsTo { return $this->belongsTo(Warehouse::class); }
}
