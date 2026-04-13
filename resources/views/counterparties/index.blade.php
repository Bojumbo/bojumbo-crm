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
    <!-- Header Area -->
    <div class="flex items-end justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Counterparties</h1>
            <p class="text-notion-text-secondary mt-1">Керування клієнтами та компаніями на основі Static IDs.</p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="resetForm()" class="flex items-center gap-1.5 px-3 py-1.5 bg-notion-blue text-white rounded-notion text-sm font-medium hover:bg-blue-600 transition-colors shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                New
            </button>
        </div>
    </div>

    <!-- Quick Filters -->
    <div class="flex items-center gap-4 border-b border-notion-border pb-2 text-sm">
        <div class="flex items-center gap-1 px-2 py-1 hover:bg-notion-hover rounded cursor-pointer text-notion-text-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
            Filter
        </div>
        <div class="flex-1"></div>
        <p class="text-xs text-notion-text-secondary">{{ $counterparties->count() }} records</p>
    </div>

    <!-- Database Table -->
    <div class="overflow-x-auto">
        <table class="notion-table">
            <thead>
                <tr>
                    <th class="w-8"></th>
                    @foreach($columns as $column)
                        <th>{{ $column->label_uk ?? $column->label_en }} <span class="text-[9px] font-mono opacity-30">#{{ $column->static_id }}</span></th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($counterparties as $item)
                    @php
                        // Підготовка даних для Alpine.js
                        $rowFields = [];
                        foreach($columns as $col) {
                            $val = $item->getFieldValue($col->static_id);
                            if($col->field_type === 'user') {
                                $rowFields[$col->static_id] = json_decode($val, true) ?: ($val ? [$val] : []);
                            } else {
                                $rowFields[$col->static_id] = $val;
                            }
                        }
                    @endphp
                    <tr class="hover:bg-notion-hover transition-colors group">
                        <td class="text-center">
                             <span class="text-notion-text-mono italic text-[10px]">#{{ $item->id }}</span>
                        </td>
                        @foreach($columns as $column)
                            <td class="text-sm">
                                @php $val = $item->getFieldValue($column->static_id); @endphp
                                @if($column->field_key === 'name')
                                    <span @click="initRow({{ $item->id }}, {{ json_encode($rowFields) }})" class="font-medium hover:underline cursor-pointer text-notion-blue">{{ $val ?? 'Untitled' }}</span>
                                @elseif($column->field_type === 'user' && $val)
                                    @php 
                                        $managerIds = json_decode($val, true) ?: [$val];
                                        $managers = \App\Models\User::whereIn('id', $managerIds)->get();
                                    @endphp
                                    <div class="flex -space-x-1">
                                        @foreach($managers as $manager)
                                            <div title="{{ $manager->name }}" class="w-5 h-5 rounded-full bg-notion-blue/20 ring-1 ring-canvas flex items-center justify-center text-[9px] font-bold text-notion-blue">
                                                {{ substr($manager->name, 0, 1) }}
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif($column->field_type === 'enum')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-notion-blue/10 text-notion-blue border border-notion-blue/20">
                                        {{ ucfirst($val) }}
                                    </span>
                                @else
                                    <span class="text-notion-text-secondary">{{ $val }}</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="{{ $columns->count() + 1 }}" class="py-20 text-center opacity-40">No records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- NOTION SIDE PEEK -->
    <div 
        x-show="open" 
        class="fixed inset-y-0 right-0 w-[600px] bg-card border-l border-notion-border shadow-2xl z-50 flex flex-col"
        x-cloak
        @keydown.escape.window="open = false"
    >
        <!-- Header -->
        <div class="h-12 border-b border-notion-border px-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button @click="open = false" class="p-1 hover:bg-notion-hover rounded text-notion-text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m18 15-6-6-6 6"/></svg>
                </button>
                <span class="text-notion-text-secondary text-sm" x-text="editMode ? 'Edit Counterparty' : 'New Counterparty'"></span>
            </div>
            
            <div class="flex items-center gap-2">
                <template x-if="editMode">
                    <form :action="'/counterparties/' + currentId" method="POST" onsubmit="return confirm('Ви впевнені?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 hover:bg-red-500/10 text-red-400 rounded transition-colors" title="Delete">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                        </button>
                    </form>
                </template>
                <button @click="open = false" class="p-1.5 hover:bg-notion-hover rounded transition-colors text-notion-text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
        </div>

        <form :action="editMode ? '/counterparties/' + currentId : '{{ route('counterparties.store') }}'" method="POST" class="flex-1 overflow-y-auto p-12 space-y-8">
            @csrf
            <template x-if="editMode">@method('PATCH')</template>
            
            <div class="space-y-4">
                @foreach($columns as $column)
                    <div class="grid grid-cols-[140px,1fr] items-baseline group">
                        <label class="text-notion-text-secondary text-sm flex items-center gap-2">
                            {{ $column->label_en }}
                        </label>
                        
                        @if($column->field_key === 'type')
                            <select name="fields[{{ $column->static_id }}]" x-model="fields[{{ $column->static_id }}]" class="notion-input w-full text-sm">
                                <option value="individual">Individual</option>
                                <option value="company">Company</option>
                            </select>
                        @elseif($column->field_type === 'user')
                            <select name="fields[{{ $column->static_id }}][]" x-model="fields[{{ $column->static_id }}]" class="notion-input w-full text-sm" multiple size="3">
                                @foreach(\App\Models\User::all() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        @elseif($column->field_type === 'numeric')
                            <input type="number" step="0.01" name="fields[{{ $column->static_id }}]" x-model="fields[{{ $column->static_id }}]" class="notion-input w-full text-sm font-mono" placeholder="0.00">
                        @elseif($column->field_type === 'date')
                            <input type="date" name="fields[{{ $column->static_id }}]" x-model="fields[{{ $column->static_id }}]" class="notion-input w-full text-sm">
                        @else
                            <input 
                                type="text" 
                                name="fields[{{ $column->static_id }}]" 
                                x-model="fields[{{ $column->static_id }}]"
                                placeholder="Empty" 
                                class="notion-input w-full text-sm"
                            >
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="pt-8 flex justify-end">
                <button type="submit" class="bg-notion-blue px-4 py-1.5 rounded-notion text-sm font-medium hover:bg-blue-600 transition-all text-white">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <div x-show="open" @click="open = false" class="fixed inset-0 bg-[var(--color-overlay)] z-40" x-cloak></div>
</div>
@endsection
