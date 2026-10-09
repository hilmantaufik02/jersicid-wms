<?php

namespace App\Helpers;

class SkuHelper
{
    /**
     * Convert full SKU to short SKU (remove color)
     * Example: JER-N00-M-hit → JER-N00-M
     */
    public static function toShort(string $sku): string
    {
        $parts = explode('-', $sku);

        if (count($parts) >= 2) {
            return implode('-', array_slice($parts, 0, -1));
        }

        return $sku;
    }

    /**
     * Convert short SKU to full SKU (add color)
     * Example: JER-N00-M + "Hitam" → JER-N00-M-hit
     */
    public static function toFull(string $shortSku, string $color): string
    {
        return $shortSku . '-' . \Illuminate\Support\Str::slug($color);
    }
}
