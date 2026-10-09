<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPerformance extends Model
{
    // Tambahkan baris ini
    protected $table = 'product_performance';

    protected $fillable = [
        'variant_id',
        'period_type',
        'period_start',
        'period_end',
        'quantity_sold',
        'revenue',
        'profit',
        'avg_daily_sales',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'revenue' => 'decimal:2',
        'profit' => 'decimal:2',
        'avg_daily_sales' => 'decimal:2',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function scopeDaily($query)
    {
        return $query->where('period_type', 'daily');
    }

    public function scopeWeekly($query)
    {
        return $query->where('period_type', 'weekly');
    }

    public function scopeMonthly($query)
    {
        return $query->where('period_type', 'monthly');
    }
}
