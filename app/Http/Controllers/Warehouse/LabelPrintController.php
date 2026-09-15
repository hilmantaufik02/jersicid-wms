<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\StockActive;
use Illuminate\Http\JsonResponse;

class LabelPrintController extends Controller
{
    public function generateLabel(string $sku, string $binCode): JsonResponse
    {
        $stock = StockActive::with('masterSku')->where('sku', $sku)->where('bin_code', $binCode)->firstOrFail();
        
        return response()->json([
            'success' => true,
            'data' => [
                'sku'          => $stock->sku,
                'product_name' => $stock->masterSku->product_name,
                'bin_code'     => $stock->bin_code,
                'qty'          => $stock->qty,
                'batch_no'     => $stock->batch_no ?? '-',
                'date'         => now()->format('d/m/Y H:i'),
            ]
        ]);
    }
}