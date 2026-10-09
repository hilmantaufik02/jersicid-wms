<?php

namespace App\Helpers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;

class ProductSearcher
{
    /**
     * Search produk by SKU atau nama
     * Support: exact match, partial match, case-insensitive
     */
    public static function search(string $query, int $limit = 10): Collection
    {
        $query = trim($query);

        if (empty($query)) {
            return collect();
        }

        // Cari by SKU (exact match dulu)
        $bySku = ProductVariant::with('product')
            ->where('sku', 'LIKE', "%{$query}%")
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->limit($limit)
            ->get();

        // Cari by nama produk
        $byName = ProductVariant::with('product')
            ->whereHas('product', function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->limit($limit)
            ->get();

        // Merge dan unique by variant_id
        $merged = $bySku->merge($byName)->unique('id')->values();

        // Sort: exact match first, then by stock
        return $merged->sortByDesc(function ($variant) use ($query) {
            $exactSku = strtolower($variant->sku) === strtolower($query);
            $exactName = strtolower($variant->product->name) === strtolower($query);
            return ($exactSku || $exactName) ? 1 : 0;
        })->values();
    }

    /**
     * Find variant by exact SKU
     */
    public static function findBySku(string $sku): ?ProductVariant
    {
        return ProductVariant::with('product')
            ->where('sku', $sku)
            ->where('is_active', true)
            ->first();
    }
}
