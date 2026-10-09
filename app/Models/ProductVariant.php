<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Exception;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'size',
        'color',
        'stock',
        'min_stock',
        'max_stock',
        'price_override',
        'barcode',
        'qr_code',
        'location_code',
        'weight_override_grams',
        'is_active',
    ];

    protected $casts = [
        'price_override' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'variant_id');
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class, 'variant_id');
    }

    public function pickingListItems()
    {
        return $this->hasMany(PickingListItem::class);
    }

    // Scopes
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'min_stock');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock', 0);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    // Accessors
    public function getFinalPriceAttribute()
    {
        return $this->price_override ?? $this->product->base_price;
    }
    public function getIsLowStockAttribute()
    {
        return $this->stock <= $this->min_stock;
    }

    // Methods
    public function updateStock($quantity, $type, $warehouseId, $userId, $referenceType = null, $referenceId = null)
    {
        // Menggunakan if/else standar agar kompatibel dengan semua versi PHP
        $newStock = $this->stock;

        if ($type === 'IN') {
            $newStock = $this->stock + $quantity;
        } elseif ($type === 'OUT') {
            $newStock = $this->stock - $quantity;
        } elseif ($type === 'ADJUSTMENT') {
            $newStock = $quantity;
        }

        if ($newStock < 0) {
            throw new Exception('Stok tidak mencukupi untuk transaksi ini.');
        }

        // Update stok di database
        $this->update(['stock' => $newStock]);

        // Catat pergerakan stok
        StockMovement::create([
            'variant_id' => $this->id,
            'warehouse_id' => $warehouseId,
            'type' => $type,
            'quantity' => $quantity,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'user_id' => $userId,
        ]);

        return $this;
    }

    /**
     * Get short SKU without color
     * Usage: $variant->short_sku
     */
    public function getShortSkuAttribute(): string
    {
        $parts = explode('-', $this->sku);

        if (count($parts) >= 3) {
            return implode('-', array_slice($parts, 0, -1));
        }

        return $this->sku;
    }

    /**
     * Get full SKU with color
     * Usage: $variant->full_sku
     */
    public function getFullSkuAttribute(): string
    {
        return $this->sku;
    }
}
