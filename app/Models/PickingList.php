<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickingList extends Model
{
    protected $fillable = [
        'reference_type',
        'reference_id',
        'warehouse_id',
        'assigned_to',
        'status',
        'priority',
        'started_at',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function items()
    {
        return $this->hasMany(PickingListItem::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function getProgressAttribute()
    {
        $total = $this->items->sum('quantity');
        $picked = $this->items->sum('picked_quantity');
        return $total > 0 ? round(($picked / $total) * 100, 2) : 0;
    }
}
