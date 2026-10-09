<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    protected $fillable = [
        'return_id',
        'variant_id',
        'quantity',
        'condition',
        'qc_status',
        'restock',
        'notes',
    ];

    protected $casts = [
        'restock' => 'boolean',
    ];

    public function return()
    {
        return $this->belongsTo(ProductReturn::class, 'return_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}