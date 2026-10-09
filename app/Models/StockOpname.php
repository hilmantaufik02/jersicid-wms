<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class StockOpname extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'opname_number',
        'warehouse_id',
        'status',
        'notes',
        'created_by',
        'approved_by',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($opname) {
            if (empty($opname->id)) {
                $opname->id = (string) Str::uuid();
            }
            if (empty($opname->opname_number)) {
                $opname->opname_number = 'OPN-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            }
        });
    }

    // ✅ Relasi ke Warehouse (meskipun bukan foreign key constraint)
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(StockOpnameItem::class, 'stock_opname_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'DRAFT' => 'bg-slate-500/20 text-slate-400',
            'IN_PROGRESS' => 'bg-amber-500/20 text-amber-400',
            'COMPLETED' => 'bg-emerald-500/20 text-emerald-400',
            'CANCELLED' => 'bg-red-500/20 text-red-400',
            default => 'bg-slate-500/20 text-slate-400',
        };
    }
}
