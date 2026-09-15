<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\StockActive;
use App\Services\InventoryMovementService;
use Illuminate\Http\Request;

class StockOpnameController extends Controller
{
    public function index()
    {
        // Ambil semua stok aktif untuk di-opname (Bisa ditambah filter bin/area)
        $stocks = StockActive::with('masterSku')->orderBy('bin_code')->get();
        return view('warehouse.opname', compact('stocks'));
    }

    public function reconcile(Request $request, InventoryMovementService $service)
    {
        $validated = $request->validate([
            'counts'   => 'required|array',
            'counts.*.sku' => 'required|string|exists:master_skus,sku',
            'counts.*.bin_code' => 'required|string',
            'counts.*.physical_qty' => 'required|integer|min:0',
        ]);

        $results = ['adjusted' => 0, 'skipped' => 0, 'errors' => []];

        foreach ($validated['counts'] as $count) {
            // Hitung variance dari database (kita fetch ulang untuk dapat qty sistem terbaru)
            $currentStock = StockActive::where('sku', $count['sku'])->where('bin_code', $count['bin_code'])->first();
            $systemQty = $currentStock ? $currentStock->qty : 0;
            $variance = $count['physical_qty'] - $systemQty;

            if ($variance !== 0) {
                try {
                    $service->processAdjustment($count['sku'], $count['bin_code'], $variance);
                    $results['adjusted']++;
                } catch (\Exception $e) {
                    $results['errors'][] = "SKU {$count['sku']} di {$count['bin_code']}: " . $e->getMessage();
                }
            } else {
                $results['skipped']++;
            }
        }

        return back()->with('success', "Opname selesai. {$results['adjusted']} SKU disesuaikan, {$results['skipped']} SKU sesuai.");
    }
}