<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- WAJIB untuk Alpine.js Fetch -->

    <title>{{ config('app.name', 'jersic.id WMS') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|jetbrains-mono:400,500,700" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-wms-bg text-wms-text font-sans antialiased">
    <div class="min-h-screen flex">
        
        <!-- Sidebar Dinamis -->
        @include('layouts.partials.sidebar')

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navbar -->
            @include('layouts.partials.navbar')

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-wms-bg p-6">
                <!-- Page Header -->
                <header class="mb-6">
                    <h1 class="text-2xl font-bold text-white">@yield('page-title', 'Dashboard')</h1>
                    <p class="text-wms-muted text-sm mt-1">@yield('page-subtitle', 'Selamat datang di sistem')</p>
                </header>

                <!-- Flash Messages -->
                @if (session('success'))
                    <div class="mb-4 p-4 bg-emerald-900/30 border border-emerald-500/50 text-emerald-400 rounded-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ session('success') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>