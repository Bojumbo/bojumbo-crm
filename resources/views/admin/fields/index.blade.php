@extends('admin.settings.layout')

@section('settings_content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="font-bold text-lg text-white">{{ __('Data Structure & Fields') }}</h3>
            <p class="text-[12px] text-notion-text-secondary">{{ __('Define properties and data types for different entities.') }}</p>
        </div>
        
        <div class="flex bg-card border border-notion-border rounded-notion p-1">
            <a href="?entity=deal" class="px-4 py-1.5 rounded-notion text-sm transition-all {{ $entity === 'deal' ? 'bg-white/10 text-white font-medium' : 'text-notion-text-secondary hover:text-white' }}">{{ __('Deals') }}</a>
            <a href="?entity=counterparty" class="px-4 py-1.5 rounded-notion text-sm transition-all {{ $entity === 'counterparty' ? 'bg-white/10 text-white font-medium' : 'text-notion-text-secondary hover:text-white' }}">{{ __('Counterparties') }}</a>
            <a href="?entity=product" class="px-4 py-1.5 rounded-notion text-sm transition-all {{ $entity === 'product' ? 'bg-white/10 text-white font-medium' : 'text-notion-text-secondary hover:text-white' }}">{{ __('Products') }}</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- New Field Form -->
        <div class="bg-card border border-notion-border rounded-notion p-6 self-start space-y-6">
            <h3 class="font-bold text-md text-white">{{ __('Add New Field') }}</h3>
            
            <form action="{{ route('admin.fields.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="entity" value="{{ $entity }}">
                
                <div class="space-y-1">
                    <label class="text-[10px] text-notion-text-secondary font-bold uppercase tracking-wider">{{ __('Field Label (EN)') }}</label>
                    <input type="text" name="label_en" required class="notion-input w-full" placeholder="{{ __('e.g. Lead Source') }}">
                </div>
                
                <div class="space-y-1">
                    <label class="text-[10px] text-notion-text-secondary font-bold uppercase tracking-wider">{{ __('Label (UK/UA)') }}</label>
                    <input type="text" name="label_uk" required class="notion-input w-full" placeholder="{{ __('Джерело ліда') }}">
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] text-notion-text-secondary font-bold uppercase tracking-wider">{{ __('Field Type') }}</label>
                    <select name="field_type" class="notion-input w-full bg-card">
                        <option value="text">{{ __('Text') }}</option>
                        <option value="numeric">{{ __('Number') }}</option>
                        <option value="date">{{ __('Date') }}</option>
                        <option value="enum">{{ __('Dropdown/Select') }}</option>
                    </select>
                </div>

                <button type="submit" class="w-full bg-notion-blue py-2 rounded-notion text-sm font-medium text-white hover:bg-blue-600 transition-all shadow-lg shadow-blue-500/10">
                    {{ __('Create Field') }}
                </button>
            </form>
        </div>

        <!-- Fields List -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-card border border-notion-border rounded-notion overflow-hidden shadow-sm">
                <table class="w-full text-[13px] text-inline-start">
                    <thead class="bg-white/5 border-b border-notion-border text-notion-text-secondary">
                        <tr>
                            <th class="ps-4 pe-4 py-3 font-medium w-16">ID</th>
                            <th class="ps-4 pe-4 py-3 font-medium">{{ __('Label') }}</th>
                            <th class="ps-4 pe-4 py-3 font-medium">{{ __('Type') }}</th>
                            <th class="ps-4 pe-4 py-3 font-medium">{{ __('Key') }}</th>
                            <th class="ps-4 pe-4 py-3 font-medium w-20 text-center">{{ __('System') }}</th>
                            <th class="ps-4 pe-4 py-3 font-medium w-12"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-notion-border/50">
                        @foreach($fields as $field)
                            <tr class="hover:bg-white/[0.02] group transition-colors">
                                <td class="ps-4 pe-4 py-3 font-mono text-[11px] text-notion-text-secondary">{{ $field->static_id }}</td>
                                <td class="ps-4 pe-4 py-3">
                                    <div class="font-medium text-white">{{ $field->label_en }}</div>
                                    <div class="text-[10px] text-notion-text-secondary">{{ $field->label_uk }}</div>
                                </td>
                                <td class="ps-4 pe-4 py-3">
                                    <span class="px-2 py-0.5 rounded bg-white/5 border border-notion-border text-[10px] uppercase font-bold tracking-tight opacity-70">
                                        {{ $field->field_type }}
                                    </span>
                                </td>
                                <td class="ps-4 pe-4 py-3 text-notion-text-secondary font-mono text-xs">{{ $field->field_key }}</td>
                                <td class="ps-4 pe-4 py-3 text-center">
                                    @if($field->is_system)
                                        <div class="flex justify-center"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="text-notion-blue"><path d="M20 6 9 17l-5-5"/></svg></div>
                                    @else
                                        <span class="text-xs opacity-30">—</span>
                                    @endif
                                </td>
                                <td class="ps-4 pe-4 py-3">
                                    @if(!$field->is_system)
                                        <form action="{{ route('admin.fields.destroy', $field->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="opacity-0 group-hover:opacity-100 p-1 hover:bg-red-500/10 text-red-400 rounded transition-all" onclick="return confirm('{{ __('Delete this field? Data will be lost.') }}')">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
