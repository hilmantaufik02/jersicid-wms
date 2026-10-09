<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterSku;
use App\Models\StockActive;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\Order;
use App\Models\AuditTrail;
use App\Models\WarehouseRack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function owner()
    {
        // ==================== STATS CARDS ====================

        // Total Stok (unit)
        $totalStock = StockActive::sum('qty');

        // Nilai Stok (Rp) - SUM(qty * price)
        $stockValue = DB::table('stock_actives')
            ->join('master_skus', 'stock_actives.sku', '=', 'master_skus.sku')
            ->where('master_skus.status', 'AKTIF')
            ->sum(DB::raw('stock_actives.qty * master_skus.price'));

        // Low Stock Count
        $lowStockCount = MasterSku::where('status', 'AKTIF')
            ->whereRaw('min_stock > 0')
            ->whereRaw('COALESCE((SELECT SUM(qty) FROM stock_actives WHERE stock_actives.sku = master_skus.sku), 0) <= min_stock')
            ->count();

        // Inbound & Outbound Hari Ini
        $todayInbound = StockMovement::where('type', 'INBOUND')
            ->whereDate('created_at', today())
            ->sum('qty');

        $todayOutbound = StockMovement::where('type', 'OUTBOUND')
            ->whereDate('created_at', today())
            ->sum('qty');

        // In Production (PO dengan status IN_PRODUCTION)
        $inProduction = PurchaseOrder::where('status', 'IN_PRODUCTION')->count();

        // Supplier Aktif
        $activeSuppliers = Supplier::where('status', 'AKTIF')->count();

        // Order Aktif (PO dengan status APPROVED/SENT)
        $activeOrders = PurchaseOrder::whereIn('status', ['APPROVED', 'SENT'])->count();

        // ==================== CHART DATA (7 Hari Terakhir) ====================

        $last7Days = collect();
        $inboundData = [];
        $outboundData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $label = now()->subDays($i)->format('d M');

            $inbound = StockMovement::where('type', 'INBOUND')
                ->whereDate('created_at', $date)
                ->sum('qty');

            $outbound = StockMovement::where('type', 'OUTBOUND')
                ->whereDate('created_at', $date)
                ->sum('qty');

            $last7Days->push($label);
            $inboundData[] = $inbound;
            $outboundData[] = $outbound;
        }

        // ==================== DISTRIBUSI STOK PER KATEGORI ====================

        $categoryDistribution = MasterSku::select('master_skus.size_category', DB::raw('COALESCE(SUM(stock_actives.qty), 0) as total_qty'))
            ->leftJoin('stock_actives', 'master_skus.sku', '=', 'stock_actives.sku')
            ->where('master_skus.status', 'AKTIF')
            ->groupBy('master_skus.size_category')
            ->orderByDesc('total_qty')
            ->get();

        // ==================== TOP 5 SKU STOK TERBANYAK ====================

        $topSkus = MasterSku::select('master_skus.*', DB::raw('COALESCE(SUM(stock_actives.qty), 0) as current_stock'))
            ->leftJoin('stock_actives', 'master_skus.sku', '=', 'stock_actives.sku')
            ->where('master_skus.status', 'AKTIF')
            ->groupBy('master_skus.id')
            ->orderByDesc('current_stock')
            ->limit(5)
            ->get();

        // ==================== PERINGATAN LOW STOCK ====================

        $lowStockItems = MasterSku::select('master_skus.*', DB::raw('COALESCE(SUM(stock_actives.qty), 0) as current_stock'))
            ->leftJoin('stock_actives', 'master_skus.sku', '=', 'stock_actives.sku')
            ->where('master_skus.status', 'AKTIF')
            ->whereRaw('master_skus.min_stock > 0')
            ->groupBy('master_skus.id')
            ->havingRaw('COALESCE(SUM(stock_actives.qty), 0) <= master_skus.min_stock')
            ->orderBy('current_stock')
            ->limit(5)
            ->get();

        // ==================== AKTIVITAS TERAKHIR ====================

        $recentActivities = AuditTrail::with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // ==================== PASS TO VIEW ====================

        return view('dashboard.owner', compact(
            'totalStock',
            'stockValue',
            'lowStockCount',
            'todayInbound',
            'todayOutbound',
            'inProduction',
            'activeSuppliers',
            'activeOrders',
            'last7Days',
            'inboundData',
            'outboundData',
            'categoryDistribution',
            'topSkus',
            'lowStockItems',
            'recentActivities'
        ));
    }

    public function gudang()
    {
        // ==================== STATS CARDS ====================

        // Total Stok
        $totalStock = StockActive::sum('qty');

        // Low Stock Count
        $lowStockCount = MasterSku::select('master_skus.sku', 'master_skus.min_stock')
            ->join('stock_actives', 'master_skus.sku', '=', 'stock_actives.sku')
            ->where('master_skus.status', 'AKTIF')
            ->whereRaw('stock_actives.qty <= master_skus.min_stock')
            ->selectRaw('SUM(stock_actives.qty) as current_qty')
            ->groupBy('master_skus.sku', 'master_skus.min_stock')
            ->havingRaw('SUM(stock_actives.qty) <= master_skus.min_stock')
            ->count();

        // Inbound & Outbound Hari Ini
        $todayInbound = StockMovement::where('type', 'INBOUND')
            ->whereDate('created_at', today())
            ->sum('qty');

        $todayOutbound = StockMovement::where('type', 'OUTBOUND')
            ->whereDate('created_at', today())
            ->sum('qty');

        // Picking & Packing Stats
        $pendingPicking = \App\Models\PickingSession::where('status', 'PENDING')->count();
        $pendingPacking = \App\Models\PackingSession::where('status', 'PENDING')->count();
        $inProgressPicking = \App\Models\PickingSession::where('status', 'IN_PROGRESS')->count();
        $inProgressPacking = \App\Models\PackingSession::where('status', 'IN_PROGRESS')->count();
        $completedToday = \App\Models\PickingSession::where('status', 'COMPLETED')
            ->whereDate('completed_at', today())->count()
            + \App\Models\PackingSession::where('status', 'COMPLETED')
            ->whereDate('completed_at', today())->count();

        // ==================== PANEL DATA ====================

        // Pending PO (Purchase Order siap diterima - status APPROVED)
        $pendingPO = PurchaseOrder::whereIn('status', ['APPROVED', 'SENT'])->count();

        // Pending Pick (Order yang butuh picking - status CONFIRMED)
        $pendingPick = \App\Models\Order::where('status', 'CONFIRMED')->count();

        // Pending Pack (Order yang butuh packing - status PICKING_COMPLETED)
        $pendingPack = \App\Models\Order::where('status', 'PICKING_COMPLETED')->count();

        // Low Stock Items (untuk panel alert)
        $lowStockItems = MasterSku::select('master_skus.sku', 'master_skus.product_name', 'master_skus.min_stock')
            ->join('stock_actives', 'master_skus.sku', '=', 'stock_actives.sku')
            ->where('master_skus.status', 'AKTIF')
            ->whereRaw('stock_actives.qty <= master_skus.min_stock')
            ->selectRaw('SUM(stock_actives.qty) as current_qty')
            ->groupBy('master_skus.sku', 'master_skus.product_name', 'master_skus.min_stock')
            ->havingRaw('SUM(stock_actives.qty) <= master_skus.min_stock')
            ->orderBy('current_qty')
            ->limit(5)
            ->get();

        // Recent Movements (5 pergerakan terakhir)
        $recentMovements = StockMovement::with(['masterSku:sku,product_name', 'user:id,name'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // ==================== PASS TO VIEW ====================

        return view('dashboard.gudang', compact(
            'totalStock',
            'lowStockCount',
            'todayInbound',
            'todayOutbound',
            'pendingPicking',
            'pendingPacking',
            'inProgressPicking',
            'inProgressPacking',
            'completedToday',
            'pendingPO',
            'pendingPick',
            'pendingPack',
            'lowStockItems',
            'recentMovements'
        ));
    }
}
