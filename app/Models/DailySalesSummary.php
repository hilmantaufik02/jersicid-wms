<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailySalesSummary extends Model
{
    // Tambahkan baris ini untuk memaksa Eloquent menggunakan nama tabel singular
    protected $table = 'daily_sales_summary';

    protected $fillable = [
        'warehouse_id',
        'sale_date',
        'total_transactions',
        'total_items_sold',
        'gross_sales',
        'tax_collected',
        'discounts_given',
        'net_sales',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'gross_sales' => 'decimal:2',
        'tax_collected' => 'decimal:2',
        'discounts_given' => 'decimal:2',
        'net_sales' => 'decimal:2',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public static function generateForDate($date, $warehouseId)
    {
        $transactions = Transaction::where('warehouse_id', $warehouseId)
            ->whereDate('created_at', $date)
            ->where('status', 'completed')
            ->get();

        return static::updateOrCreate(
            [
                'warehouse_id' => $warehouseId,
                'sale_date' => $date,
            ],
            [
                'total_transactions' => $transactions->count(),
                'total_items_sold' => $transactions->sum(fn($t) => $t->items->sum('quantity')),
                'gross_sales' => $transactions->sum('subtotal'),
                'tax_collected' => $transactions->sum('tax_amount'),
                'discounts_given' => $transactions->sum('discount_amount'),
                'net_sales' => $transactions->sum('total'),
            ]
        );
    }
}
