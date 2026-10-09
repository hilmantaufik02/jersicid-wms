@props(['menus' => [], 'currentRoute' => ''])

<aside 
    x-data="{ sidebarOpen: window.innerWidth >= 1024 }" 
    x-bind:class="sidebarOpen ? 'w-64' : 'w-20'"
    class="flex flex-col bg-slate-900 border-r border-slate-800 transition-all duration-300 ease-in-out relative h-screen sticky top-0"
>
    <!-- Logo & Toggle -->
    <div class="flex items-center h-16 px-4 border-b border-slate-800 relative">
        <div class="flex items-center gap-3" x-show="sidebarOpen" x-transition>
            <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                <img src="{{ asset('asset/images/logo-jersic.png') }}" alt="Logo" class="w-full h-full object-contain"> 
            </div>
            <span class="text-lg font-bold text-white tracking-wider">JERSIC<span class="text-cyan-400">.WMS</span></span>
        </div>
        
        <!-- Hamburger Button -->
        <button 
            x-on:click="sidebarOpen = !sidebarOpen" 
            class="p-2 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-colors flex-shrink-0"
            x-bind:class="!sidebarOpen ? 'absolute left-1/2 -translate-x-1/2' : 'ml-auto'"
            title="Toggle Sidebar"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        @foreach($menus as $menu)
            @php
                // Safe handling untuk array atau object
                $menuName = is_array($menu) ? ($menu['name'] ?? '') : ($menu->name ?? '');
                $menuRoute = is_array($menu) ? ($menu['route'] ?? '') : ($menu->route ?? '');
                $menuIcon = is_array($menu) ? ($menu['icon'] ?? '') : ($menu->icon ?? '');
                
                $isActive = $currentRoute === $menuRoute;
                $activeClass = 'bg-cyan-500/20 text-cyan-400 border border-cyan-500/30';
                $inactiveClass = 'text-slate-400 hover:bg-slate-800 hover:text-white border border-transparent';
            @endphp

            <a 
                href="{{ route($menuRoute) }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all relative group {{ $isActive ? $activeClass : $inactiveClass }}"
                title="{{ $menuName }}"
            >
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $menuIcon }}"/>
                </svg>
                <span x-show="sidebarOpen" x-transition>{{ $menuName }}</span>
                
                <!-- Tooltip saat sidebar collapsed -->
                <div x-show="!sidebarOpen" class="absolute left-full ml-2 px-2 py-1 bg-slate-800 text-white text-xs rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-50 border border-slate-700">
                    {{ $menuName }}
                </div>
            </a>
        @endforeach
    </nav>

    <!-- User Profile + Tanggal di Bottom Sidebar -->
    <div class="px-3 py-4 border-t border-slate-800">
        <!-- Info User -->
        <div class="flex items-center gap-3 w-full px-3 py-2" x-bind:class="sidebarOpen ? '' : 'justify-center'">
            <img 
                src="{{ auth()->user()->profile_photo_url }}" 
                alt="{{ auth()->user()->name }}"
                class="w-8 h-8 rounded-full object-cover border-2 border-cyan-500/30 flex-shrink-0"
            />
            <div class="flex-1 text-left min-w-0" x-show="sidebarOpen" x-transition>
                <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-500 truncate">{{ auth()->user()->role }}</p>
            </div>
        </div>

        <!-- Tanggal & Jam Real-Time -->
        <div 
            x-show="sidebarOpen" 
            x-transition
            class="mt-3 pt-3 border-t border-slate-800/50 px-3"
        >
            <div 
                x-data="{ date: '' }" 
                x-init="
                    const updateDate = () => {
                        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                        const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        const now = new Date();
                        date = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
                    };
                    updateDate();
                    setInterval(updateDate, 60000);
                "
                class="flex items-center gap-2 text-xs text-slate-500"
            >
                <svg class="w-3.5 h-3.5 text-cyan-400/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="font-medium text-slate-400" x-text="date"></span>
            </div>
            <div 
                x-data="{ time: '' }" 
                x-init="
                    const updateTime = () => {
                        const now = new Date();
                        const hours = String(now.getHours()).padStart(2, '0');
                        const minutes = String(now.getMinutes()).padStart(2, '0');
                        time = `${hours}.${minutes} WIB`;
                    };
                    updateTime();
                    setInterval(updateTime, 1000);
                "
                class="flex items-center gap-2 text-xs text-slate-500 mt-1"
            >
                <svg class="w-3.5 h-3.5 text-cyan-400/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium text-slate-400 font-mono tracking-wider" x-text="time"></span>
            </div>
        </div>
    </div>
</aside>