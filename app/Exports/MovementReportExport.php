<?php

namespace App\Exports;

use App\Models\StockMovement;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MovementReportExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldQueue
{
    use Exportable;

    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function query()
    {
        $query = StockMovement::query()
            ->join('master_skus', 'stock_movements.sku', '=', 'master_skus.sku')
            ->join('users', 'stock_movements.user_id', '=', 'users.id')
            ->select('stock_movements.*', 'master_skus.product_name', 'users.name as user_name');

        if ($this->request->filled('start_date')) {
            $query->where('stock_movements.created_at', '>=', $this->request->start_date);
        }
        if ($this->request->filled('end_date')) {
            $query->where('stock_movements.created_at', '<=', $this->request->end_date . ' 23:59:59');
        }
        if ($this->request->filled('type')) {
            $query->where('stock_movements.type', $this->request->type);
        }
        if ($this->request->filled('search')) {
            $query->where(function ($q) {
                $q->where('stock_movements.reference_no', 'like', "%{$this->request->search}%")
                    ->orWhere('stock_movements.sku', 'like', "%{$this->request->search}%");
            });
        }

        return $query->orderBy('stock_movements.created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'Tanggal & Waktu',
            'No. Referensi',
            'Tipe',
            'SKU',
            'Nama Produk',
            'Lokasi Bin',
            'Qty Sebelum',
            'Qty Perubahan',
            'Qty Sesudah',
            'User',
            'Catatan'
        ];
    }

    public function map($row): array
    {
        $qtyChange = $row->type === 'INBOUND' || $row->type === 'ADJUSTMENT' && ($row->qty_after > $row->qty_before)
            ? '+' . $row->qty : '-' . $row->qty;

        return [
            $row->created_at->format('d-m-Y H:i:s'),
            $row->reference_no,
            $row->type,
            $row->sku,
            $row->product_name,
            $row->bin_code,
            $row->qty_before,
            $qtyChange,
            $row->qty_after,
            $row->user_name,
            $row->notes,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0F172A']]],
        ];
    }
}
