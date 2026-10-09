<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BarcodeScan extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'sku',
        'bin_code',
        'scan_type',
        'qty',
        'reference_no',
        'status',
        'message',
        'notes',
        'scanner_device',
        'ip_address'
    ];

    protected $casts = [
        'qty' => 'integer',
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function masterSku()
    {
        return $this->belongsTo(MasterSku::class, 'sku', 'sku');
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'SUCCESS' => 'bg-emerald-500/20 text-emerald-400',
            'FAILED' => 'bg-red-500/20 text-red-400',
            'WARNING' => 'bg-amber-500/20 text-amber-400',
            default => 'bg-slate-500/20 text-slate-400',
        };
    }

    public function getScanTypeColorAttribute()
    {
        return match ($this->scan_type) {
            'INBOUND' => 'bg-blue-500/20 text-blue-400',
            'OUTBOUND' => 'bg-purple-500/20 text-purple-400',
            'PICKING' => 'bg-amber-500/20 text-amber-400',
            'PACKING' => 'bg-cyan-500/20 text-cyan-400',
            'OPNAME' => 'bg-pink-500/20 text-pink-400',
            'TRANSFER' => 'bg-indigo-500/20 text-indigo-400',
            'QC_CHECK' => 'bg-emerald-500/20 text-emerald-400',
            'RETURN_IN' => 'bg-orange-500/20 text-orange-400',
            'RETURN_OUT' => 'bg-red-500/20 text-red-400',
            default => 'bg-slate-500/20 text-slate-400',
        };
    }
}
