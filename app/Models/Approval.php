<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    protected $fillable = [
        'reference_type',
        'reference_id',
        'requested_by',
        'approved_by',
        'status',
        'approval_type',
        'reason',
        'rejection_reason',
    ];

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function approve($userId, $reason = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'reason' => $reason,
        ]);
    }

    public function reject($userId, $reason)
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $userId,
            'rejection_reason' => $reason,
        ]);
    }
}
