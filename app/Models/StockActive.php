<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockActive extends Model
{
    use HasUuids;

    // Karena menggunakan UUID
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'sku', 'bin_code', 'qty', 'reserved_qty', 
        'batch_no', 'received_at', 'last_movement_at'
    ];

    protected $casts = [
        'qty' => 'integer',
        'reserved_qty' => 'integer',
        'received_at' => 'datetime',
        'last_movement_at' => 'datetime',
    ];

    /**
     * Relasi balik ke MasterSku.
     */
    public function masterSku(): BelongsTo
    {
        return $this->belongsTo(MasterSku::class, 'sku', 'sku');
    }

    /**
     * Accessor untuk menghitung stok yang benar-benar tersedia (bukan reserved).
     */
    public function getAvailableQtyAttribute(): int
    {
        return $this->qty - $this->reserved_qty;
    }
}
