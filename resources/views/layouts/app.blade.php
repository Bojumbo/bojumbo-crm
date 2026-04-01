<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Bojumbo CRM') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.min.js"></script>
    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-canvas text-notion-text-primary selection:bg-notion-blue/30 overflow-hidden">
    <div class="flex h-screen w-full" x-data="{ sidebarOpen: true }">
        
        <!-- Sidebar -->
        <aside 
            class="notion-sidebar flex-shrink-0 flex flex-col transition-all duration-300 ease-in-out"
            :class="sidebarOpen ? 'w-[240px]' : 'w-0 -translate-x-full'"
        >
            <!-- Workspace Header -->
            <div class="px-3 py-4 flex items-center justify-between group">
                <div class="flex items-center gap-2 overflow-hidden">
                    <div class="w-5 h-5 bg-notion-blue rounded flex-shrink-0 flex items-center justify-center text-[10px] font-bold">B</div>
                    <span class="font-medium truncate text-sm">Bojumbo Workspace</span>
                </div>
                <button @click="sidebarOpen = false" class="opacity-0 group-hover:opacity-100 p-1 hover:bg-notion-hover rounded transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-notion-text-secondary"><path d="m15 18-6-6 6-6"/></svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-2 space-y-0.5 mt-2">
                <a href="{{ url('/') }}" class="flex items-center gap-2 px-3 py-1.5 {{ request()->is('/') ? 'bg-notion-hover' : '' }} hover:bg-notion-hover rounded group transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-notion-text-secondary"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    <span class="text-sm">Dashboard</span>
                </a>
                <a href="{{ route('deals.index') }}" class="flex items-center gap-2 px-3 py-1.5 {{ request()->routeIs('deals.*') ? 'bg-notion-hover' : '' }} hover:bg-notion-hover rounded group transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-notion-text-secondary"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/></svg>
                    <span class="text-sm">Deals</span>
                </a>
                <a href="{{ route('statistics.index') }}" class="flex items-center gap-2 px-3 py-1.5 {{ request()->routeIs('statistics.*') ? 'bg-notion-hover' : '' }} hover:bg-notion-hover rounded group transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-notion-text-secondary"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
                    <span class="text-sm">{{ __('Statistics') }}</span>
                </a>
                <a href="{{ route('counterparties.index') }}" class="flex items-center gap-2 px-3 py-1.5 {{ request()->routeIs('counterparties.*') ? 'bg-notion-hover' : '' }} hover:bg-notion-hover rounded group transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-notion-text-secondary"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <span class="text-sm">Counterparties</span>
                </a>
                <a href="{{ route('products.index') }}" class="flex items-center gap-2 px-3 py-1.5 {{ request()->routeIs('products.*') ? 'bg-notion-hover' : '' }} hover:bg-notion-hover rounded group transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-notion-text-secondary"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                    <span class="text-sm">Products</span>
                </a>
            </nav>

            <!-- Admin Section -->
            <div class="px-3 mt-4 mb-2">
                <p class="text-[10px] uppercase tracking-wider text-notion-text-secondary font-bold px-3">Admin</p>
            </div>
                <div class="space-y-0.5">
                    <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-2 px-3 py-1.5 rounded-notion text-sm hover:bg-notion-hover {{ request()->routeIs('admin.*') ? 'bg-notion-hover text-white' : 'text-notion-text-secondary' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                        {{ __('Settings') }}
                    </a>
                </div>

            <!-- User Bio & Logout -->
            <div class="p-2 border-t border-notion-border space-y-0.5">
                <div class="px-3 py-2 flex items-center gap-3">
                    <div class="w-6 h-6 rounded bg-notion-blue/20 flex items-center justify-center text-[10px] font-bold text-notion-blue uppercase">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-[12px] font-medium truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-notion-text-secondary truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-1.5 hover:bg-red-500/10 text-notion-text-secondary hover:text-red-400 rounded transition-colors text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                        <span>Log out</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col h-full overflow-hidden relative min-w-0">
            <!-- Topbar / Breadcrumbs -->
            <header class="h-11 px-4 flex items-center justify-between flex-shrink-0 z-10">
                <div class="flex items-center gap-2 overflow-hidden">
                    <button @click="sidebarOpen = true" x-show="!sidebarOpen" x-cloak class="p-1 hover:bg-notion-hover rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" x2="21" y1="6" y2="6"/><line x1="3" x2="21" y1="12" y2="12"/><line x1="3" x2="21" y1="18" y2="18"/></svg>
                    </button>
                    <div class="flex items-center gap-1 text-sm overflow-hidden whitespace-nowrap">
                        <span class="text-notion-text-secondary">Workspace</span>
                        <span class="text-notion-text-secondary">/</span>
                        <span class="font-medium">Dashboard</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="text-xs text-notion-text-secondary">Edited 2m ago</div>
                    <button class="text-sm font-medium px-2 py-0.5 hover:bg-notion-hover rounded transition-colors">Share</button>
                    
                    <!-- View Options Dropdown -->
                    <div class="relative" x-data="{ menuOpen: false }">
                        <button @click="menuOpen = !menuOpen" class="p-1 hover:bg-notion-hover rounded text-notion-text-secondary transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg>
                        </button>
                        <div x-show="menuOpen" @click.outside="menuOpen = false" x-cloak
                             class="absolute right-0 top-full mt-2 w-56 bg-card border border-notion-border rounded-notion shadow-2xl z-50 p-1 flex flex-col">
                            @stack('header_menu')
                            <div class="h-px bg-notion-border my-1"></div>
                            <button class="flex items-center gap-2 px-3 py-1.5 text-sm rounded hover:bg-notion-hover text-notion-text-secondary transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                                {{ __('Export') }}
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 overflow-y-auto preview-scrollbar">
                <div class="max-w-[1200px] mx-auto px-12 py-12 lg:px-24 relative">
                    @if(session('automation_success'))
                        <div x-data="{ show: true }" 
                             x-show="show" 
                             x-init="setTimeout(() => show = false, 5000)"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-4"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-300"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-4"
                             class="fixed bottom-8 right-8 z-[100] flex items-center gap-3 bg-notion-blue text-white px-4 py-3 rounded-notion shadow-2xl border border-white/20 animate-bounce-subtle">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            <span class="text-sm font-medium">{{ session('automation_success') }}</span>
                            <button @click="show = false" class="ml-2 hover:opacity-70 transition-opacity">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        </div>
                    @endif
                    @yield('content')
                </div>
            </div>
        </main>

    </div>

    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbar for content */
        .preview-scrollbar::-webkit-scrollbar {
            width: 10px;
        }
        .preview-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .preview-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid transparent;
            background-clip: padding-box;
            border-radius: 10px;
        }
        .preview-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid transparent;
            background-clip: padding-box;
        }
    </style>
</body>
</html>
