<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StockActive extends Model
{
    protected $table = 'stock_actives';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'sku',
        'bin_code',
        'qty',
        'reserved_qty',
        'received_at',
        'last_movement_at',
        'batch_no',
    ];

    protected $casts = [
        'qty' => 'integer',
        'reserved_qty' => 'integer',
        'received_at' => 'datetime',
        'last_movement_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function masterSku()
    {
        return $this->belongsTo(MasterSku::class, 'sku', 'sku');
    }
}
