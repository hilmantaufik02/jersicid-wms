<?php

namespace App\Exports;

use App\Models\MasterSku;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Database\Eloquent\Builder;

class MasterSkuExport implements FromQuery, WithHeadings, ShouldAutoSize
{
    public function query(): Builder
    {
        return MasterSku::query()->select('sku', 'article', 'product_name', 'version', 'size_category', 'size', 'price', 'standard_weight_gram', 'min_stock', 'status');
    }

    public function headings(): array
    {
        return ['SKU', 'Article', 'Product Name', 'Version', 'Size Category', 'Size', 'Price', 'Weight (gram)', 'Min Stock', 'Status'];
    }
}