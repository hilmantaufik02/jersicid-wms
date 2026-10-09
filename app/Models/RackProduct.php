<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RackProduct extends Model
{
    protected $table = 'rack_products';

    protected $fillable = [
        'warehouse_rack_id',
        'sku',
        'current_qty',
        'max_capacity',
        'bin_location'
    ];

    protected $casts = [
        'current_qty' => 'integer',
        'max_capacity' => 'integer',
    ];

    public function rack()
    {
        return $this->belongsTo(WarehouseRack::class, 'warehouse_rack_id', 'id');
    }

    public function masterSku()
    {
        return $this->belongsTo(MasterSku::class, 'sku', 'sku');
    }
}
