<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockRequest extends Model
{
    protected $fillable = [
        'product_id',
        'variant_id',
        'request_type',           // NEW: restock atau new_product
        'product_name',           // NEW: untuk produk baru
        'mockup_file',            // NEW: file mockup
        'sample_notes',           // NEW: catatan sample
        'sample_approved',        // NEW: sample disetujui?
        'sample_approved_at',     // NEW
        'sample_approved_by',     // NEW
        'production_status',      // NEW: status produksi
        'requested_by',
        'approved_by',
        'quantity_requested',
        'status',
        'notes',
        'approved_at',
        'sublimation_done_at',
        'konveksi_done_at',
        'completed_at',
    ];

    protected $casts = [
        'sample_approved' => 'boolean',
        'approved_at' => 'datetime',
        'sample_approved_at' => 'datetime',
        'sublimation_done_at' => 'datetime',
        'konveksi_done_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function sampleApprovedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sample_approved_by');
    }

    // Status badge
    public function getStatusBadgeAttribute(): array
    {
        $badges = [
            'pending' => ['bg-amber-500/10 text-amber-400 border border-amber-500/20', 'Sedang Diajukan'],
            'approved' => ['bg-blue-500/10 text-blue-400 border border-blue-500/20', 'Disetujui - Ke Sublim'],
            'sample_created' => ['bg-purple-500/10 text-purple-400 border border-purple-500/20', 'Sample Dibuat'],
            'sample_approved' => ['bg-emerald-500/10 text-emerald-400 border border-emerald-500/20', 'Sample Disetujui'],
            'in_production' => ['bg-indigo-500/10 text-indigo-400 border border-indigo-500/20', 'Dalam Produksi'],
            'sublimation_done' => ['bg-cyan-500/10 text-cyan-400 border border-cyan-500/20', 'Selesai Sublim'],
            'konveksi_done' => ['bg-pink-500/10 text-pink-400 border border-pink-500/20', 'Selesai Konveksi'],
            'production_done' => ['bg-green-500/10 text-green-400 border border-green-500/20', 'Produksi Selesai'],
            'done' => ['bg-emerald-500/10 text-emerald-400 border border-emerald-500/20', 'Done di Gudang'],
        ];

        return $badges[$this->status] ?? $badges['pending'];
    }

    // Request type badge
    public function getRequestTypeBadgeAttribute(): array
    {
        if ($this->request_type === 'new_product') {
            return ['bg-purple-500/10 text-purple-400 border border-purple-500/20', 'Produk Baru'];
        }

        return ['bg-blue-500/10 text-blue-400 border border-blue-500/20', 'Restock'];
    }

    public function getNextStatusAttribute(): ?string
    {
        $transitions = [
            'pending' => 'approved',
            'approved' => 'sample_created',
            'sample_created' => 'sample_approved',
            'sample_approved' => 'in_production',
            'in_production' => 'sublimation_done',
            'sublimation_done' => 'konveksi_done',
            'konveksi_done' => 'production_done',
            'production_done' => 'done',
        ];

        return $transitions[$this->status] ?? null;
    }
}
