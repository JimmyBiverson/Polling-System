<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kenya Election Tally') — Kenya National Polling System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="bg-[#f4f7f5] text-gray-950 antialiased min-h-screen flex flex-col">

<div x-data="{ mobileMenuOpen: false }" @keydown.escape.window="mobileMenuOpen = false" class="min-h-screen flex flex-col bg-[#f4f7f5]">

    {{-- Top Bar --}}
    <header class="bg-gradient-to-r from-emerald-950 via-green-900 to-emerald-950 text-white shadow-xl relative overflow-hidden z-30">
        <div class="absolute inset-0 opacity-15 pointer-events-none">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-red-600"></div>
            <div class="absolute top-1.5 left-0 w-full h-1.5 bg-black"></div>
            <div class="absolute top-3 left-0 w-full h-1.5 bg-white"></div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-emerald-800/80 border border-emerald-400/40 rounded-2xl flex items-center justify-center text-xl shadow-inner backdrop-blur-md">
                        <svg class="w-6 h-6 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-base sm:text-xl font-extrabold tracking-tight text-white flex items-center gap-2">
                            <span>Kenya Election Tally</span>
                            <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        </h1>
                        <p class="text-amber-300 text-xs sm:text-sm font-bold tracking-wide">National Polling System — Kakamega County</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @auth
                        {{-- Desktop User Badge & Logout --}}
                        <div class="hidden md:flex items-center gap-3">
                            <div class="flex items-center gap-2 bg-emerald-900/90 px-3.5 py-1.5 rounded-xl border border-emerald-700/60 shadow-inner">
                                <span class="text-xs font-black uppercase tracking-wider px-2 py-0.5 rounded-md shadow-sm {{ auth()->user()->isSuperAdmin() ? 'bg-amber-400 text-gray-950 font-extrabold' : (auth()->user()->isAdmin() ? 'bg-blue-400 text-gray-950 font-extrabold' : 'bg-gray-200 text-gray-900 font-extrabold') }}">
                                    {{ auth()->user()->role === 'super_admin' ? '👑 Super Admin' : (auth()->user()->role === 'county_admin' ? '🏢 County Admin' : '👤 Field Agent') }}
                                </span>
                                <span class="text-white text-sm font-bold">{{ auth()->user()->name }}</span>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="bg-red-600/90 hover:bg-red-600 text-white text-xs font-bold px-3.5 py-2 rounded-xl border border-red-500/40 shadow-sm transition-all flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Logout
                                </button>
                            </form>
                        </div>

                        {{-- Mobile Hamburger Trigger Button --}}
                        <button @click="mobileMenuOpen = true" type="button" aria-label="Open navigation menu" :aria-expanded="mobileMenuOpen.toString()" aria-controls="mobile-navigation" class="md:hidden bg-emerald-800/80 hover:bg-emerald-700 text-white p-2.5 rounded-xl border border-emerald-500/50 shadow-md focus:outline-none flex items-center gap-2">
                            <span class="text-xs font-bold text-amber-300">Menu</span>
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    {{-- Desktop Navigation Bar --}}
    @auth
    <nav class="hidden md:block bg-white border-b border-gray-200 shadow-sm sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex gap-2 py-2.5">
                @php $role = auth()->user()->role; @endphp

                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-extrabold transition-all whitespace-nowrap {{ request()->routeIs('dashboard') ? 'bg-emerald-700 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard Analytics
                </a>

                <a href="{{ route('votes.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-extrabold transition-all whitespace-nowrap {{ request()->routeIs('votes.*') ? 'bg-emerald-700 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Submit Vote Report
                </a>

                @if($role === 'super_admin' || $role === 'county_admin')
                <a href="{{ route('reports.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-extrabold transition-all whitespace-nowrap {{ request()->routeIs('reports.*') ? 'bg-emerald-700 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Electoral Reports
                </a>

                <a href="{{ route('manage.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-extrabold transition-all whitespace-nowrap {{ request()->routeIs('manage.*') ? 'bg-amber-600 text-white shadow-md' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Manage & Governance
                </a>
                @endif
            </div>
        </div>
    </nav>

    {{-- Sliding Mobile Off-Canvas Hamburger Drawer Navigation --}}
    <div x-show="mobileMenuOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 md:hidden"
         x-cloak>
        {{-- Backdrop --}}
        <div @click="mobileMenuOpen = false" class="fixed inset-0 bg-gray-950/70 backdrop-blur-sm"></div>

        {{-- Off-Canvas Panel --}}
        <div id="mobile-navigation" role="dialog" aria-modal="true" aria-label="Main navigation" class="fixed inset-y-0 right-0 w-[min(86vw,22rem)] max-w-xs bg-gray-950 text-white shadow-2xl p-5 sm:p-6 flex flex-col justify-between border-l border-emerald-800/50 z-50 transform transition-transform duration-300">
            <div class="space-y-6">
                {{-- Drawer Header --}}
                <div class="flex items-center justify-between border-b border-gray-800 pb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-black text-sm">
                            🇰🇪
                        </div>
                        <span class="font-extrabold text-base text-amber-300">Navigation Menu</span>
                    </div>
                    <button @click="mobileMenuOpen = false" type="button" aria-label="Close navigation menu" class="text-gray-300 hover:text-white p-2 rounded-lg bg-gray-800">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- User Account Mobile Badge --}}
                <div class="bg-gray-800/90 rounded-2xl p-4 border border-gray-700/80 shadow-md">
                    <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider mb-1">Signed in as</p>
                    <p class="font-black text-white text-base truncate">{{ auth()->user()->name }}</p>
                    <div class="mt-2">
                        <span class="inline-block px-3 py-1 rounded-lg text-xs font-black tracking-wide shadow-sm {{ auth()->user()->isSuperAdmin() ? 'bg-amber-400 text-gray-950' : (auth()->user()->isAdmin() ? 'bg-blue-400 text-gray-950' : 'bg-emerald-400 text-gray-950') }}">
                            {{ auth()->user()->role === 'super_admin' ? '👑 Super Admin' : (auth()->user()->role === 'county_admin' ? '🏢 County Admin' : '👤 Field Agent') }}
                        </span>
                    </div>
                </div>

                {{-- Navigation Links --}}
                <div class="space-y-2">
                    @php $role = auth()->user()->role; @endphp

                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white shadow-lg' : 'text-gray-300 hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard Analytics
                    </a>

                    <a href="{{ route('votes.create') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all {{ request()->routeIs('votes.*') ? 'bg-emerald-600 text-white shadow-lg' : 'text-gray-300 hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Submit Vote Report
                    </a>

                    @if($role === 'super_admin' || $role === 'county_admin')
                    <a href="{{ route('reports.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all {{ request()->routeIs('reports.*') ? 'bg-emerald-600 text-white shadow-lg' : 'text-gray-300 hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Electoral Reports
                    </a>

                    <a href="{{ route('manage.index') }}"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold text-sm transition-all {{ request()->routeIs('manage.*') ? 'bg-amber-600 text-white shadow-lg' : 'text-gray-300 hover:bg-gray-800' }}">
                        <svg class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Manage & Governance
                    </a>
                    @endif
                </div>
            </div>

            {{-- Logout Button in Mobile Drawer --}}
            <div class="border-t border-gray-800 pt-4 mt-6">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-extrabold py-3 px-4 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Sign Out of System
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endauth

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-cloak>
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="text-green-600 hover:text-green-800">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-cloak>
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
            <button @click="show = false" class="text-red-600 hover:text-red-800">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
    @endif

    {{-- Main Content --}}
    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t border-gray-200 py-4 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-xs text-gray-400">Kenya National Polling System v1.0 — Secure. Transparent. Trusted.</p>
            <p class="text-xs text-gray-300 mt-1">Built for the 2027 General Election</p>
        </div>
    </footer>

    {{-- Alpine.js & Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('scripts')
</div>
</body>
</html>
