<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RackProductMapping extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'rack_id',
        'sku',
        'qty',
        'max_capacity',
        'is_active',
        'user_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'qty' => 'integer',
        'max_capacity' => 'integer',
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

    public function rack()
    {
        return $this->belongsTo(WarehouseRack::class, 'rack_id');
    }

    public function masterSku()
    {
        return $this->belongsTo(MasterSku::class, 'sku', 'sku');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getUtilizationPercentageAttribute()
    {
        if ($this->max_capacity == 0) return 0;
        return round(($this->qty / $this->max_capacity) * 100, 2);
    }
}
