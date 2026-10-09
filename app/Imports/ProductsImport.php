<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public $successCount = 0;
    public $errorCount = 0;
    public $errors = [];

    private function normalizeRow(array $row): array
    {
        $normalized = [];
        foreach ($row as $key => $value) {
            $cleanKey = preg_replace('/[\s\p{Z}\p{C}]+/u', '', $key);
            $cleanKey = strtolower($cleanKey);
            $cleanKey = preg_replace('/[-\.\s]+/', '_', $cleanKey);
            $cleanKey = preg_replace('/_+/', '_', $cleanKey);
            $cleanKey = trim($cleanKey, '_');
            $normalized[$cleanKey] = $value;
        }
        return $normalized;
    }

    private function getValue(array $row, ...$keys)
    {
        foreach ($keys as $key) {
            $normalizedKey = preg_replace('/[\s\p{Z}\p{C}]+/u', '', $key);
            $normalizedKey = strtolower($normalizedKey);
            $normalizedKey = preg_replace('/[-\.\s]+/', '_', $normalizedKey);
            $normalizedKey = preg_replace('/_+/', '_', $normalizedKey);
            $normalizedKey = trim($normalizedKey, '_');

            if (isset($row[$normalizedKey]) && !empty($row[$normalizedKey])) {
                return $row[$normalizedKey];
            }
        }

        foreach ($keys as $key) {
            $searchKey = strtolower(str_replace([' ', '-', '.'], '_', $key));
            foreach ($row as $actualKey => $value) {
                if (str_contains($actualKey, $searchKey) && !empty($value)) {
                    return $value;
                }
            }
        }

        return null;
    }

    private function parseNumber($value): float
    {
        if (is_null($value) || $value === '') {
            return 0.0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $cleaned = preg_replace('/[^0-9,\.]/', '', $value);
        $cleaned = str_replace('.', '', $cleaned);
        $cleaned = str_replace(',', '.', $cleaned);

        return (float) $cleaned;
    }

    public function model(array $row)
    {
        try {
            $row = $this->normalizeRow($row);

            Log::info('=== Processing Row ===');
            Log::info('Normalized keys: ' . json_encode(array_keys($row)));

            // Ambil data - HANYA nama dan SKU yang wajib
            $namaProduk = $this->getValue(
                $row,
                'nama_produk',
                'nama produk',
                'nama',
                'product_name',
                'product name',
                'namaproduk',
                'nm_produk',
                'product'
            );

            $skuInduk = $this->getValue(
                $row,
                'sku_induk',
                'sku induk',
                'sku',
                'kode_produk',
                'kode produk',
                'skuproduk',
                'sku_produk',
                'kode',
                'product_sku'
            );

            // ✅ HARGA BOLEH KOSONG - Default 0
            $hargaDasar = $this->getValue(
                $row,
                'harga_dasar',
                'harga dasar',
                'harga',
                'price',
                'base_price',
                'hargadasar',
                'harga_jual',
                'selling_price'
            );

            $hargaModal = $this->getValue(
                $row,
                'harga_modal',
                'harga modal',
                'cost_price',
                'hpp',
                'cost',
                'hargamodal',
                'modal',
                'purchase_price'
            );

            // ✅ VALIDASI: Hanya nama dan SKU yang wajib
            if (empty($namaProduk)) {
                $this->errorCount++;
                $this->errors[] = "Baris dengan SKU '{$skuInduk}': Nama produk kosong";
                Log::warning("❌ Skip: Nama produk kosong");
                return null;
            }

            if (empty($skuInduk)) {
                $this->errorCount++;
                $this->errors[] = "Baris '{$namaProduk}': SKU induk kosong";
                Log::warning("❌ Skip: SKU induk kosong");
                return null;
            }

            // ✅ Harga default 0 jika kosong
            $parsedHargaDasar = $hargaDasar ? $this->parseNumber($hargaDasar) : 0;
            $parsedHargaModal = $hargaModal ? $this->parseNumber($hargaModal) : 0;

            Log::info("📦 Product: {$namaProduk} | SKU: {$skuInduk} | Price: {$parsedHargaDasar} | Cost: {$parsedHargaModal}");

            // Category
            $category = null;
            $kategoriName = $this->getValue($row, 'kategori', 'category', 'kat', 'type');
            if (!empty($kategoriName)) {
                $category = Category::firstOrCreate(
                    ['name' => $kategoriName],
                    [
                        'slug' => Str::slug($kategoriName),
                        'type' => 'type', // ✅ TAMBAHKAN INI
                    ]
                );
            }

            // Supplier
            $supplier = null;
            $supplierName = $this->getValue($row, 'supplier', 'supplier_name', 'pemasok', 'vendor');
            if (!empty($supplierName)) {
                $supplier = Supplier::firstOrCreate(['name' => $supplierName]);
            }

            // Cari product
            $product = Product::where('sku', $skuInduk)->first();

            DB::beginTransaction();

            if (!$product) {
                $product = Product::create([
                    'sku' => $skuInduk,
                    'name' => $namaProduk,
                    'slug' => Str::slug($namaProduk) . '-' . Str::random(5),
                    'category_id' => $category?->id,
                    'supplier_id' => $supplier?->id,
                    'brand' => $this->getValue($row, 'brand', 'merek', 'merk'),
                    'season' => $this->getValue($row, 'season', 'musim'),
                    'year' => $this->getValue($row, 'tahun', 'year'),
                    'base_price' => $parsedHargaDasar,    // ✅ Default 0
                    'cost_price' => $parsedHargaModal,    // ✅ Default 0
                    'description' => $this->getValue($row, 'deskripsi', 'description', 'keterangan', 'desc'),
                    'is_active' => true,
                ]);
                Log::info("✅ Created product ID: {$product->id}");
            } else {
                $product->update([
                    'name' => $namaProduk,
                    'category_id' => $category?->id ?? $product->category_id,
                    'supplier_id' => $supplier?->id ?? $product->supplier_id,
                    'brand' => $this->getValue($row, 'brand', 'merek', 'merk') ?? $product->brand,
                    'base_price' => $parsedHargaDasar,
                    'cost_price' => $parsedHargaModal,
                ]);
                Log::info("🔄 Updated product ID: {$product->id}");
            }

            // Variant
            $ukuran = $this->getValue($row, 'ukuran', 'size', 'sz');
            $warna = $this->getValue($row, 'warna', 'color', 'clr');

            if (!empty($ukuran) && !empty($warna)) {
                $variantSku = $skuInduk . '-' . strtoupper($ukuran) . '-' . Str::substr(Str::slug($warna), 0, 3);

                ProductVariant::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'size' => strtoupper($ukuran),
                        'color' => $warna,
                    ],
                    [
                        'sku' => $variantSku,
                        'stock' => (int) ($this->getValue($row, 'stok', 'stock', 'qty', 'quantity') ?? 0),
                        'min_stock' => (int) ($this->getValue($row, 'min_stok', 'minimum_stok', 'min_stock') ?? 5),
                        'price_override' => null,
                        'is_active' => true,
                    ]
                );
            }

            DB::commit();
            $this->successCount++;
            return $product;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errorCount++;
            $errorMsg = "Error: " . $e->getMessage();
            $this->errors[] = $errorMsg;
            Log::error("❌ Failed: {$errorMsg}");
            return null;
        }
    }

    public function rules(): array
    {
        return [
            '*.nama_produk' => 'nullable|string|max:200',
            '*.sku_induk' => 'nullable|string|max:50',
            '*.harga_dasar' => 'nullable',      // ✅ BOLEH KOSONG
            '*.harga_modal' => 'nullable',      // ✅ BOLEH KOSONG
        ];
    }
}
