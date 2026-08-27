<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'is_active'];

    protected static function booted(): void
    {
        static::creating(function (Category $category) {
            $category->slug ??= Str::slug($category->name) ?: Str::random(8);
        });
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
