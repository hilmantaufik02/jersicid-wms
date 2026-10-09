<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WarehouseRack extends Model
{
    // ✅ FIX: Konfigurasi untuk UUID primary key
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'code',
        'aisle_number',
        'color',
        'rows_count',
        'position_x',
        'position_y',
        'is_top_row',
        'status'
    ];

    protected $casts = [
        'is_top_row' => 'boolean',
        'position_x' => 'integer',
        'position_y' => 'integer',
        'rows_count' => 'integer',
        'aisle_number' => 'integer',
    ];

    // ✅ FIX: Auto-generate UUID saat create
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Relasi many-to-many ke MasterSku
     */
    public function products()
    {
        return $this->belongsToMany(MasterSku::class, 'rack_products', 'warehouse_rack_id', 'sku')
            ->withPivot('current_qty', 'max_capacity', 'bin_location')
            ->withTimestamps();
    }

    /**
     * Relasi one-to-many ke RackProduct (pivot model)
     */
    public function rackProductMappings()
    {
        return $this->hasMany(RackProduct::class, 'warehouse_rack_id', 'id');
    }

    /**
     * Hitung total produk di rak
     */
    public function productsCount()
    {
        return $this->products()->count();
    }
}
