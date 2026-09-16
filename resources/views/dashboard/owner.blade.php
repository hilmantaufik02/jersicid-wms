@extends('layouts.app')

@section('title', 'Dashboard Owner')
@section('page-title', 'Dashboard Owner')
@section('page-subtitle', 'Overview sistem Warehouse Management')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    
    <!-- Statistics Cards - Grid 4 Kolom -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total SKU -->
        <div class="gradient-card border border-slate-700 rounded-xl p-6 glow-cyan hover:glow-cyan-strong transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-cyan-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <span class="text-xs text-slate-400 uppercase tracking-wider">Total SKU</span>
            </div>
            <p class="text-3xl font-bold text-cyan-400 mb-1">{{ number_format($totalSku) }}</p>
            <p class="text-xs text-slate-400">SKU Aktif</p>
        </div>

        <!-- Total Stok -->
        <div class="gradient-card border border-slate-700 rounded-xl p-6 hover:shadow-[0_0_20px_rgba(16,185,129,0.2)] transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-emerald-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="text-xs text-slate-400 uppercase tracking-wider">Total Stok</span>
            </div>
            <p class="text-3xl font-bold text-emerald-400 mb-1">{{ number_format($totalStok) }}</p>
            <p class="text-xs text-slate-400">Unit Tersedia</p>
        </div>

        <!-- Inbound Today -->
        <div class="gradient-card border border-slate-700 rounded-xl p-6 hover:shadow-[0_0_20px_rgba(6,182,212,0.2)] transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-cyan-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                    </svg>
                </div>
                <span class="text-xs text-slate-400 uppercase tracking-wider">Inbound</span>
            </div>
            <p class="text-3xl font-bold text-cyan-400 mb-1">{{ $inboundToday }}</p>
            <p class="text-xs text-slate-400">Hari Ini</p>
        </div>

        <!-- Outbound Today -->
        <div class="gradient-card border border-slate-700 rounded-xl p-6 hover:shadow-[0_0_20px_rgba(249,115,22,0.2)] transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-orange-500/10 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </div>
                <span class="text-xs text-slate-400 uppercase tracking-wider">Outbound</span>
            </div>
            <p class="text-3xl font-bold text-orange-400 mb-1">{{ $outboundToday }}</p>
            <p class="text-xs text-slate-400">Hari Ini</p>
        </div>
    </div>

    <!-- Main Content Grid - 2 Kolom -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Low Stock Alert (2/3 width) -->
        <div class="lg:col-span-2 gradient-card border border-slate-700 rounded-xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Stok Menipis
                </h3>
                <span class="text-xs text-slate-400">{{ $lowStockItems->count() }} item</span>
            </div>

            <div class="space-y-3">
                @forelse($lowStockItems as $item)
                <div class="flex items-center justify-between p-3 bg-slate-900/50 rounded-lg border border-slate-700 hover:border-red-500/30 transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-500/10 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-mono text-cyan-400">{{ $item->sku }}</p>
                            <p class="text-xs text-slate-400">{{ $item->product_name }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-red-400">Min: {{ $item->min_stock }}</p>
                        <p class="text-xs text-slate-400">Stok rendah</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-slate-400">
                    <svg class="w-12 h-12 mx-auto mb-2 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm">Semua stok dalam kondisi aman</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Stock Distribution (1/3 width) -->
        <div class="gradient-card border border-slate-700 rounded-xl p-6">
            <h3 class="text-lg font-bold text-white mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Distribusi Kategori
            </h3>

            <div class="space-y-4">
                @forelse($stockByCategory as $category)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-slate-300">{{ $category->size_category }}</span>
                        <span class="text-cyan-400 font-bold">{{ $category->total }}</span>
                    </div>
                    <div class="w-full bg-slate-700 rounded-full h-2">
                        <div class="bg-gradient-to-r from-cyan-400 to-cyan-600 h-2 rounded-full" 
                             style="width: {{ $totalSku > 0 ? ($category->total / $totalSku * 100) : 0 }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-center text-slate-400 text-sm py-4">Belum ada data kategori</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Activities - Full Width -->
    <div class="gradient-card border border-slate-700 rounded-xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Aktivitas Terbaru
            </h3>
            <a href="{{ route('audit-trail.index') }}" class="text-xs text-cyan-400 hover:text-cyan-300 transition-colors">Lihat Semua →</a>
        </div>

        <div class="space-y-3">
            @forelse($recentActivities as $activity)
            <div class="flex items-center gap-4 p-3 bg-slate-900/50 rounded-lg border border-slate-700 hover:border-cyan-500/30 transition-all">
                <div class="flex-shrink-0">
                    @if($activity->action === 'INBOUND')
                        <div class="w-10 h-10 bg-cyan-500/10 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                            </svg>
                        </div>
                    @elseif($activity->action === 'OUTBOUND')
                        <div class="w-10 h-10 bg-orange-500/10 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </div>
                    @else
                        <div class="w-10 h-10 bg-slate-500/10 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    @endif
                </div>
                
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-white">
                        <span class="font-bold text-cyan-400">{{ $activity->user?->name ?? 'System' }}</span>
                        <span class="text-slate-400">{{ $activity->action }}</span>
                        <span class="text-slate-300">{{ $activity->module }}</span>
                    </p>
                    <p class="text-xs text-slate-400 mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                </div>

                <div class="text-right flex-shrink-0 hidden md:block">
                    <p class="text-xs font-mono text-slate-400">{{ $activity->ip_address }}</p>
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-slate-400">
                <p class="text-sm">Belum ada aktivitas</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection