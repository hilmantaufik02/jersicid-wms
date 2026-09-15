<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\MasterSku;
use App\Exports\MasterSkuExport;
use App\Imports\MasterSkuImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MasterSkuController extends Controller
{
    public function index(Request $request)
    {
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
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv|max:5120']);

        try {
            Excel::import(new MasterSkuImport, $request->file('file'));
            return back()->with('success', 'Data SKU berhasil diimpor!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            return back()->withErrors('Terdapat error validasi pada baris tertentu. Periksa format Excel.');
        }
    }

    public function export() 
    {
        return Excel::download(new MasterSkuExport, 'master_skus_' . date('Y-m-d') . '.xlsx');
    }
}