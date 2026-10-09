<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InboundReceipt extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'receipt_number',
        'purchase_order_id',
        'supplier_name',
        'source_type',
        'receipt_date',
        'status',
        'received_by',
        'notes',
        'vehicle_number',
        'driver_name',
    ];

    protected $casts = [
        'receipt_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(InboundReceiptItem::class, 'inbound_receipt_id');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'DRAFT' => 'bg-slate-500/20 text-slate-400',
            'PROCESSING' => 'bg-amber-500/20 text-amber-400',
            'COMPLETED' => 'bg-emerald-500/20 text-emerald-400',
            'CANCELLED' => 'bg-red-500/20 text-red-400',
            default => 'bg-slate-500/20 text-slate-400',
        };
    }
}
