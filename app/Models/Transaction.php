<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'invoice_number',
        'type',
        'status',
        'customer_name',
        'customer_phone',
        'customer_email',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount_amount',
        'discount_reason',
        'total',
        'payment_method',
        'payment_status',
        'paid_amount',
        'change_amount',
        'cashier_id',
        'warehouse_id',
        'notes',
        'qr_code_url',
        'completed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'completed_at' => 'datetime',
    ];

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function returns()
    {
        return $this->hasMany(ProductReturn::class, 'transaction_id');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', date('Y-m-d'));
    }
}
