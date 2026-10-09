<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductReturn extends Model
{
    protected $table = 'returns';

    protected $fillable = [
        'return_number',
        'transaction_id',
        'customer_name',
        'customer_phone',
        'reason',
        'status',
        'refund_amount',
        'refund_method',
        'inspected_by',
        'inspected_at',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'refund_amount' => 'decimal:2',
        'inspected_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($return) {
            if (empty($return->return_number)) {
                $return->return_number = 'RET-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function inspectedBy()
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items()
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
