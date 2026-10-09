<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickingListItem extends Model
{
    protected $fillable = [
        'picking_list_id',
        'variant_id',
        'quantity',
        'picked_quantity',
        'location_code',
        'status',
        'picked_at',
        'verified_at',
    ];

    protected $casts = [
        'picked_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function pickingList()
    {
        return $this->belongsTo(PickingList::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePicked($query)
    {
        return $query->where('status', 'picked');
    }
}
