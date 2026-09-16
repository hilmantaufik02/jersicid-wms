<?php

namespace App\Imports;

use App\Models\MasterSku;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;

class MasterSkuImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows, SkipsOnFailure
{
    protected int $successCount = 0;
    protected int $failCount = 0;
    protected array $failures = [];


    /**
     * Return type WAJIB: Model|array|null
     * Sesuai interface ToModel versi terbaru
     */
    public function model(array $row): Model|array|null
    {
        try {
            // Normalisasi key (handle berbagai format header)
            $sku = $row['sku'] ?? null;
            $productName = $row['product_name'] ?? $row['nama_produk'] ?? null;
            $sizeCategory = $row['size_category'] ?? $row['kategori_ukuran'] ?? 'DEWASA';
            $size = $row['size'] ?? $row['ukuran'] ?? null;

            // Skip baris yang data wajibnya kosong
            if (empty($sku) || empty($productName) || empty($size)) {
                $this->failCount++;
                $this->failures[] = "Baris kosong atau data wajib tidak lengkap (SKU: {$sku})";
                return null;
            }

            // Cek apakah SKU sudah ada (skip duplicate)
            $existing = MasterSku::where('sku', $sku)->first();
            if ($existing) {
                $this->failCount++;
                $this->failures[] = "SKU {$sku} sudah ada di database";
                return null;
            }

            $this->successCount++;

            return new MasterSku([
                'sku'                  => trim($sku),
                'article'              => trim($row['article'] ?? ''),
                'product_name'         => trim($productName),
                'version'              => trim($row['version'] ?? 'V1'),
                'sub_version'          => $row['sub_version'] ?? null,
                'size_category'        => strtoupper(trim($sizeCategory)),
                'size'                 => trim($size),
                'size_token'           => $row['size_token'] ?? null,
                'price'                => (int) ($row['price'] ?? $row['harga'] ?? 0),
                'standard_weight_gram' => (int) ($row['standard_weight_gram'] ?? $row['berat_gram'] ?? 250),
                'min_stock'            => (int) ($row['min_stock'] ?? $row['stok_minimum'] ?? 0),
                'status'               => strtoupper(trim($row['status'] ?? 'AKTIF')),
            ]);

        } catch (\Exception $e) {
            $this->failCount++;
            $this->failures[] = "Error: " . $e->getMessage();
            return null;
        }
    }

    /**
     * Validasi per baris
     */
    public function rules(): array
    {
        return [
            '*.sku' => 'required|string',
            '*.product_name' => 'required|string',
            '*.size_category' => 'required|in:DEWASA,KIDS,TEENS',
            '*.size' => 'required|string',
        ];
    }

    /**
     * Custom heading row mapping (jika header Excel berbeda)
     */
    public function headingRow(): int
    {
        return 1;
    }

    /**
     * Tangani failure dari validasi
     */
    public function onFailure(Failure ...$failures): void
    {
        foreach ($failures as $failure) {
            $this->failCount++;
            $this->failures[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
        }
    }

    // ============ GETTER METHODS ============

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getFailCount(): int
    {
        return $this->failCount;
    }

    public function getFailures(): array
    {
        return $this->failures;
    }
}