<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsTemplateExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return collect([
            [
                'nama_produk' => 'Jersic Basic Number 00',
                'sku_induk' => 'JER-N00',
                'kategori' => 'Basic Number',
                'supplier' => 'Sentra Multi Kreasi',
                'brand' => 'JERSIC.ID',
                'season' => 'All Season',
                'tahun' => 2025,
                'harga_dasar' => 150000,
                'harga_modal' => 100000,
                'deskripsi' => 'Jersey Basic Number 00',
                'ukuran' => 'M',
                'warna' => 'Hitam Putih',
                'stok' => 50,
                'min_stok' => 5,
                'harga_override' => '',
            ],
            [
                'nama_produk' => 'Jersic Basic Number 00',
                'sku_induk' => 'JER-N00',
                'kategori' => 'Basic Number',
                'supplier' => 'Sentra Multi Kreasi',
                'brand' => 'JERSIC.ID',
                'season' => 'All Season',
                'tahun' => 2025,
                'harga_dasar' => 150000,
                'harga_modal' => 100000,
                'deskripsi' => 'Jersey Basic Number 00',
                'ukuran' => 'L',
                'warna' => 'Hitam Putih',
                'stok' => 50,
                'min_stok' => 5,
                'harga_override' => '',
            ],
        ]);
    }

    public function headings(): array
    {
        return [
            'nama_produk',
            'sku_induk',
            'kategori',
            'supplier',
            'brand',
            'season',
            'tahun',
            'harga_dasar',
            'harga_modal',
            'deskripsi',
            'ukuran',
            'warna',
            'stok',
            'min_stok',
            'harga_override',
        ];
    }
}
