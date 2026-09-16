<?php

namespace App\Exports;

use App\Models\MasterSku;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MasterSkuExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
     * Return type WAJIB: Enumerable
     * Sesuai interface FromCollection versi terbaru
     */
    public function collection(): Enumerable
    {
        return MasterSku::with('stockActives')->get();
    }

    /**
     * Map setiap row data ke array
     * Return type: array
     */
    public function map($sku): array
    {
        return [
            $sku->sku,
            $sku->article,
            $sku->product_name,
            $sku->version,
            $sku->sub_version,
            $sku->size_category,
            $sku->size,
            $sku->size_token,
            $sku->price,
            $sku->standard_weight_gram,
            $sku->min_stock,
            $sku->status,
        ];
    }

    /**
     * Headings untuk baris pertama Excel
     * Return type: array
     */
    public function headings(): array
    {
        return [
            'SKU',
            'Article',
            'Product Name',
            'Version',
            'Sub Version',
            'Size Category',
            'Size',
            'Size Token',
            'Price',
            'Standard Weight (gram)',
            'Min Stock',
            'Status',
        ];
    }

    /**
     * Styling untuk header Excel
     * Parameter type: Worksheet
     * Return type: array
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '22D3EE'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E293B'],
                ],
            ],
        ];
    }
}