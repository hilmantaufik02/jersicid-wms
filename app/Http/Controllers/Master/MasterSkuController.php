<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MasterSku;
use App\Exports\MasterSkuExport;
use App\Imports\MasterSkuImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasterSkuController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = MasterSku::query();

            // Advanced Filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('sku', 'LIKE', "%{$search}%")
                      ->orWhere('product_name', 'LIKE', "%{$search}%")
                      ->orWhere('article', 'LIKE', "%{$search}%");
                });
            }

            if ($request->filled('product_name')) {
                $query->where('product_name', 'LIKE', "%{$request->product_name}%");
            }

            if ($request->filled('sku')) {
                $query->where('sku', 'LIKE', "%{$request->sku}%");
            }

            if ($request->filled('article')) {
                $query->where('article', 'LIKE', "%{$request->article}%");
            }

            if ($request->filled('size_category')) {
                $query->where('size_category', $request->size_category);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Group by product
            $products = $query->selectRaw('
                    product_name,
                    article,
                    version,
                    size_category,
                    COUNT(*) as total_variants,
                    MIN(price) as min_price,
                    MAX(price) as max_price,
                    GROUP_CONCAT(DISTINCT size ORDER BY size SEPARATOR ", ") as available_sizes
                ')
                ->groupBy('product_name', 'article', 'version', 'size_category')
                ->orderBy('product_name')
                ->paginate(12)
                ->withQueryString();

            return view('master.skus.index', compact('products'));
            
        } catch (\Exception $e) {
            Log::error('MasterSku Index Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat data: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|unique:master_skus,sku|max:50',
            'article' => 'required|string|max:50',
            'product_name' => 'required|string|max:255',
            'version' => 'nullable|string|max:20',
            'sub_version' => 'nullable|string|max:20',
            'size_category' => 'required|in:DEWASA,KIDS,TEENS',
            'size' => 'required|string|max:10',
            'size_token' => 'nullable|string|max:20',
            'price' => 'required|integer|min:0',
            'standard_weight_gram' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'status' => 'required|in:AKTIF,NONAKTIF',
        ]);

        try {
            DB::beginTransaction();
            
            MasterSku::create($validated);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'SKU berhasil ditambahkan!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store SKU Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah SKU: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $sku = MasterSku::findOrFail($id);
        return response()->json($sku);
    }

    public function update(Request $request, $id)
    {
        $sku = MasterSku::findOrFail($id);

        $validated = $request->validate([
            'sku' => 'required|string|unique:master_skus,sku,' . $id . '|max:50',
            'article' => 'required|string|max:50',
            'product_name' => 'required|string|max:255',
            'version' => 'nullable|string|max:20',
            'sub_version' => 'nullable|string|max:20',
            'size_category' => 'required|in:DEWASA,KIDS,TEENS',
            'size' => 'required|string|max:10',
            'size_token' => 'nullable|string|max:20',
            'price' => 'required|integer|min:0',
            'standard_weight_gram' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'status' => 'required|in:AKTIF,NONAKTIF',
        ]);

        try {
            DB::beginTransaction();
            
            $sku->update($validated);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'SKU berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update SKU Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui SKU: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $sku = MasterSku::findOrFail($id);

        try {
            DB::beginTransaction();
            
            $sku->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'SKU berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete SKU Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus SKU: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus($id)
    {
        $sku = MasterSku::findOrFail($id);

        try {
            $newStatus = $sku->status === 'AKTIF' ? 'NONAKTIF' : 'AKTIF';
            $sku->update(['status' => $newStatus]);
            
            return response()->json([
                'success' => true,
                'message' => "Status SKU berhasil diubah menjadi {$newStatus}",
                'new_status' => $newStatus
            ]);
        } catch (\Exception $e) {
            Log::error('Toggle Status Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProductVariants($productName)
    {
        $variants = MasterSku::where('product_name', $productName)
            ->orderBy('size')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $variants
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ], [
            'file.required' => 'Silakan pilih file Excel untuk diimport.',
            'file.mimes' => 'File harus berformat Excel (.xlsx, .xls) atau CSV.',
            'file.max' => 'Ukuran file tidak boleh lebih dari 10MB.',
        ]);

        try {
            $import = new MasterSkuImport;
            Excel::import($import, $request->file('file'));

            $successCount = $import->getSuccessCount();
            $failCount = $import->getFailCount();
            $failures = $import->getFailures();

            if ($successCount > 0 && $failCount === 0) {
                return response()->json([
                    'success' => true,
                    'message' => "✅ Import berhasil! {$successCount} data SKU ditambahkan."
                ]);
            }

            if ($successCount > 0 && $failCount > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "⚠️ Import sebagian berhasil: {$successCount} data ditambahkan, {$failCount} data dilewati.",
                    'failures' => $failures
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => "❌ Import gagal. Semua data tidak valid.",
                'failures' => $failures
            ], 422);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris " . $failure->row() . ": " . implode(', ', $failure->errors());
            }

            return response()->json([
                'success' => false,
                'message' => 'Import gagal karena kesalahan validasi.',
                'failures' => $errorMessages
            ], 422);

        } catch (\Exception $e) {
            Log::error('Import Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Import gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request) 
    {
        try {
            return Excel::download(new MasterSkuExport, 'master_skus_' . date('Y-m-d_His') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Export Error: ' . $e->getMessage());
            return back()->with('error', 'Export gagal: ' . $e->getMessage());
        }
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'sku_ids' => 'required|array|min:1',
            'sku_ids.*' => 'required|integer|exists:master_skus,id'
        ]);

        try {
            DB::beginTransaction();
            
            $count = MasterSku::whereIn('id', $validated['sku_ids'])->count();
            MasterSku::whereIn('id', $validated['sku_ids'])->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$count} SKU."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk Delete Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus SKU: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'sku_ids' => 'required|array|min:1',
            'sku_ids.*' => 'required|integer|exists:master_skus,id',
            'status' => 'required|in:AKTIF,NONAKTIF'
        ]);

        try {
            DB::beginTransaction();
            
            $count = MasterSku::whereIn('id', $validated['sku_ids'])
                ->update(['status' => $validated['status']]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Berhasil mengubah status {$count} SKU menjadi {$validated['status']}."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk Update Status Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ], 500);
        }
    }
}