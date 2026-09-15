<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Master\MasterSkuController;
use App\Http\Controllers\Warehouse\InboundController;
use App\Http\Controllers\Warehouse\OutboundController;
use App\Http\Controllers\Warehouse\StockOpnameController;
use App\Http\Controllers\Warehouse\LabelPrintController;
use App\Http\Controllers\AuditController;

/*
|--------------------------------------------------------------------------
| Web Routes - jersic.id WMS v3.0
|--------------------------------------------------------------------------
*/

// Public Routes (Login/Register via Breeze)
require __DIR__.'/auth.php';

// Route Default (Redirect ke login atau dashboard)
Route::get('/', function () {
    return redirect()->route('login');
});

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard Default (Redirect berdasarkan role)
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        if ($user->role->value === 'OWNER') {
            return redirect()->route('dashboard.owner');
        } elseif (in_array($user->role->value, ['SUPERVISOR', 'STAFF_GUDANG'])) {
            return redirect()->route('gudang.inbound');
        }
        
        return view('dashboard.default');
    })->name('dashboard');

    // ============================================================
    // WORKSPACE: UTAMA (Akses: OWNER)
    // ============================================================
    Route::middleware(['role:OWNER'])->group(function () {
        
        Route::get('/dashboard/owner', [DashboardController::class, 'owner'])
            ->name('dashboard.owner');

        // Master Data Routes
        Route::prefix('master-data')->group(function () {
            Route::get('/', [MasterSkuController::class, 'index'])->name('master-data.index');
            Route::post('/import', [MasterSkuController::class, 'import'])->name('master-data.import');
            Route::get('/export', [MasterSkuController::class, 'export'])->name('master-data.export');
        });

        // Audit Trail
        Route::get('/audit-trail', [AuditController::class, 'index'])
            ->name('audit-trail.index');
    });

    // ============================================================
    // WORKSPACE: GUDANG (Akses: SUPERVISOR, STAFF_GUDANG)
    // ============================================================
    Route::prefix('gudang')
        ->middleware(['workspace:GUDANG', 'role:SUPERVISOR,STAFF_GUDANG'])
        ->group(function () {

        // INBOUND ROUTES
        Route::get('/inbound', [InboundController::class, 'create'])->name('gudang.inbound');
        Route::post('/inbound', [InboundController::class, 'store'])->name('gudang.inbound.store');

        // OUTBOUND ROUTES
        Route::get('/outbound', [OutboundController::class, 'create'])->name('gudang.outbound');
        Route::post('/outbound', [OutboundController::class, 'store'])->name('gudang.outbound.store');

        // STOCK OPNAME ROUTES
        Route::get('/opname', [StockOpnameController::class, 'index'])->name('gudang.opname');
        Route::post('/opname/reconcile', [StockOpnameController::class, 'reconcile'])->name('gudang.opname.reconcile');

        // THERMAL PRINTER ROUTE
        Route::get('/label/{sku}/{binCode}', [LabelPrintController::class, 'generateLabel'])
            ->name('gudang.label');
    });

    // ============================================================
    // WORKSPACE: PACKING (Akses: STAFF_GUDANG)
    // ============================================================
    Route::prefix('packing')
        ->middleware(['workspace:PACKING', 'role:STAFF_GUDANG'])
        ->group(function () {
        
        Route::get('/scan', function () {
            return view('warehouse.packing.scan');
        })->name('packing.scan');

        Route::get('/manifest', function () {
            return view('warehouse.packing.manifest');
        })->name('packing.manifest');
    });

    // ============================================================
    // WORKSPACE: PO (Akses: SUBLIM, KONVEKSI) - Placeholder
    // ============================================================
    Route::prefix('po')
        ->middleware(['workspace:PO', 'role:SUBLIM,KONVEKSI'])
        ->group(function () {
        
        Route::get('/sublim', function () {
            return view('po.sublim.index');
        })->name('po.sublim');

        Route::get('/konveksi', function () {
            return view('po.konveksi.index');
        })->name('po.konveksi');
    });
});