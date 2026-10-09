<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService
{
    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $product = Product::create([
                'name' => $data['name'],
                'sku' => $data['sku'],
                'slug' => Str::slug($data['name']).'-'.Str::random(5),
                'category_id' => $data['category_id'] ?? null,
                'supplier_id' => $data['supplier_id'] ?? null,
                'brand' => $data['brand'] ?? null,
                'season' => $data['season'] ?? null,
                'year' => $data['year'] ?? null,
                'base_price' => $data['base_price'] ?? 0,
                'cost_price' => $data['cost_price'] ?? 0,
                'description' => $data['description'] ?? null,
                'is_active' => true,
            ]);

            if (! empty($data['variants'])) {
                $this->createVariants($product, $data['variants']);
            }

            ActivityLogger::product('create', auth()->user()->name.' menambahkan produk: '.$product->name, $product);

            return $product;
        });
    }

    public function update(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            $product->update($data);

            if (! empty($data['variants'])) {
                $this->syncVariants($product, $data['variants']);
            }

            ActivityLogger::product('update', auth()->user()->name.' memperbarui produk: '.$product->name, $product);

            return $product;
        });
    }

    public function delete(Product $product): bool
    {
        $result = $product->delete();

        if ($result) {
            ActivityLogger::product('delete', auth()->user()->name.' menghapus produk: '.$product->name);
        }

        return $result;
    }

    private function createVariants(Product $product, array $variants): void
    {
        foreach ($variants as $variant) {
            ProductVariant::create([
                'product_id' => $product->id,
                'sku' => $this->generateVariantSku($product->sku, $variant['size'], $variant['color']),
                'size' => strtoupper($variant['size']),
                'color' => $variant['color'],
                'stock' => $variant['stock'] ?? 0,
                'min_stock' => $variant['min_stock'] ?? 5,
                'price_override' => $variant['price_override'] ?? null,
                'is_active' => true,
            ]);
        }
    }

    private function syncVariants(Product $product, array $variants): void
    {
        $existingIds = [];

        foreach ($variants as $variant) {
            $variantSku = $this->generateVariantSku($product->sku, $variant['size'], $variant['color']);

            if (! empty($variant['id'])) {
                ProductVariant::where('id', $variant['id'])->update([
                    'sku' => $variantSku,
                    'size' => strtoupper($variant['size']),
                    'color' => $variant['color'],
                    'stock' => $variant['stock'],
                    'min_stock' => $variant['min_stock'],
                    'price_override' => $variant['price_override'] ?? null,
                ]);
                $existingIds[] = $variant['id'];
            } else {
                $newVariant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $variantSku,
                    'size' => strtoupper($variant['size']),
                    'color' => $variant['color'],
                    'stock' => $variant['stock'],
                    'min_stock' => $variant['min_stock'],
                    'price_override' => $variant['price_override'] ?? null,
                    'is_active' => true,
                ]);
                $existingIds[] = $newVariant->id;
            }
        }

        // Hapus varian yang tidak ada di form
        ProductVariant::where('product_id', $product->id)
            ->whereNotIn('id', $existingIds)
            ->delete();
    }

    public function generateVariantSku(string $baseSku, string $size, string $color): string
    {
        return $baseSku.'-'.strtoupper($size).'-'.Str::substr(Str::slug($color), 0, 3);
    }
}
