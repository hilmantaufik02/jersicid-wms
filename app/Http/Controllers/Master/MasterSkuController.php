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

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('sku', 'LIKE', "%{$search}%")
                      ->orWhere('product_name', 'LIKE', "%{$search}%")
                      ->orWhere('article', 'LIKE', "%{$search}%");
                });
            }

            $skus = $query->orderBy('product_name')->paginate(20)->withQueryString();
            
            return view('master.skus.index', compact('skus'));
            
        } catch (\Exception $e) {
            Log::error('MasterSku Index Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat data: ' . $e->getMessage());
        }
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
                return back()->with('success', "✅ Import berhasil! {$successCount} data SKU ditambahkan.");
            }

            if ($successCount > 0 && $failCount > 0) {
                return back()
                    ->with('success', "⚠️ Import sebagian berhasil: {$successCount} data ditambahkan, {$failCount} data dilewati.")
                    ->with('import_failures', $failures);
            }

            return back()
                ->with('error', "❌ Import gagal. Semua data tidak valid.")
                ->with('import_failures', $failures);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris " . $failure->row() . ": " . implode(', ', $failure->errors());
            }

            return back()
                ->with('error', 'Import gagal karena kesalahan validasi.')
                ->with('import_failures', $errorMessages);

        } catch (\Exception $e) {
            Log::error('Import Error: ' . $e->getMessage());
            return back()->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    public function export() 
    {
        try {
            return Excel::download(new MasterSkuExport, 'master_skus_' . date('Y-m-d_His') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Export Error: ' . $e->getMessage());
            return back()->with('error', 'Export gagal: ' . $e->getMessage());
        }
    }
}