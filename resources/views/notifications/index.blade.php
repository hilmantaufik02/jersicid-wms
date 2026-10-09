<x-admin-layout title="Notifikasi">
    <x-slot name="pageTitle">Notifikasi Aktivitas</x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-primary">Notifikasi</h2>
            <button onclick="markAllAsRead()" class="btn-ghost px-4 py-2 rounded-lg text-sm">
                Tandai Semua Sudah Dibaca
            </button>
        </div>

        <div class="space-y-3" id="notificationsList">
            @forelse($notifications as $notification)
            <div class="glass-panel p-4 {{ $notification->is_read ? 'opacity-60' : 'border-l-4 border-l-indigo-500' }}"
                data-id="{{ $notification->id }}">
                <div class="flex items-start gap-4">
                    <!-- Icon based on type -->
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                            {{ $notification->type === 'activity' ? 'bg-blue-500/10 text-blue-400' : '' }}
                            {{ $notification->type === 'alert' ? 'bg-red-500/10 text-red-400' : '' }}
                            {{ $notification->type === 'success' ? 'bg-emerald-500/10 text-emerald-400' : '' }}
                            {{ $notification->type === 'warning' ? 'bg-amber-500/10 text-amber-400' : '' }}
                            {{ $notification->type === 'info' ? 'bg-purple-500/10 text-purple-400' : '' }}">
                        @if($notification->type === 'activity')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        @elseif($notification->type === 'alert')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        @elseif($notification->type === 'success')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        @elseif($notification->type === 'warning')
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <h3 class="text-sm font-semibold text-primary">{{ $notification->title }}</h3>
                            <span class="text-xs text-muted">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-secondary">{{ $notification->message }}</p>

                        @if($notification->user)
                        <p class="text-xs text-muted mt-2">Oleh: {{ $notification->user->name }}</p>
                        @endif

                        @if(!$notification->is_read)
                        <button onclick="markAsRead({{ $notification->id }})"
                            class="text-xs text-indigo-400 hover:text-indigo-300 mt-2">
                            Tandai sudah dibaca
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="glass-panel p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <p class="text-muted">Belum ada notifikasi</p>
            </div>
            @endforelse
        </div>
    </div>

    @push('scripts')
    <script>
        function markAsRead(notificationId) {
            fetch(`/notifications/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelector(`[data-id="${notificationId}"]`).classList.add('opacity-60');
                        document.querySelector(`[data-id="${notificationId}"]`).classList.remove('border-l-4', 'border-l-indigo-500');
                    }
                });
        }

        function markAllAsRead() {
            fetch('/notifications/read-all', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
        }

        // Auto-refresh notifications every 30 seconds
        setInterval(() => {
            fetch('/notifications/unread-count')
                .then(response => response.json())
                .then(data => {
                    // Update badge count if exists
                    const badge = document.querySelector('#notificationBadge');
                    if (badge && data.count > 0) {
                        badge.textContent = data.count;
                        badge.classList.remove('hidden');
                    } else if (badge) {
                        badge.classList.add('hidden');
                    }
                });
        }, 30000);
    </script>
    @endpush
</x-admin-layout>