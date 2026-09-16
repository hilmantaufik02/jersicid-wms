<?php

namespace App\Http\Controllers;

use App\Models\MasterSku;
use App\Models\StockActive;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function owner()
    {
        // Statistik Utama
        $totalSku = MasterSku::where('status', 'AKTIF')->count();
        $totalStok = StockActive::sum('qty');
        
        // Inbound & Outbound Hari Ini
        $today = now()->toDateString();
        $inboundToday = AuditTrail::where('action', 'INBOUND')
            ->whereDate('created_at', $today)
            ->count();
        $outboundToday = AuditTrail::where('action', 'OUTBOUND')
            ->whereDate('created_at', $today)
            ->count();

        // Stok Menipis (Low Stock Alert)
        $lowStockItems = MasterSku::where('status', 'AKTIF')
            ->whereColumn('min_stock', '>', DB::raw('(SELECT COALESCE(SUM(qty), 0) FROM stock_actives WHERE stock_actives.sku = master_skus.sku)'))
            ->limit(5)
            ->get();

        // Aktivitas Terbaru
        $recentActivities = AuditTrail::with('user')
            ->latest()
            ->limit(10)
            ->get();

        // Distribusi Stok per Kategori Size
        $stockByCategory = MasterSku::select('size_category', DB::raw('COUNT(*) as total'))
            ->where('status', 'AKTIF')
            ->groupBy('size_category')
            ->get();

        return view('dashboard.owner', compact(
            'totalSku',
            'totalStok',
            'inboundToday',
            'outboundToday',
            'lowStockItems',
            'recentActivities',
            'stockByCategory'
        ));
    }
}