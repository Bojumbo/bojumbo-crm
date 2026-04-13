@extends('layouts.app')
@section('content')
<div class="max-w-[1200px] mx-auto space-y-8">
    <div class="flex items-end justify-between border-b border-notion-border pb-6">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">{{ __('CRM Settings') }}</h1>
            <p class="text-notion-text-secondary mt-1">{{ __('Manage your global system configurations and data structures.') }}</p>
        </div>
    </div>
    <div class="flex gap-8 items-start">
        <!-- Settings Sidebar -->
        <aside class="w-64 flex-shrink-0 space-y-1">
            <a href="{{ route('admin.settings.index') }}" 
               class="flex items-center gap-2 px-3 py-2 rounded-notion text-sm transition-colors {{ request()->routeIs('admin.settings.index') ? 'bg-notion-blue/10 text-notion-blue font-bold' : 'text-notion-text-secondary hover:bg-notion-hover hover:text-notion-text-primary' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                <span>{{ __('General Settings') }}</span>
            </a>
            <a href="{{ route('admin.pipelines.index') }}" 
               class="flex items-center gap-2 px-3 py-2 rounded-notion text-sm transition-colors {{ request()->routeIs('admin.pipelines.*') ? 'bg-notion-blue/10 text-notion-blue font-bold' : 'text-notion-text-secondary hover:bg-notion-hover hover:text-notion-text-primary' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                <span>{{ __('Pipelines & Stages') }}</span>
            </a>
            <a href="{{ route('admin.fields.index') }}" 
               class="flex items-center gap-2 px-3 py-2 rounded-notion text-sm transition-colors {{ request()->routeIs('admin.fields.*') ? 'bg-notion-blue/10 text-notion-blue font-bold' : 'text-notion-text-secondary hover:bg-notion-hover hover:text-notion-text-primary' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                <span>{{ __('Custom Fields') }}</span>
            </a>
            <a href="{{ route('admin.settings.templates') }}" 
               class="flex items-center gap-2 px-3 py-2 rounded-notion text-sm transition-colors {{ request()->routeIs('admin.settings.templates') ? 'bg-notion-blue/10 text-notion-blue font-bold' : 'text-notion-text-secondary hover:bg-notion-hover hover:text-notion-text-primary' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                <span>{{ __('Document Templates') }}</span>
            </a>
            <a href="{{ route('admin.settings.document_tables.index') }}" 
               class="flex items-center gap-2 px-3 py-2 rounded-notion text-sm transition-colors {{ request()->routeIs('admin.settings.document_tables.*') ? 'bg-notion-blue/10 text-notion-blue font-bold' : 'text-notion-text-secondary hover:bg-notion-hover hover:text-notion-text-primary' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M3 15h18"/><path d="M9 3v18"/></svg>
                <span>{{ __('Document Tables') }}</span>
            </a>
        </aside>
        <!-- Dynamic Content -->
        <main class="flex-1 min-w-0">
            @yield('settings_content')
        </main>
    </div>
</div>
@endsection