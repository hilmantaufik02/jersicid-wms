<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'tracking_number',
        'courier_name',
        'courier_service',
        'marketplace',
        'status',
        'customer_name',
        'customer_phone',
        'customer_address',
        'city',
        'province',
        'postal_code',
        'subtotal',
        'shipping_cost',
        'discount',
        'total',
        'picked_by',
        'picked_at',
        'packed_by',
        'packed_at',
        'qc_verified_by',
        'qc_verified_at',
        'shipped_at',
        'notes',
        'marketplace_data',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'picked_at' => 'datetime',
        'packed_at' => 'datetime',
        'qc_verified_at' => 'datetime',
        'shipped_at' => 'datetime',
        'marketplace_data' => 'array',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function pickedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'picked_by');
    }

    public function packedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'packed_by');
    }

    public function qcVerifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'qc_verified_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePicking($query)
    {
        return $query->where('status', 'picking');
    }

    public function scopeReadyPacking($query)
    {
        return $query->where('status', 'ready_packing');
    }

    public function scopeReadyPickup($query)
    {
        return $query->where('status', 'ready_pickup');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // Helper methods
    public function getTotalItemsAttribute()
    {
        return $this->items->sum('quantity');
    }

    public function getPickedItemsAttribute()
    {
        return $this->items->sum('picked_quantity');
    }

    public function getProgressPercentageAttribute()
    {
        $total = $this->total_items;
        if ($total === 0) return 0;
        return round(($this->picked_items / $total) * 100, 2);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => ['bg-gray-500/10 text-gray-400 border-gray-500/20', 'Belum Diproses'],
            'picking' => ['bg-blue-500/10 text-blue-400 border-blue-500/20', 'Sedang Picking'],
            'ready_packing' => ['bg-amber-500/10 text-amber-400 border-amber-500/20', 'Siap Packing'],
            'packing' => ['bg-purple-500/10 text-purple-400 border-purple-500/20', 'Sedang Packing'],
            'ready_pickup' => ['bg-emerald-500/10 text-emerald-400 border-emerald-500/20', 'Siap Pickup'],
            'shipped' => ['bg-indigo-500/10 text-indigo-400 border-indigo-500/20', 'Sudah Dikirim'],
            'delivered' => ['bg-green-500/10 text-green-400 border-green-500/20', 'Sudah Sampai'],
            'cancelled' => ['bg-red-500/10 text-red-400 border-red-500/20', 'Dibatalkan'],
            'returned' => ['bg-orange-500/10 text-orange-400 border-orange-500/20', 'Diretur'],
            'qc_review' => ['bg-purple-500/10 text-purple-400 border-purple-500/20', 'Menunggu QC'],
        ];

        return $badges[$this->status] ?? $badges['pending'];
    }

    public function pickingSession()
    {
        return $this->hasOne(PickingSession::class, 'order_id');
    }

    public function packingSession()
    {
        return $this->hasOne(PackingSession::class, 'order_id');
    }
}
