<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Services\InventoryMovementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InboundController extends Controller
{
    protected InventoryMovementService $movementService;

    public function __construct(InventoryMovementService $movementService)
    {
        $this->movementService = $movementService;
    }

    public function create()
    {
        return view('warehouse.inbound');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sku'      => ['required', 'string', 'exists:master_skus,sku'],
            'bin_code' => ['required', 'string', 'regex:/^RAK-[A-Z]-\d{2}-\d{2}$/'],
            'qty'      => ['required', 'integer', 'min:1'],
            'batch_no' => ['nullable', 'string', 'max:50'],
        ]);

        try {
            $stock = $this->movementService->processInbound(
                $validated['sku'], 
                $validated['bin_code'], 
                $validated['qty'], 
                $validated['batch_no'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => "Berhasil menambah {$validated['qty']} pcs SKU {$validated['sku']} ke bin {$validated['bin_code']}.",
                'data'    => [
                    'sku'      => $stock->sku,
                    'bin_code' => $stock->bin_code,
                    'qty'      => $stock->qty,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}