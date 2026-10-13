<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} — Party Management System</title>

    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        (function() {
            var savedTheme = localStorage.getItem('pms_theme') || 'default';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-50 flex flex-col selection:bg-indigo-500 selection:text-white">

    <!-- Toast Notification Banner -->
    <div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none">
        @if(session('toast_success'))
            <div class="pointer-events-auto flex items-start gap-3 p-4 bg-white border border-emerald-200/80 rounded-xl shadow-lg shadow-emerald-500/10 text-slate-800 transition transform duration-300">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div class="flex-1 pt-0.5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600">Success</p>
                    <p class="text-sm text-slate-700 mt-0.5">{{ session('toast_success') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 text-sm p-1">&times;</button>
            </div>
        @endif

        @if(session('toast_info'))
            <div class="pointer-events-auto flex items-start gap-3 p-4 bg-white border border-blue-200/80 rounded-xl shadow-lg shadow-blue-500/10 text-slate-800 transition transform duration-300">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="flex-1 pt-0.5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">Notice</p>
                    <p class="text-sm text-slate-700 mt-0.5">{{ session('toast_info') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 text-sm p-1">&times;</button>
            </div>
        @endif

        @if($errors->any())
            <div class="pointer-events-auto flex items-start gap-3 p-4 bg-white border border-rose-200/80 rounded-xl shadow-lg shadow-rose-500/10 text-slate-800 transition transform duration-300">
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div class="flex-1 pt-0.5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-rose-600">Validation Notice</p>
                    <p class="text-sm text-slate-700 mt-0.5">{{ $errors->first() }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 text-sm p-1">&times;</button>
            </div>
        @endif
    </div>

    <div class="flex h-screen overflow-hidden">

        <!-- Desktop Sidebar -->
        <aside class="hidden lg:flex lg:flex-col w-64 bg-white border-r border-slate-200/80 z-20 shrink-0">
            <!-- Brand Header -->
            <div class="h-16 flex items-center justify-between px-6 border-b border-slate-100">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-indigo-500 flex items-center justify-center text-white shadow-sm shadow-indigo-500/20 group-hover:scale-105 transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-sm font-bold tracking-tight text-slate-900 block leading-tight">PartyManager</span>
                        <span class="text-[11px] font-medium text-slate-400 uppercase tracking-wider">Nearby Finder</span>
                    </div>
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto px-4 py-6 space-y-1.5">
                <div class="px-2 pb-2 text-[11px] font-semibold tracking-wider uppercase text-slate-400">Core Menu</div>

                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-indigo-50/80 text-indigo-600 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('parties.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('parties.index') ? 'bg-indigo-50/80 text-indigo-600 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('parties.index') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span>All Parties</span>
                </a>

                <a href="{{ route('parties.map') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('parties.map') ? 'bg-indigo-50/80 text-indigo-600 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('parties.map') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                    <span>Interactive Map</span>
                </a>

                <a href="{{ route('import.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('import.*') ? 'bg-indigo-50/80 text-indigo-600 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('import.*') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span>Import Excel / CSV</span>
                </a>

                <div class="pt-6 px-2 pb-2 text-[11px] font-semibold tracking-wider uppercase text-slate-400">Management</div>

                <a href="{{ route('parties.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('parties.create') ? 'bg-indigo-50/80 text-indigo-600 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('parties.create') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Add New Party</span>
                </a>

                <a href="{{ route('recycle_bin.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('recycle_bin.*') ? 'bg-indigo-50/80 text-indigo-600 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('recycle_bin.*') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Recycle Bin</span>
                </a>

                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('profile.*') ? 'bg-indigo-50/80 text-indigo-600 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('profile.*') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span>Admin Profile</span>
                </a>
            </div>

            <!-- User Profile & Logout -->
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                <div class="flex items-center justify-between">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 min-w-0 group flex-1 mr-2" title="View & Edit Profile">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs flex items-center justify-center shrink-0 ring-2 ring-white group-hover:bg-indigo-200 transition">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="truncate">
                            <p class="text-xs font-semibold text-slate-800 truncate group-hover:text-indigo-600 transition">{{ auth()->user()->name ?? 'Super Admin' }}</p>
                            <p class="text-[11px] text-slate-400 truncate">{{ auth()->user()->email ?? 'infiniteatpc7@gmail.com' }}</p>
                        </div>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                        @csrf
                        <button type="submit" title="Logout" class="desktop-logout-btn p-1.5 rounded-lg border transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            <!-- Top Header Bar -->
            <header class="h-16 bg-white border-b border-slate-200/80 px-3 sm:px-6 flex items-center justify-between relative z-[2000] shrink-0">
                <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1 mr-2">
                    <!-- Mobile Hamburger -->
                    <button id="mobile-menu-btn" type="button" class="lg:hidden p-1.5 sm:p-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div class="min-w-0">
                        <h1 class="text-sm sm:text-lg font-bold text-slate-900 truncate tracking-tight">{{ $header ?? 'Dashboard' }}</h1>
                    </div>
                </div>

                <!-- Header Actions & Live GPS Status -->
                <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
                    <!-- GPS Status Pill / Detector Button -->
                    <div id="gps-status-container" class="shrink-0">
                        <button id="detect-gps-btn" onclick="detectUserLocation()" type="button" class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1.5 rounded-full text-xs font-medium border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 shadow-xs transition hover:border-slate-300 whitespace-nowrap shrink-0">
                            <span id="gps-dot" class="w-2 h-2 rounded-full bg-slate-400 shrink-0"></span>
                            <span id="gps-label" class="whitespace-nowrap"><span class="hidden sm:inline">Detect GPS</span><span class="sm:hidden">GPS</span></span>
                        </button>
                    </div>

                    <!-- Theme Selector Dropdown (Classic, Black Iris, Shadow Citrus) -->
                    <x-theme-switcher />

                    <!-- Quick Add Party Button -->
                    <a href="{{ route('parties.create') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold shadow-xs shadow-indigo-600/20 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Add Party</span>
                    </a>

                    <!-- Quick Import Button -->
                    <a href="{{ route('import.index') }}" class="hidden md:inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold border border-slate-200/60 transition">
                        <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <span>Import</span>
                    </a>
                </div>
            </header>

            <!-- Main Content Area with Bottom Padding on Mobile for Bottom Nav -->
            <main class="flex-1 {{ request()->routeIs('parties.map') ? 'overflow-hidden pb-16 lg:pb-0 flex flex-col' : 'overflow-y-auto pb-20 lg:pb-8' }} bg-slate-50">
                {{ $slot }}
            </main>

            <!-- Mobile Sticky Bottom Navigation -->
            <nav class="lg:hidden fixed bottom-0 left-0 right-0 h-16 bg-white/95 backdrop-blur-md border-t border-slate-200/80 z-50 px-2 flex items-center justify-around shadow-lg">
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center flex-1 py-1 text-center {{ request()->routeIs('dashboard') ? 'text-indigo-600 font-semibold' : 'text-slate-400 hover:text-slate-600' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span class="text-[10px] mt-1">Home</span>
                </a>

                <a href="{{ route('parties.index') }}" class="flex flex-col items-center justify-center flex-1 py-1 text-center {{ request()->routeIs('parties.index') ? 'text-indigo-600 font-semibold' : 'text-slate-400 hover:text-slate-600' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span class="text-[10px] mt-1">Parties</span>
                </a>

                <a href="{{ route('parties.map') }}" class="flex flex-col items-center justify-center flex-1 py-1 text-center {{ request()->routeIs('parties.map') ? 'text-indigo-600 font-semibold' : 'text-slate-400 hover:text-slate-600' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                    <span class="text-[10px] mt-1">Map</span>
                </a>

                <a href="{{ route('import.index') }}" class="flex flex-col items-center justify-center flex-1 py-1 text-center {{ request()->routeIs('import.*') ? 'text-indigo-600 font-semibold' : 'text-slate-400 hover:text-slate-600' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span class="text-[10px] mt-1">Import</span>
                </a>

                <button id="mobile-more-btn" type="button" class="flex flex-col items-center justify-center flex-1 py-1 text-center text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <span class="text-[10px] mt-1">More</span>
                </button>
            </nav>

        </div>
    </div>

    <!-- Mobile Slide-out Drawer -->
    <div id="mobile-drawer" class="fixed inset-0 z-[9999] pointer-events-none transform -translate-x-full transition-transform duration-300 ease-in-out lg:hidden">
        <div id="mobile-drawer-backdrop" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs pointer-events-auto"></div>
        <div class="relative w-72 max-w-[85vw] bg-white h-full flex flex-col justify-between p-6 pointer-events-auto z-10 overflow-y-auto">
            <div>
                <div class="flex items-center justify-between pb-6 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-bold">P</div>
                        <span class="font-bold text-slate-900">PartyManager</span>
                    </div>
                    <button id="close-drawer-btn" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="mt-6 space-y-2">
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-xl text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-indigo-600 bg-indigo-50 font-semibold' : 'text-slate-700 hover:bg-slate-50' }}">Dashboard</a>
                    <a href="{{ route('parties.index') }}" class="block px-3 py-2 rounded-xl text-sm font-medium {{ request()->routeIs('parties.index') ? 'text-indigo-600 bg-indigo-50 font-semibold' : 'text-slate-700 hover:bg-slate-50' }}">All Parties</a>
                    <a href="{{ route('parties.map') }}" class="block px-3 py-2 rounded-xl text-sm font-medium {{ request()->routeIs('parties.map') ? 'text-indigo-600 bg-indigo-50 font-semibold' : 'text-slate-700 hover:bg-slate-50' }}">Interactive Map</a>
                    <a href="{{ route('parties.create') }}" class="block px-3 py-2 rounded-xl text-sm font-medium {{ request()->routeIs('parties.create') ? 'text-indigo-600 bg-indigo-50 font-semibold' : 'text-slate-700 hover:bg-slate-50' }}">Add Party</a>
                    <a href="{{ route('import.index') }}" class="block px-3 py-2 rounded-xl text-sm font-medium {{ request()->routeIs('import.*') ? 'text-indigo-600 bg-indigo-50 font-semibold' : 'text-slate-700 hover:bg-slate-50' }}">Import Excel</a>
                    <a href="{{ route('recycle_bin.index') }}" class="block px-3 py-2 rounded-xl text-sm font-medium {{ request()->routeIs('recycle_bin.*') ? 'text-indigo-600 bg-indigo-50 font-semibold' : 'text-slate-700 hover:bg-slate-50' }}">Recycle Bin</a>
                    <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-xl text-sm font-medium {{ request()->routeIs('profile.*') ? 'text-indigo-600 bg-indigo-50 font-semibold' : 'text-slate-700 hover:bg-slate-50' }}">Admin Profile</a>
                </div>

                <div class="mt-6">
                    <x-theme-switcher :mobile="true" />
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100">
                <p class="text-xs text-slate-400 mb-2">Signed in as <a href="{{ route('profile.edit') }}" class="font-semibold text-slate-700 hover:text-indigo-600 transition">{{ auth()->user()->name ?? 'Super Admin' }}</a></p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="theme-logout-btn w-full py-2.5 px-4 rounded-xl text-xs font-semibold transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Sign Out</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Client Geolocation & Mobile Scripts -->
    <script>
        // Drawer toggle
        const drawer = document.getElementById('mobile-drawer');
        const openDrawer = () => {
            drawer.classList.remove('-translate-x-full');
            drawer.classList.remove('pointer-events-none');
        };
        const closeDrawer = () => {
            drawer.classList.add('-translate-x-full');
            drawer.classList.add('pointer-events-none');
        };

        document.getElementById('mobile-menu-btn')?.addEventListener('click', openDrawer);
        document.getElementById('mobile-more-btn')?.addEventListener('click', openDrawer);
        document.getElementById('close-drawer-btn')?.addEventListener('click', closeDrawer);
        document.getElementById('mobile-drawer-backdrop')?.addEventListener('click', closeDrawer);

        // Auto-detect and display GPS status
        function updateGpsUI(lat, lng) {
            const dot = document.getElementById('gps-dot');
            const label = document.getElementById('gps-label');
            const btn = document.getElementById('detect-gps-btn');
            if (dot && label) {
                dot.classList.remove('bg-slate-400', 'bg-rose-500', 'bg-amber-500');
                dot.classList.add('bg-emerald-500');
                const latFixed = parseFloat(lat).toFixed(2);
                const lngFixed = parseFloat(lng).toFixed(2);
                label.innerHTML = `<span class="hidden md:inline">GPS Active (${latFixed}, ${lngFixed})</span><span class="hidden sm:inline md:hidden">GPS (${latFixed}, ${lngFixed})</span><span class="sm:hidden font-semibold">GPS</span>`;
                if (btn) {
                    btn.title = `GPS Active: ${latFixed}, ${lngFixed} (Click to refresh location)`;
                }
            }
        }

        function detectUserLocation() {
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser.');
                return;
            }

            const label = document.getElementById('gps-label');
            if (label) {
                label.innerHTML = '<span class="hidden sm:inline">Detecting...</span><span class="sm:hidden">...</span>';
            }

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    sessionStorage.setItem('user_lat', lat);
                    sessionStorage.setItem('user_lng', lng);
                    updateGpsUI(lat, lng);
                    window.dispatchEvent(new CustomEvent('pms:location-updated', { detail: { lat, lng } }));

                    // If URL doesn't have lat/lng query params, reload with them
                    const url = new URL(window.location.href);
                    if (url.searchParams.get('lat') !== lat.toString() || url.searchParams.get('lng') !== lng.toString()) {
                        url.searchParams.set('lat', lat);
                        url.searchParams.set('lng', lng);
                        window.location.href = url.toString();
                    }
                },
                (error) => {
                    const dot = document.getElementById('gps-dot');
                    const label = document.getElementById('gps-label');
                    if (dot && label) {
                        dot.classList.remove('bg-emerald-500');
                        dot.classList.add('bg-amber-500');
                        label.innerHTML = '<span class="hidden sm:inline">GPS Denied</span><span class="sm:hidden">Denied</span>';
                    }
                    console.warn('Geolocation denied or unavailable:', error.message);
                },
                { enableHighAccuracy: true, timeout: 8000 }
            );
        }

        // Initialize GPS from URL or SessionStorage
        window.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const urlLat = urlParams.get('lat');
            const urlLng = urlParams.get('lng');

            if (urlLat && urlLng) {
                sessionStorage.setItem('user_lat', urlLat);
                sessionStorage.setItem('user_lng', urlLng);
                updateGpsUI(urlLat, urlLng);
                window.dispatchEvent(new CustomEvent('pms:location-updated', { detail: { lat: urlLat, lng: urlLng } }));
            } else {
                const storedLat = sessionStorage.getItem('user_lat');
                const storedLng = sessionStorage.getItem('user_lng');
                if (storedLat && storedLng) {
                    updateGpsUI(storedLat, storedLng);
                    window.dispatchEvent(new CustomEvent('pms:location-updated', { detail: { lat: storedLat, lng: storedLng } }));
                }
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
