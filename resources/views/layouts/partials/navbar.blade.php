<header class="h-16 bg-wms-card border-b border-wms-border flex items-center justify-between px-6">
    <div class="flex items-center gap-4">
        <!-- Breadcrumb atau Judul Halaman bisa diletakkan di sini -->
    </div>

    <div class="flex items-center gap-4">
        <!-- Live Clock menggunakan Alpine.js -->
        <div class="text-wms-muted text-sm font-mono bg-wms-bg px-3 py-1.5 rounded border border-wms-border" 
             x-data="{ time: new Date().toLocaleTimeString('id-ID') }" 
             x-init="setInterval(() => time = new Date().toLocaleTimeString('id-ID'), 1000)">
            <span x-text="time"></span>
        </div>
    </div>
</header>