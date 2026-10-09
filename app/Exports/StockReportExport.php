<?php

namespace App\Exports;

use App\Models\StockActive;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockReportExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldQueue
{
    use Exportable;

    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = StockActive::query()
            ->join('master_skus', 'stock_actives.sku', '=', 'master_skus.sku')
            ->select('stock_actives.*', 'master_skus.product_name', 'master_skus.size_category', 'master_skus.size', 'master_skus.min_stock', 'master_skus.status as sku_status');

        if ($this->request->filled('search')) {
            $query->where(function ($q) {
                $q->where('stock_actives.sku', 'like', "%{$this->request->search}%")
                    ->orWhere('master_skus.product_name', 'like', "%{$this->request->search}%");
            });
        }

        if ($this->request->filled('category')) {
            $query->where('master_skus.size_category', $this->request->category);
        }

        return $query->orderBy('stock_actives.sku')->orderBy('stock_actives.bin_code');
    }

    public function headings(): array
    {
        return [
            'SKU',
            'Nama Produk',
            'Kategori',
            'Size',
            'Lokasi Bin',
            'Qty Stok',
            'Min. Stok',
            'Status SKU',
            'Terakhir Update'
        ];
    }

    public function map($row): array
    {
        return [
            $row->sku,
            $row->product_name,
            $row->size_category,
            $row->size,
            $row->bin_code,
            $row->qty,
            $row->min_stock,
            $row->sku_status,
            $row->last_movement_at ? $row->last_movement_at->format('d-m-Y H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0F172A']]],
        ];
    }
}
