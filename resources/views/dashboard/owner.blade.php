@extends('layouts.app')

@section('title', 'Dashboard Owner')
@section('page-title', 'Dashboard Owner')
@section('page-subtitle', 'Overview sistem Warehouse Management')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <div class="bg-wms-card border border-wms-border rounded-xl p-6">
        <h3 class="text-wms-muted text-sm mb-2">Total SKU</h3>
        <p class="text-3xl font-bold text-wms-accent">0</p>
    </div>
    <div class="bg-wms-card border border-wms-border rounded-xl p-6">
        <h3 class="text-wms-muted text-sm mb-2">Total Stok</h3>
        <p class="text-3xl font-bold text-emerald-400">0</p>
    </div>
    <div class="bg-wms-card border border-wms-border rounded-xl p-6">
        <h3 class="text-wms-muted text-sm mb-2">Inbound Hari Ini</h3>
        <p class="text-3xl font-bold text-cyan-400">0</p>
    </div>
    <div class="bg-wms-card border border-wms-border rounded-xl p-6">
        <h3 class="text-wms-muted text-sm mb-2">Outbound Hari Ini</h3>
        <p class="text-3xl font-bold text-orange-400">0</p>
    </div>
</div>
@endsection