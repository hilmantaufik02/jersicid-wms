<x-admin-layout title="Produktivitas Tim">
    <x-slot name="pageTitle">Dashboard Produktivitas Tim</x-slot>

    <div class="space-y-6">
        <!-- Period Selector -->
        <div class="glass-panel p-4 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-bold text-primary">Aktivitas User</h2>
                <p class="text-sm text-muted">Tracking produktivitas dan kesalahan</p>
            </div>
            <div class="flex gap-2">
                <button onclick="loadProductivity('today')" class="period-btn active px-4 py-2 rounded-lg text-sm font-medium btn-ghost" data-period="today">Hari Ini</button>
                <button onclick="loadProductivity('week')" class="period-btn px-4 py-2 rounded-lg text-sm font-medium btn-ghost" data-period="week">Minggu Ini</button>
                <button onclick="loadProductivity('month')" class="period-btn px-4 py-2 rounded-lg text-sm font-medium btn-ghost" data-period="month">Bulan Ini</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Productivity Leaderboard -->
            <div class="glass-panel p-6">
                <h3 class="text-lg font-bold text-primary mb-4">🏆 Produktivitas User</h3>
                <div id="productivityList" class="space-y-3">
                    <div class="text-center py-10 text-muted">Memuat data...</div>
                </div>
            </div>

            <!-- Error Tracker -->
            <div class="glass-panel p-6">
                <h3 class="text-lg font-bold text-primary mb-4">⚠️ Tracker Kesalahan</h3>
                <div id="errorList" class="space-y-3">
                    <div class="text-center py-10 text-muted">Memuat data...</div>
                </div>
            </div>
        </div>

        <!-- Activity Breakdown -->
        <div class="glass-panel p-6">
            <h3 class="text-lg font-bold text-primary mb-4"> Breakdown Aktivitas</h3>
            <div id="breakdownChart" class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <!-- Will be filled by JS -->
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let currentPeriod = 'today';

        function loadProductivity(period) {
            currentPeriod = period;
            
            // Update active button
            document.querySelectorAll('.period-btn').forEach(btn => {
                btn.classList.remove('active');
                if (btn.dataset.period === period) btn.classList.add('active');
            });

            fetch(`/api/productivity?period=${period}`)
                .then(res => res.json())
                .then(data => {
                    renderProductivity(data.productivity);
                    renderErrors(data.errors);
                    renderBreakdown(data.productivity);
                });
        }

        function renderProductivity(users) {
            const container = document.getElementById('productivityList');
            
            if (users.length === 0) {
                container.innerHTML = '<div class="text-center py-10 text-muted">Tidak ada data</div>';
                return;
            }

            const medals = ['🥇', '🥈', '🥉'];
            
            container.innerHTML = users.map((u, i) => `
                <div class="flex items-center gap-3 p-3 rounded-lg bg-elevated border border-subtle">
                    <div class="text-2xl w-8">${medals[i] || (i + 1) + '.'}</div>
                    <div class="flex-1">
                        <p class="font-semibold text-primary">${u.user_name}</p>
                        <p class="text-xs text-muted">${u.user_role}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-indigo-400">${u.total_actions}</p>
                        <p class="text-xs text-muted">aksi</p>
                    </div>
                </div>
            `).join('');
        }

        function renderErrors(users) {
            const container = document.getElementById('errorList');
            
            if (users.length === 0) {
                container.innerHTML = '<div class="text-center py-10 text-muted text-sm">✅ Tidak ada kesalahan tercatat</div>';
                return;
            }

            container.innerHTML = users.map(u => `
                <div class="flex items-center gap-3 p-3 rounded-lg bg-red-500/5 border border-red-500/20">
                    <div class="text-2xl">⚠️</div>
                    <div class="flex-1">
                        <p class="font-semibold text-primary">${u.user_name}</p>
                        <p class="text-xs text-muted">${u.user_role}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold text-red-400">${u.error_count}</p>
                        <p class="text-xs text-muted">kesalahan</p>
                    </div>
                </div>
            `).join('');
        }

        function renderBreakdown(users) {
            const container = document.getElementById('breakdownChart');
            const typeLabels = {
                'auth': '🔐 Auth',
                'product': '📦 Produk',
                'order': '📋 Order',
                'stock': '📊 Stok',
                'pos': '🛒 POS',
                'import': '📥 Import',
                'qr': '🔳 QR',
                'user': ' User',
                'category': '️ Kategori',
                'supplier': ' Supplier',
            };

            // Aggregate all breakdowns
            const totals = {};
            users.forEach(u => {
                Object.entries(u.breakdown || {}).forEach(([type, count]) => {
                    totals[type] = (totals[type] || 0) + count;
                });
            });

            const total = Object.values(totals).reduce((a, b) => a + b, 0);

            container.innerHTML = Object.entries(totals).map(([type, count]) => {
                const percentage = total > 0 ? Math.round((count / total) * 100) : 0;
                return `
                    <div class="p-3 rounded-lg bg-elevated border border-subtle">
                        <p class="text-xs text-muted mb-1">${typeLabels[type] || type}</p>
                        <p class="text-2xl font-bold text-primary">${count}</p>
                        <div class="mt-2 h-2 bg-zinc-800 rounded-full overflow-hidden">
                            <div class="h-full bg-indigo-500" style="width: ${percentage}%"></div>
                        </div>
                        <p class="text-xs text-muted mt-1">${percentage}%</p>
                    </div>
                `;
            }).join('');
        }

        // Load on page load
        document.addEventListener('DOMContentLoaded', () => loadProductivity('today'));
    </script>
    @endpush
</x-admin-layout>