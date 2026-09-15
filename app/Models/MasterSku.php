<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterSku extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'sku', 'article', 'product_name', 'version', 'sub_version', 
        'size_category', 'size', 'size_token', 'price', 
        'standard_weight_gram', 'min_stock', 'status'
    ];

    protected $casts = [
        'price' => 'integer',
        'standard_weight_gram' => 'integer',
        'min_stock' => 'integer',
    ];

    /**
     * Relasi ke StockActive menggunakan kolom 'sku' sebagai key, bukan 'id'.
     */
    public function stockActives(): HasMany
    {
        return $this->hasMany(StockActive::class, 'sku', 'sku');
    }

    /**
     * Scope untuk hanya mengambil SKU yang aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'AKTIF');
    }
}