<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StockOpnameItem extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'stock_opname_id',
        'sku',
        'bin_code',
        'system_qty',
        'physical_qty',
        'difference',
        'notes',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($item) {
            if (empty($item->id)) {
                $item->id = (string) Str::uuid();
            }
        });
    }

    public function opname()
    {
        return $this->belongsTo(StockOpname::class, 'stock_opname_id');
    }

    // ✅ Relasi ke MasterSku
    public function masterSku()
    {
        return $this->belongsTo(MasterSku::class, 'sku', 'sku');
    }

    public function calculateDifference()
    {
        $this->difference = ($this->physical_qty ?? 0) - $this->system_qty;
    }
}
