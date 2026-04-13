@extends('layouts.app')

@section('content')
<div x-data="{ 
    open: false, 
    editMode: false,
    currentId: null,
    fields: {},
    
    initRow(id, rowFields) {
        this.editMode = true;
        this.currentId = id;
        this.fields = rowFields;
        this.open = true;
    },
    
    resetForm() {
        this.editMode = false;
        this.currentId = null;
        this.fields = {};
        this.open = true;
    }
}" class="space-y-6">
    <!-- Header -->
    <div class="flex items-end justify-between px-2">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">{{ __('Products') }}</h1>
            <p class="text-notion-text-secondary mt-1">{{ __('Catalog of products and services with SKU and pricing.') }}</p>
        </div>
        <button @click="resetForm()" class="flex items-center gap-1.5 px-3 py-1.5 bg-notion-blue text-white rounded-notion text-sm font-medium hover:bg-blue-600 transition-colors shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            {{ __('Add product') }}
        </button>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="notion-table">
            <thead>
                <tr class="text-inline-start">
                    <th class="w-12 text-center">{{ __('Icon') }}</th>
                    @foreach($columns as $column)
                        <th class="ps-4 pe-4">{{ $column->label_uk ?? $column->label_en }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($products as $item)
                    @php
                        $rowFields = [];
                        foreach($columns as $col) {
                            $rowFields[$col->static_id] = $item->getFieldValue($col->static_id);
                        }
                    @endphp
                    <tr class="hover:bg-notion-hover transition-colors group">
                        <td class="py-2 text-center">
                            @php $photo = $item->getFieldValue(3005); @endphp
                            @if($photo)
                                <img src="{{ $photo }}" class="w-8 h-8 rounded border border-notion-border object-cover mx-auto">
                            @else
                                <div class="w-8 h-8 rounded border border-notion-border bg-notion-hover flex items-center justify-center mx-auto">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-notion-text-mono"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                </div>
                            @endif
                        </td>
                        @foreach($columns as $column)
                            <td class="text-sm ps-4 pe-4">
                                @php $val = $item->getFieldValue($column->static_id); @endphp
                                @if($column->field_key === 'name')
                                    <span @click="initRow({{ $item->id }}, {{ json_encode($rowFields) }})" class="font-medium hover:underline cursor-pointer text-notion-blue">
                                        {{ $val ?? __('Untitled Product') }}
                                    </span>
                                @elseif($column->field_key === 'price' || $column->static_id == 3003)
                                    <span class="font-mono text-green-500/80">{{ $currency }}{{ number_format($val ?? 0, 2) }}</span>
                                @elseif($column->field_key === 'sku')
                                    <span class="bg-notion-hover px-1.5 py-0.5 rounded font-mono text-xs text-notion-text-secondary">{{ $val }}</span>
                                @else
                                    <span class="text-notion-text-secondary">{{ Str::limit($val, 40) }}</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="{{ $columns->count() + 1 }}" class="py-20 text-center opacity-40">{{ __('No products found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Side Peek Form -->
    <div x-show="open" 
         class="fixed inset-y-0 inset-inline-end-0 w-[600px] bg-card border-inline-start border-notion-border shadow-2xl z-50 flex flex-col" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         x-cloak>
        <div class="h-12 border-b border-notion-border ps-4 pe-4 flex items-center justify-between">
            <span class="text-sm font-medium" x-text="editMode ? '{{ __('Edit Product') }}' : '{{ __('New Product') }}'"></span>
            <div class="flex items-center gap-2">
                <template x-if="editMode">
                    <form :action="'/products/' + currentId" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 hover:bg-red-500/10 text-red-400 rounded transition-colors" onclick="return confirm('{{ __('Delete this product?') }}')"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg></button>
                    </form>
                </template>
                <button @click="open = false" class="p-1.5 hover:bg-notion-hover rounded transition-colors text-notion-text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
        </div>

        <form :action="editMode ? '/products/' + currentId : '{{ route('products.store') }}'" method="POST" class="flex-1 overflow-y-auto ps-12 pe-12 py-12 space-y-6">
            @csrf
            <template x-if="editMode">@method('PATCH')</template>

            <div class="space-y-4">
                @foreach($columns as $column)
                    <div class="grid grid-cols-[140px,1fr] items-baseline gap-4">
                        <label class="text-notion-text-secondary text-[11px] font-bold uppercase tracking-wider">{{ $column->label_en }}</label>
                        @if($column->field_key === 'description')
                            <textarea name="fields[{{ $column->static_id }}]" x-model="fields[{{ $column->static_id }}]" class="notion-input w-full text-sm h-32 resize-none" placeholder="{{ __('Description...') }}"></textarea>
                        @elseif($column->field_type === 'numeric')
                            <div class="relative">
                                <span class="absolute isolate-inline-start-3 top-1/2 -translate-y-1/2 text-notion-text-secondary text-xs">{{ $currency }}</span>
                                <input type="number" step="0.01" name="fields[{{ $column->static_id }}]" x-model="fields[{{ $column->static_id }}]" class="notion-input w-full text-sm font-mono ps-7" placeholder="0.00">
                            </div>
                        @elseif($column->field_type === 'date')
                            <input type="date" name="fields[{{ $column->static_id }}]" x-model="fields[{{ $column->static_id }}]" class="notion-input w-full text-sm">
                        @else
                            <input type="text" name="fields[{{ $column->static_id }}]" x-model="fields[{{ $column->static_id }}]" class="notion-input w-full text-sm" placeholder="{{ __('Empty') }}">
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="pt-8 flex justify-inline-end">
                <button type="submit" class="bg-notion-blue px-6 py-2 rounded-notion text-sm font-medium text-white hover:bg-blue-600 shadow-lg shadow-blue-500/10">
                    {{ __('Save Product') }}
                </button>
            </div>
        </form>
    </div>
    <div x-show="open" @click="open = false" class="fixed inset-0 bg-[var(--color-overlay)] z-40" x-cloak></div>
</div>
@endsection
