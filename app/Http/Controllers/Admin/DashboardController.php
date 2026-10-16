<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterSku;
use App\Models\StockActive;
use App\Models\StockMovement;
use App\Models\InboundReceipt;
use App\Models\Order;
use App\Models\PickingSession;
use App\Models\PackingSession;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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
        $user = auth()->user();

        // ✅ FIX: Hitung total stok unit
        $totalStock = StockActive::sum('qty');

        // ✅ FIX: Hitung nilai stok (price × qty)
        $stockValue = DB::table('stock_actives')
            ->join('master_skus', 'stock_actives.sku', '=', 'master_skus.sku')
            ->selectRaw('SUM(master_skus.price * stock_actives.qty) as total_value')
            ->value('total_value') ?? 0;

        // Low stock alert
        $lowStock = MasterSku::where('status', 'AKTIF')
            ->whereRaw('min_stock > 0')
            ->whereRaw('COALESCE((SELECT SUM(qty) FROM stock_actives WHERE stock_actives.sku = master_skus.sku), 0) <= min_stock')
            ->count();

        // Inbound hari ini
        $inboundToday = InboundReceipt::whereDate('created_at', today())->sum('total_qty') ?? 0;

        // Outbound hari ini
        $outboundToday = Order::whereDate('created_at', today())->sum('total_qty') ?? 0;

        // Picking pending
        $pickingPending = PickingSession::where('status', 'PENDING')->count();

        // Packing pending
        $packingPending = PackingSession::where('status', 'PENDING')->count();

        // In progress
        $inProgress = PickingSession::where('status', 'IN_PROGRESS')->count()
            + PackingSession::where('status', 'IN_PROGRESS')->count();

        // Selesai hari ini
        $completedToday = PickingSession::whereDate('completed_at', today())->count()
            + PackingSession::whereDate('completed_at', today())->count();

        // PO siap diterima
        $poReady = PurchaseOrder::where('status', 'READY_FOR_PICKUP')->count();

        // Order perlu picking
        $orderNeedPicking = Order::whereIn('status', ['CONFIRMED', 'PENDING'])->count();

        // Peringatan stok menipis (top 5)
        $lowStockProducts = MasterSku::select('master_skus.*')
            ->selectRaw('COALESCE((SELECT SUM(qty) FROM stock_actives WHERE stock_actives.sku = master_skus.sku), 0) as current_stock')
            ->where('master_skus.status', 'AKTIF')
            ->whereRaw('min_stock > 0')
            ->whereRaw('COALESCE((SELECT SUM(qty) FROM stock_actives WHERE stock_actives.sku = master_skus.sku), 0) <= min_stock')
            ->orderBy('current_stock')
            ->limit(5)
            ->get();

        // Pergerakan terakhir
        $recentMovements = StockMovement::with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.gudang', compact(
            'totalStock',
            'stockValue',           // ✅ FIX: Kirim variabel ini
            'lowStock',
            'inboundToday',
            'outboundToday',
            'pickingPending',
            'packingPending',
            'inProgress',
            'completedToday',
            'poReady',
            'orderNeedPicking',
            'lowStockProducts',
            'recentMovements'
        ));
    }
}
