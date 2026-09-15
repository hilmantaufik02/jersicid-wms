<?php

namespace App\Imports;

use App\Models\MasterSku;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MasterSkuImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row): ?MasterSku
    {
        return new MasterSku([
            'sku'                 => $row['sku'],
            'article'             => $row['article'],
            'product_name'        => $row['product_name'],
            'version'             => $row['version'] ?? 'V1',
            'size_category'       => $row['size_category'],
            'size'                => $row['size'],
            'price'               => $row['price'] ?? 0,
            'standard_weight_gram'=> $row['weight_gram'] ?? 250,
            'min_stock'           => $row['min_stock'] ?? 0,
            'status'              => $row['status'] ?? 'AKTIF',
        ]);
    }

    public function rules(): array
    {
        return [
            'sku' => 'required|unique:master_skus,sku',
            'product_name' => 'required',
            'size_category' => 'required|in:DEWASA,KIDS,TEENS',
        ];
    }
}