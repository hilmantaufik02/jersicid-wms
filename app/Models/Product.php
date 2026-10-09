<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    // Hapus 'use SoftDeletes;' dari sini

    protected $fillable = [
        'sku',
        'name',
        'slug',
        'description',
        'category_id',
        'supplier_id',
        'brand',
        'season',
        'year',
        'base_price',
        'cost_price',
        'image',
        'images',
        'weight_grams',
        'is_active',
        'is_featured',
        'tags',
    ];

    protected $casts = [
        'images' => 'array',
        'tags' => 'array',
        'base_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'year' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function transactionItems()
    {
        return $this->hasManyThrough(TransactionItem::class, ProductVariant::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get total stock from all variants
     */
    public function getTotalStockAttribute(): int
    {
        return $this->variants->sum('stock');
    }

    /**
     * Get short SKU format (without color)
     */
    public function getShortSkuAttribute(): string
    {
        return $this->sku;
    }
}
