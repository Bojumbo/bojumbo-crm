@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <header>
        <h1 class="text-4xl font-bold tracking-tight">Dashboard</h1>
        <p class="text-notion-text-secondary mt-2 text-lg">Ласкаво просимо до Bojumbo CRM. Система активована з архітектурою Dynamic Fields.</p>
    </header>

    <!-- Numeric Field-First Demo -->
    <section class="space-y-4">
        <div class="flex items-center gap-2 group cursor-default">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-notion-text-secondary"><path d="m16 13 5.23-5.23a2.38 2.38 0 0 0 0-3.37l-.4-.4a2.38 2.38 0 0 0-3.37 0L12 9.23"/><path d="M7 21h5"/><path d="M12.58 11.42 14 10l4 4-1.42 1.42"/><path d="m6 12-3 3s1.5 7 3.5 7 5.5-3 5.5-3s-3-5.5-3-5.5z"/><path d="m12.58 11.42-.42-.42c-1.33-1.33-4.14-1.63-5.32-.45l-1.29 1.29s3.5 3.5 3.5 3.5l1.29-1.29c1.18-1.18.89-3.99-.44-5.32z"/></svg>
            <h2 class="text-xl font-semibold">Системні Static IDs</h2>
        </div>

        <div class="bg-notion-hover/30 rounded-notion p-1 border border-notion-border">
            <table class="notion-table">
                <thead>
                    <tr>
                        <th>Static ID</th>
                        <th>Entity</th>
                        <th>Key</th>
                        <th>Type</th>
                        <th>System</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $fields = \App\Models\FieldMetadata::all();
                    @endphp
                    @forelse($fields as $field)
                        <tr class="hover:bg-notion-hover/50 transition-colors group">
                            <td class="font-mono text-notion-blue text-sm">{{ $field->static_id }}</td>
                            <td class="text-notion-text-secondary">{{ ucfirst($field->entity) }}</td>
                            <td><code class="bg-white/5 px-1.5 py-0.5 rounded text-xs">{{ $field->field_key }}</code></td>
                            <td>
                                <span class="text-xs px-2 py-0.5 rounded-full bg-white/5 border border-white/5">
                                    {{ $field->field_type }}
                                </span>
                            </td>
                            <td>
                                @if($field->is_system)
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-500/70"><path d="M20 6 9 17l-5-5"/></svg>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-notion-text-secondary">
                                Дані не знайдено. Будь ласка, запустіть <code>php artisan db:seed</code>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        <div class="p-4 rounded-notion border border-notion-border bg-card hover:bg-notion-hover transition-all cursor-pointer group">
            <div class="text-notion-text-secondary text-xs font-medium uppercase mb-1">Total Fields</div>
            <div class="text-2xl font-bold tracking-tight">{{ $fields->count() }}</div>
        </div>
        <div class="p-4 rounded-notion border border-notion-border bg-card hover:bg-notion-hover transition-all cursor-pointer group">
            <div class="text-notion-text-secondary text-xs font-medium uppercase mb-1">Active Entities</div>
            <div class="text-2xl font-bold tracking-tight">{{ $fields->pluck('entity')->unique()->count() }}</div>
        </div>
        <div class="p-4 rounded-notion border border-notion-border bg-card hover:bg-notion-hover transition-all cursor-pointer group">
            <div class="text-notion-text-secondary text-xs font-medium uppercase mb-1">Google Templates</div>
            <div class="text-2xl font-bold tracking-tight">0</div>
        </div>
    </div>
</div>
@endsection
