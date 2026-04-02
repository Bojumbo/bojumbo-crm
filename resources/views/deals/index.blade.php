@extends('layouts.app')

@section('content')
@push('header_menu')
    <button @click="$dispatch('open-automations')" class="flex items-center gap-2 px-3 py-1.5 text-sm rounded hover:bg-notion-hover text-notion-text-secondary transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        {{ __('Automations') }}
    </button>
@endpush
<div x-data="dealBoard()" @open-automations.window="automationsOpen = true" class="h-full flex flex-col space-y-4">
    
    <!-- Kanban Header -->
    <div class="flex items-center justify-between px-2">
        <div class="flex items-center gap-4">
            <h1 class="text-2xl font-bold tracking-tight">{{ __('Deals Board') }}</h1>
            
            <div class="relative" x-data="{ dropdown: false }">
                <button @click="dropdown = !dropdown" class="flex items-center gap-2 px-3 py-1 bg-white/5 border border-notion-border rounded-notion text-sm hover:bg-white/10 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-notion-blue"><path d="m9 18 6-6-6-6"/></svg>
                    <span>{{ $currentPipeline?->name ?? __('Select Pipeline') }}</span>
                </button>
                <div x-show="dropdown" @click.outside="dropdown = false" class="absolute top-full isolate-inline-start-0 mt-1 w-48 bg-card border border-notion-border rounded-notion shadow-xl z-50 p-1" x-cloak>
                    @foreach($pipelines as $p)
                        <a href="?pipeline_id={{ $p->id }}" class="block px-3 py-1.5 text-sm rounded hover:bg-notion-hover {{ $currentPipeline?->id == $p->id ? 'text-notion-blue font-medium' : '' }}">
                            {{ $p->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button @click="resetForm()" class="bg-notion-blue text-white px-4 py-1.5 rounded-notion text-sm font-medium hover:bg-blue-600 transition-colors shadow-lg shadow-blue-500/20 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                {{ __('New Deal') }}
            </button>
        </div>
    </div>
    <!-- Kanban Board -->
    <div class="flex-1 flex gap-4 overflow-x-auto pb-4 custom-scrollbar">
        @foreach($currentPipeline->stages as $stage)
            <div class="flex-shrink-0 w-[214px] flex flex-col bg-white/[0.02] rounded-xl border border-notion-border/50">
                <div class="p-3 flex items-center justify-between border-b border-notion-border/30">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-notion-text-secondary">{{ $stage->name }}</span>
                        <span class="bg-white/5 px-1.5 py-0.5 rounded text-[10px] text-notion-text-secondary">
                            {{ $deals->filter(fn($d) => $d->getFieldValue(2006) == $stage->id)->count() }}
                        </span>
                    </div>
                    <button @click="resetForm('{{ $stage->id }}')" class="p-1 hover:bg-white/5 rounded text-notion-text-secondary opacity-0 group-hover:opacity-100 transition-opacity">+</button>
                </div>
                <div id="stage-{{ $stage->id }}" data-stage-id="{{ $stage->id }}" class="flex-1 p-2 space-y-2 overflow-y-auto custom-scrollbar min-h-[100px]">
                    @foreach($deals->filter(fn($d) => $d->getFieldValue(2006) == $stage->id) as $deal)
                        @php
                            $rowFields = [];
                            foreach($columns as $col) {
                                $rowFields[$col->static_id] = $deal->getFieldValue($col->static_id);
                            }
                        @endphp
                        <div draggable="true" 
                             data-id="{{ $deal->id }}"
                             @click="initRow({{ $deal->id }}, {{ json_encode($rowFields) }}, {{ $deal->products->map(fn($p) => ['id' => $p->id, 'pivot' => ['quantity' => $p->pivot->quantity, 'price_at_sale' => $p->pivot->price_at_sale]]) }})"
                             class="bg-card p-3 rounded-notion border border-notion-border shadow-sm hover:border-notion-blue/50 hover:shadow-md transition-all cursor-pointer group relative">
                            
                            <div class="text-sm font-medium mb-2 group-hover:text-notion-blue transition-colors">
                                {{ $deal->getFieldValue(2001) ?? __('Untitled Deal') }}
                            </div>
                            
                            @if($deal->getFieldValue(2007))
                                <div class="mb-2 flex items-center gap-1.5 opacity-60">
                                    <div class="w-4 h-4 rounded-full bg-notion-hover flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    </div>
                                    <span class="text-[10px] whitespace-nowrap overflow-hidden text-ellipsis">{{ $users->find($deal->getFieldValue(2007))?->name ?? 'Unknown' }}</span>
                                </div>
                            @endif

                            <div class="flex items-center justify-between mt-auto">
                                <div class="text-xs font-mono text-green-500/80">
                                    {{ $currency }}{{ number_format($deal->getFieldValue(2002) ?? 0, 2) }}
                                </div>
                                <div class="flex -space-x-1">
                                    <div class="w-5 h-5 rounded-full bg-notion-blue/20 border border-card flex items-center justify-center text-[8px] font-bold text-notion-blue">
                                        {{ mb_substr($deal->getFieldValue(2001) ?? 'U', 0, 1) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    <!-- Side Peek Modal -->
    <div x-show="open" 
         class="fixed inset-y-0 right-0 w-[1200px] bg-card border-inline-start border-notion-border shadow-2xl z-50 flex flex-col"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         x-cloak>
        
        <div class="h-14 border-b border-notion-border ps-6 pe-6 flex items-center justify-between bg-white/[0.02]">
            <div class="flex items-center gap-3">
                <span class="w-2 h-2 rounded-full bg-notion-blue animate-pulse"></span>
                <span class="text-sm font-bold tracking-tight" x-text="editMode ? '{{ __('Deal Details') }}' : '{{ __('Create New Deal') }}'"></span>
            </div>
            <div class="flex items-center gap-2">
                <!-- Document Generation Actions -->
                <template x-if="editMode">
                    <div class="relative" x-data="{ docDropdown: false, docLoading: false, templates: [] }">
                        <button @click="docDropdown = !docDropdown; if(templates.length === 0) fetch('/api/templates').then(r => r.json()).then(d => templates = d)" 
                                :disabled="docLoading"
                                class="flex items-center gap-2 px-3 py-1.5 bg-white/5 border border-notion-border rounded-notion text-xs hover:bg-white/10 transition-colors disabled:opacity-50">
                            <template x-if="!docLoading">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                            </template>
                            <template x-if="docLoading">
                                <svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </template>
                            <span x-text="docLoading ? '{{ __('Generating...') }}' : '{{ __('Generate Doc') }}'"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-50"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
                        
                        <div x-show="docDropdown" @click.outside="docDropdown = false" class="absolute top-full right-0 mt-1 w-56 bg-card border border-notion-border rounded-notion shadow-2xl z-[60] p-1" x-cloak>
                            <template x-for="tpl in templates" :key="tpl.id">
                                <button @click="docLoading = true; docDropdown = false; 
                                        fetch(`/deals/${currentId}/generate-doc`, {
                                            method: 'POST',
                                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                            body: JSON.stringify({ template_id: tpl.id })
                                        }).then(r => r.json()).then(data => {
                                            docLoading = false;
                                            if(data.url) window.open(data.url, '_blank');
                                            else alert(data.message || 'Error');
                                        }).catch(() => docLoading = false)" 
                                        class="w-full text-left px-3 py-1.5 text-xs rounded hover:bg-notion-hover flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-notion-text-secondary"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    <span x-text="tpl.name"></span>
                                </button>
                            </template>
                            <template x-if="templates.length === 0">
                                <div class="px-3 py-1.5 text-[10px] text-notion-text-secondary italic">{{ __('No templates found') }}</div>
                            </template>
                        </div>
                    </div>
                </template>
                <template x-if="editMode">
                    <form :action="'/deals/' + currentId" method="POST" onsubmit="return confirm('{{ __('Видалити цю угоду?') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-1.5 hover:bg-red-500/10 text-red-400 rounded transition-colors" title="{{ __('Delete Deal') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                        </button>
                    </form>
                </template>
                <button @click="open = false" class="p-1.5 hover:bg-notion-hover rounded text-notion-text-secondary transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
        </div>
        <div class="flex-1 flex overflow-hidden">
            <!-- LEFT SECTOR (Properties) -->
            <form :action="editMode ? '/deals/' + currentId : '{{ route('deals.store') }}'" method="POST" class="w-[320px] border-inline-end border-notion-border ps-6 pe-6 py-6 space-y-6 overflow-y-auto bg-white/[0.01]">
                @csrf
                <template x-if="editMode"><input type="hidden" name="_method" value="PATCH"></template>
                
                <h3 class="text-[10px] uppercase tracking-wider text-notion-text-secondary font-bold">{{ __('Properties') }}</h3>
                <input type="hidden" name="fields[2005]" x-model="fields['2005']">
                
                <template x-for="(p, index) in dealProducts" :key="'hp-'+index">
                    <div>
                        <input type="hidden" :name="'products['+index+'][id]'" :value="p.id">
                        <input type="hidden" :name="'products['+index+'][qty]'" :value="p.qty">
                        <input type="hidden" :name="'products['+index+'][price]'" :value="p.price">
                    </div>
                </template>
                <div class="space-y-4">
                    @foreach($columns as $column)
                        @if($column->static_id == 2005) @continue @endif
                        <div class="space-y-1">
                            <label class="text-[11px] text-notion-text-secondary">{{ $column->label_en }}</label>
                            @if($column->static_id == 2006)
                                <select name="fields[2006]" x-model="fields['2006']" class="notion-input w-full text-xs">
                                    @foreach($pipelines as $p)
                                        <optgroup label="{{ $p->name }}">
                                            @foreach($p->stages as $s)
                                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            @elseif($column->field_key === 'counterparty_id')
                                <div class="flex gap-2">
                                    <select name="fields[{{ $column->static_id }}]" x-model="fields['{{ $column->static_id }}']" class="notion-input flex-1 text-xs bg-[#252525] text-white">
                                        <option value="" class="text-white bg-[#202020]">{{ __('Select Customer...') }}</option>
                                        @foreach($counterparties as $cp)
                                            <option value="{{ $cp->id }}" class="text-white bg-[#202020]">{{ $cp->getFieldValue(1001) }}</option>
                                        @endforeach
                                        <template x-for="cp in newCounterparties" :key="cp.id">
                                            <option :value="cp.id" x-text="cp.name" class="text-white bg-[#202020]"></option>
                                        </template>
                                    </select>
                                    <button type="button" @click="showQuickCounterparty()" class="w-8 h-8 flex items-center justify-center bg-notion-blue/10 text-notion-blue rounded hover:bg-notion-blue hover:text-white transition-all shadow-sm" title="{{ __('Quick Create Counterparty') }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                                    </button>
                                </div>
                            @elseif($column->static_id == 2007)
                                <select name="fields[2007]" x-model="fields['2007']" class="notion-input w-full text-xs">
                                    <option value="">{{ __('No Manager') }}</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            @elseif($column->field_type === 'numeric')
                                <input type="number" step="0.01" name="fields[{{ $column->static_id }}]" x-model="fields['{{ $column->static_id }}']" @input="if('{{ $column->static_id }}' == '2002') return; updateAmount()" class="notion-input w-full text-xs font-mono" {{ $column->static_id == 2002 ? 'readonly' : '' }}>
                            @elseif($column->field_type === 'date')
                                <input type="date" name="fields[{{ $column->static_id }}]" x-model="fields['{{ $column->static_id }}']" class="notion-input w-full text-xs">
                            @else
                                <input type="text" name="fields[{{ $column->static_id }}]" x-model="fields['{{ $column->static_id }}']" class="notion-input w-full text-xs" placeholder="{{ __('Empty') }}">
                            @endif
                        </div>
                    @endforeach
                </div>
                <button type="submit" class="w-full bg-notion-blue mt-4 py-2 rounded-notion text-sm text-white font-medium hover:bg-blue-600 transition-colors shadow-lg shadow-blue-500/10">
                    {{ __('Save Changes') }}
                </button>
            </form>
            <!-- RIGHT SECTOR (Tabs) -->
            <div class="flex-1 flex flex-col min-w-0">
                <div class="flex items-center ps-6 pe-6 border-b border-notion-border gap-6 h-12">
                    <button type="button" @click="activeTab = 'comments'" class="text-sm font-medium h-full border-b-2 transition-colors" :class="activeTab === 'comments' ? 'text-white border-white' : 'text-notion-text-secondary border-transparent hover:text-white'">{{ __('Comments') }}</button>
                    <button type="button" @click="activeTab = 'products'" class="text-sm font-medium h-full border-b-2 transition-colors" :class="activeTab === 'products' ? 'text-white border-white' : 'text-notion-text-secondary border-transparent hover:text-white'">{{ __('Products') }}</button>
                    <button type="button" @click="activeTab = 'documents'; fetchFiles();" class="text-sm font-medium h-full border-b-2 transition-colors" :class="activeTab === 'documents' ? 'text-white border-white' : 'text-notion-text-secondary border-transparent hover:text-white'">{{ __('Documents') }}</button>
                </div>
                <div class="flex-1 overflow-y-auto ps-8 pe-8 py-8">
                    <!-- Comments Feed -->
                    <div x-show="activeTab === 'comments'" class="h-full flex flex-col">
                        <div class="flex-1 space-y-6 overflow-y-auto pe-2 custom-scrollbar">
                            <template x-for="log in activityLogs" :key="log.id">
                                <div class="flex gap-3">
                                    <div class="w-8 h-8 rounded-full bg-notion-blue/20 flex items-center justify-center text-[10px] font-bold text-notion-blue" x-text="log.user.charAt(0)"></div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-xs font-bold" x-text="log.user"></span>
                                            <span class="text-[10px] text-notion-text-secondary" x-text="log.created_at"></span>
                                        </div>
                                        <template x-if="log.action === 'comment'">
                                            <div class="text-sm bg-white/5 p-3 rounded-notion" x-text="log.new_value"></div>
                                        </template>
                                        <template x-if="log.action === 'field_updated'">
                                            <div class="text-[11px] text-notion-text-secondary">
                                                {{ __('Updated') }} <span class="font-bold text-white/70" x-text="log.field_name"></span>: 
                                                <span class="line-through opacity-50" x-text="log.old_value || 'None'"></span> 
                                                → <span class="text-notion-blue" x-text="log.new_value"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div class="mt-4 pt-4 border-t border-notion-border flex gap-2">
                             <input type="text" x-model="newComment" @keydown.enter.prevent="postComment()" class="notion-input flex-1 bg-white/5 text-sm" placeholder="{{ __('Add a comment...') }}">
                             <button type="button" @click="postComment()" class="bg-notion-blue px-4 rounded-notion text-sm font-bold text-white hover:bg-blue-600 disabled:opacity-50 transition-colors" :disabled="!newComment.trim()">{{ __('Send') }}</button>
                        </div>
                    </div>
                    <!-- Products Tab -->
                    <div x-show="activeTab === 'products'" class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold uppercase tracking-widest text-notion-text-secondary">{{ __('Line Items') }}</h4>
                            <button type="button" @click="addProduct()" class="text-xs text-notion-blue">+ {{ __('Add Item') }}</button>
                        </div>
                        <div class="space-y-2">
                            <template x-for="(p, index) in dealProducts" :key="index">
                                <div class="flex gap-2 items-center bg-white/5 p-2 rounded border border-notion-border/30">
                                    <div class="flex-1 flex gap-2">
                                        <!-- Custom Searchable Dropdown -->
                                        <div class="relative flex-1" x-data="{ 
                                            dropdownOpen: false, 
                                            search: '',
                                            get filtered() {
                                                const s = this.search.toLowerCase();
                                                return allProducts.filter(prod => {
                                                    const n = (prod.name || '').toLowerCase();
                                                    const k = (prod.sku || '').toLowerCase();
                                                    return !s || n.includes(s) || k.includes(s);
                                                }).slice(0, 5);
                                            }
                                        }">
                                            <button type="button" @click="dropdownOpen = !dropdownOpen" 
                                                    class="notion-input w-full flex items-center justify-between text-xs bg-[#252525] text-white border border-notion-border/30 hover:border-notion-blue/50 transition-all px-3 py-2 rounded shadow-sm">
                                                <span x-text="allProducts.find(ap => ap.id == p.id)?.name || '{{ __('Select Product...') }}'" class="truncate text-white"></span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="opacity-50"><path d="m6 9 6 6 6-6"/></svg>
                                            </button>

                                            <div x-show="dropdownOpen" @click.outside="dropdownOpen = false" 
                                                 class="absolute top-full left-0 w-full mt-1 bg-[#202020] border border-notion-border shadow-2xl z-[100] p-1 flex flex-col space-y-1 overflow-hidden rounded-notion" x-cloak>
                                                <div class="p-2 border-b border-white/5">
                                                    <input type="text" 
                                                           x-model="p.search"
                                                           @click.stop
                                                           placeholder="Search name or SKU..." 
                                                           class="w-full bg-notion-bg-dark border border-white/10 rounded px-2 py-1 text-[11px] text-white focus:outline-none focus:border-notion-blue">
                                                </div>
                                                <div class="max-h-[220px] overflow-y-auto preview-scrollbar">
                                                    <template x-for="ap in allProducts.filter(x => !p.search || (x.name + x.sku).toLowerCase().includes(p.search.toLowerCase())).slice(0, 10)" :key="ap.id">
                                                        <button type="button" 
                                                                @click="p.id = ap.id; p.price = ap.price; updateAmount(); dropdownOpen = false; p.search = '';" 
                                                                class="w-full text-left px-2 py-2 hover:bg-notion-blue/20 rounded text-[11px] transition-colors border-b border-white/[0.03] last:border-0 flex flex-col items-start gap-0.5">
                                                            <div class="flex items-center gap-1.5 w-full">
                                                                <span x-text="ap.name" class="text-white font-medium truncate"></span>
                                                                <span x-text="'#' + ap.id" class="text-[9px] opacity-30 flex-shrink-0"></span>
                                                            </div>
                                                            <div class="flex items-center gap-2 text-[9px] opacity-60">
                                                                <span x-show="ap.sku" x-text="'SKU: ' + ap.sku"></span>
                                                                <span x-text="'$' + parseFloat(ap.price).toFixed(2)"></span>
                                                            </div>
                                                        </button>
                                                    </template>
                                                    <div x-show="allProducts.filter(x => !p.search || (x.name + x.sku).toLowerCase().includes(p.search.toLowerCase())).length === 0" 
                                                         class="p-4 text-center text-[10px] opacity-40">
                                                        No products found
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="button" @click="showQuickProduct(index)" class="w-8 h-8 flex items-center justify-center bg-notion-blue/10 text-notion-blue rounded hover:bg-notion-blue hover:text-white transition-all shadow-sm shrink-0" title="{{ __('Quick Create Product') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                                        </button>
                                    </div>
                                    <input type="number" x-model="p.qty" @input="updateAmount()" class="notion-input w-20 text-xs text-center" placeholder="{{ __('Qty') }}">
                                    <div class="w-20 text-inline-end px-2">
                                        <span class="text-[10px] text-notion-text-secondary block leading-none">{{ __('Price') }}</span>
                                        <span class="text-xs font-mono text-white/60" x-text="parseFloat(p.price || 0).toFixed(2)"></span>
                                    </div>
                                    <div class="w-24 text-inline-end pe-2">
                                        <span class="text-[10px] text-notion-text-secondary block leading-none">{{ __('Subtotal') }}</span>
                                        <span class="text-xs font-mono text-white/90" x-text="( (parseFloat(p.price)||0) * (parseFloat(p.qty)||0) ).toFixed(2)"></span>
                                    </div>
                                    <button type="button" @click="removeProduct(index); updateAmount();" class="text-red-400 p-1 hover:bg-red-500/10 rounded">×</button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Documents Tab -->
                    <div x-show="activeTab === 'documents'" class="h-full flex flex-col space-y-6">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold uppercase tracking-widest text-notion-text-secondary">{{ __('Google Drive Documents') }}</h4>
                            <label class="cursor-pointer bg-notion-blue/10 hover:bg-notion-blue/20 text-notion-blue px-3 py-1.5 rounded-notion text-xs font-bold transition-colors flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                {{ __('Upload File') }}
                                <input type="file" @change="uploadFile($event)" class="hidden">
                            </label>
                        </div>

                        <div class="flex-1 overflow-y-auto space-y-2 pe-2 custom-scrollbar" x-data="{ loading: false }">
                            <template x-if="filesLoading">
                                <div class="flex flex-col items-center justify-center h-32 space-y-3">
                                    <svg class="animate-spin h-6 w-6 text-notion-blue" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span class="text-xs text-notion-text-secondary">{{ __('Syncing with Google Drive...') }}</span>
                                </div>
                            </template>

                            <template x-if="!filesLoading && dealFiles.length === 0">
                                <div class="flex flex-col items-center justify-center h-32 space-y-2 border-2 border-dashed border-notion-border rounded-xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-notion-text-secondary opacity-20"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    <span class="text-xs text-notion-text-secondary font-medium">{{ __('No documents yet') }}</span>
                                </div>
                            </template>

                            <template x-for="file in dealFiles" :key="file.id">
                                <a :href="file.webViewLink" target="_blank" class="group flex items-center gap-4 p-3 rounded-xl border border-notion-border bg-white/[0.02] hover:bg-white/[0.05] hover:border-notion-blue/30 transition-all">
                                    <div class="w-10 h-10 rounded-lg bg-white/5 flex items-center justify-center flex-shrink-0 group-hover:bg-notion-blue/10 transition-colors">
                                        <img :src="file.iconLink" class="w-5 h-5 opacity-70 group-hover:opacity-100 transition-opacity">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium text-white truncate leading-none mb-1.5" x-text="file.name"></div>
                                        <div class="flex items-center gap-3 text-[10px] text-notion-text-secondary font-bold uppercase tracking-widest">
                                            <span x-text="new Date(file.createdTime).toLocaleDateString('uk-UA', {day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit'})"></span>
                                            <template x-if="file.size">
                                                <span class="flex items-center gap-1 before:content-['•'] before:opacity-30 before:mr-2" x-text="(file.size / 1024).toFixed(1) + ' KB'"></span>
                                            </template>
                                        </div>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-notion-text-secondary opacity-0 group-hover:opacity-100 transition-all transform translate-x-1 group-hover:translate-x-0"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div x-show="open" @click="open = false" class="fixed inset-0 bg-black/40 z-40" x-cloak></div>

    <!-- Automations Sidebar -->
    <div x-show="automationsOpen" 
         class="fixed inset-y-0 right-0 w-[500px] bg-card border-inline-start border-notion-border shadow-2xl z-50 flex flex-col"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         x-cloak>
        
        <div class="h-14 border-b border-notion-border ps-6 pe-6 flex items-center justify-between bg-white/[0.02]">
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold tracking-tight">{{ __('Pipeline Automations') }}</span>
            </div>
            <button @click="automationsOpen = false" class="p-1.5 hover:bg-notion-hover rounded text-notion-text-secondary transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">
            @foreach($currentPipeline->stages as $stage)
                <div class="bg-white/5 border border-notion-border rounded-xl p-4 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full" style="background-color: {{ $stage->color }}"></span>
                            <h4 class="font-bold text-sm">{{ $stage->name }}</h4>
                        </div>
                        <button @click="showAddAutomationModal({{ $stage->id }})" class="text-xs text-notion-blue hover:underline">
                            + {{ __('Add Rule') }}
                        </button>
                    </div>
                    <div class="space-y-1.5 overflow-y-auto max-h-[300px] pr-1 preview-scrollbar">
                        @forelse($stage->automations as $automation)
                            <div class="text-[11px] bg-white/[0.03] p-2 rounded flex items-center justify-between group border border-white/5">
                                <div class="flex items-center gap-2 text-notion-text-secondary truncate mr-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-notion-blue flex-shrink-0"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                    <div class="truncate">
                                        @if($automation->action_type === 'duplicate_deal')
                                            <span>
                                                {{ __('Duplicate to') }}: 
                                                @php
                                                    $tPipe = $pipelines->find($automation->action_payload['target_pipeline_id'] ?? null);
                                                    $tStage = $tPipe?->stages->find($automation->action_payload['target_stage_id'] ?? null);
                                                @endphp
                                                <span class="font-bold text-white">{{ $tPipe?->name ?? '?' }} ({{ $tStage?->name ?? '?' }})</span>
                                            </span>
                                        @elseif($automation->action_type === 'send_webhook')
                                            <span>
                                                {{ __('Webhook to') }}: 
                                                <span class="font-bold text-white truncate inline-block max-w-[150px] align-bottom" title="{{ $automation->action_payload['url'] ?? '' }}">
                                                    {{ $automation->action_payload['url'] ?? '?' }}
                                                </span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button @click="editAutomation({{ json_encode($automation) }})" class="p-1 hover:bg-notion-hover rounded text-notion-text-secondary hover:text-notion-text transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                                    </button>
                                    <button @click="deleteAutomation({{ $automation->id }})" class="p-1 hover:bg-red-500/10 rounded text-notion-text-secondary hover:text-red-400 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="text-[11px] text-notion-text-secondary italic px-2 py-4 text-center bg-white/[0.02] rounded border border-dashed border-white/5">
                                {{ __('No automations yet') }}
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div x-show="automationsOpen" @click="automationsOpen = false" class="fixed inset-0 bg-black/40 z-[45]" x-cloak></div>

    <!-- Quick Counterparty Modal -->
    <div x-show="quickCP" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60" x-cloak>
        <div @click.outside="quickCP = false" class="bg-card w-full max-w-sm rounded-2xl border border-notion-border shadow-2xl overflow-hidden p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-white/5 pb-3">
                <h4 class="font-bold text-white text-sm">{{ __('Quick Create Customer') }}</h4>
                <button @click="quickCP = false" class="text-notion-text-secondary">×</button>
            </div>
            <div class="space-y-3">
                <div>
                    <label class="text-[10px] uppercase font-bold text-notion-text-secondary">{{ __('Name/Company') }}</label>
                    <input type="text" x-model="cpForm.name" class="notion-input w-full text-sm mt-1" placeholder="{{ __('Required') }}">
                </div>
                <div>
                    <label class="text-[10px] uppercase font-bold text-notion-text-secondary">{{ __('Phone') }}</label>
                    <input type="text" x-model="cpForm.phone" class="notion-input w-full text-sm mt-1">
                </div>
                <div>
                    <label class="text-[10px] uppercase font-bold text-notion-text-secondary">{{ __('Email') }}</label>
                    <input type="email" x-model="cpForm.email" class="notion-input w-full text-sm mt-1">
                </div>
            </div>
            <div class="flex justify-end pt-2">
                <button @click="saveQuickCounterparty()" :disabled="!cpForm.name" class="bg-notion-blue text-white px-4 py-2 rounded-notion text-xs font-bold hover:bg-blue-600 disabled:opacity-50 transition-all">
                    {{ __('Create & Select') }}
                </button>
            </div>
        </div>
    </div>

    <div x-show="quickCP" @click="quickCP = false" class="fixed inset-0 bg-black/40 z-[95]" x-cloak></div>

    <!-- Quick Product Modal -->
    <div x-show="quickProd" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60" x-cloak>
        <div @click.outside="quickProd = false" class="bg-card w-full max-w-sm rounded-2xl border border-notion-border shadow-2xl overflow-hidden p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-white/5 pb-3">
                <h4 class="font-bold text-white text-sm">{{ __('Quick Create Product') }}</h4>
                <button @click="quickProd = false" class="text-notion-text-secondary">×</button>
            </div>
            <div class="space-y-3">
                <div>
                    <label class="text-[10px] uppercase font-bold text-notion-text-secondary">{{ __('Product Name') }}</label>
                    <input type="text" x-model="prodForm.name" class="notion-input w-full text-sm mt-1" placeholder="{{ __('Required') }}">
                </div>
                <div>
                    <label class="text-[10px] uppercase font-bold text-notion-text-secondary">{{ __('Price') }}</label>
                    <input type="number" step="0.01" x-model="prodForm.price" class="notion-input w-full text-sm mt-1">
                </div>
            </div>
            <div class="flex justify-end pt-2">
                <button @click="saveQuickProduct()" :disabled="!prodForm.name" class="bg-notion-blue text-white px-4 py-2 rounded-notion text-xs font-bold hover:bg-blue-600 disabled:opacity-50 transition-all">
                    {{ __('Create & Add') }}
                </button>
            </div>
        </div>
    </div>
    <div x-show="automationFormOpen" class="fixed inset-0 bg-black/60 z-[50] flex items-center justify-center p-4" x-cloak>
        <div @click.outside="automationFormOpen = false" class="bg-card border border-notion-border rounded-xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col">
            <div class="h-12 border-b border-notion-border px-4 flex items-center justify-between">
                <span class="text-sm font-bold" x-text="automationEditMode ? '{{ __('Edit Automation Rule') }}' : '{{ __('Add Automation Rule') }}'"></span>
                <button @click="automationFormOpen = false" class="text-notion-text-secondary hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            
            <div class="p-6 space-y-4">
                <div class="space-y-1">
                    <label class="text-xs text-notion-text-secondary uppercase tracking-widest">{{ __('Action') }}</label>
                    <select x-model="automationForm.action_type" class="notion-input w-full text-sm">
                        <option value="duplicate_deal">{{ __('Duplicate Deal') }}</option>
                        <option value="send_webhook">{{ __('Send Webhook') }}</option>
                    </select>
                </div>

                <template x-if="automationForm.action_type === 'send_webhook'">
                    <div class="space-y-1.5 pt-2">
                        <label class="block text-xs font-medium text-notion-text-secondary mb-1">{{ __('Webhook URL') }}</label>
                        <input type="url" x-model="automationForm.url" placeholder="https://hooks.zapier.com/..." class="w-full bg-notion-hover border-none rounded p-2 text-sm focus:ring-1 focus:ring-notion-blue text-white">
                    </div>
                </template>

                <template x-if="automationForm.action_type === 'duplicate_deal'">
                    <div class="space-y-4 pt-2">
                        <div>
                            <label class="block text-xs font-medium text-notion-text-secondary mb-1">{{ __('Target Pipeline') }}</label>
                            <select x-model="automationForm.target_pipeline" class="w-full bg-notion-hover border-none rounded p-2 text-sm focus:ring-1 focus:ring-notion-blue">
                                <option value="">{{ __('Select Pipeline') }}</option>
                                @foreach($pipelines as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-notion-text-secondary mb-1">{{ __('Target Stage') }}</label>
                            <select x-model="automationForm.target_stage" class="w-full bg-notion-hover border-none rounded p-2 text-sm focus:ring-1 focus:ring-notion-blue">
                                <option value="">{{ __('Select Stage') }}</option>
                                <template x-if="automationForm.target_pipeline">
                                    @foreach($pipelines as $p)
                                        <template x-if="automationForm.target_pipeline == {{ $p->id }}">
                                            <optgroup label="{{ $p->name }}">
                                                @foreach($p->stages as $s)
                                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        </template>
                                    @endforeach
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-notion-text-secondary mb-1">{{ __('Responsible Manager (Optional)') }}</label>
                            <select x-model="automationForm.target_user_id" class="w-full bg-notion-hover border-none rounded p-2 text-sm focus:ring-1 focus:ring-notion-blue">
                                <option value="">{{ __('Original Manager') }}</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </template>

                <div class="pt-4 flex justify-end gap-2">
                    <button @click="automationFormOpen = false" class="px-3 py-1.5 text-sm hover:bg-white/5 rounded-notion transition-colors">{{ __('Cancel') }}</button>
                    <button @click="saveAutomation()" 
                        :disabled="automationForm.action_type === 'duplicate_deal' ? (!automationForm.target_pipeline || !automationForm.target_stage) : (!automationForm.url)" 
                        class="bg-notion-blue px-4 py-1.5 rounded-notion text-sm font-bold text-white hover:bg-blue-600 disabled:opacity-50 transition-colors">
                        {{ __('Save Rule') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    /* Агресивне виправлення для темної теми та системних меню браузера */
    select.notion-input, select.notion-input option {
        background-color: #252525 !important;
        color: rgba(255, 255, 255, 0.9) !important;
    }
    select.notion-input:focus, select.notion-input:active {
        background-color: #252525 !important;
        color: white !important;
        outline: none !important;
        box-shadow: 0 0 0 1px rgba(35, 131, 226, 0.5) !important;
    }
    /* Видаляємо прозорість, яка викликає проблеми з видимістю тексту */
    .notion-input.bg-transparent {
        background-color: #252525 !important;
    }
</style>
<script>
function dealBoard() {
    return {
        open: false,
        quickCP: false,
        quickProd: false,
        newCounterparties: [],
        newProductsAdded: [],
        dynamicCounterparties: {!! json_encode($counterparties->map(function($cp) {
            return [
                'id' => (int)$cp->id,
                'name' => (string)($cp->getFieldValue(1001) ?? 'Customer #'.$cp->id)
            ];
        })) !!},
        allProducts: {!! json_encode($allProducts->map(function($p) {
            $f = \DB::table('product_field_values')->where('product_id', $p->id)->pluck('value', 'static_id');
            return [
                'id' => (int)$p->id,
                'name' => (string)($f->get(3001) ?: $f->get(3002) ?: ('Product #'.$p->id)),
                'price' => (float)($f->get(3003) ?: 0),
                'sku' => (string)($f->get(3002) ?: '')
            ];
        })->values()) !!},
        cpForm: { name: '', phone: '', email: '' },
        prodForm: { name: '', price: 0, targetIndex: null },
        automationsOpen: false,
        automationFormOpen: false,
        automationStageId: null,
        automationForm: { action_type: 'duplicate_deal', target_pipeline: '', target_stage: '', target_user_id: '', url: '', id: null },
        automationEditMode: false,
        allPipelines: @json($pipelines),
        editMode: false,
        currentId: null,
        fields: {},
        newComment: '',
        activityLogs: [],
        dealProducts: [],
        dealFiles: [],
        filesLoading: false,
        activeTab: 'comments',
        initRow(id, rowFields, products) {
            this.editMode = true;
            this.currentId = id;
            this.fields = rowFields;
            this.newComment = '';
            this.dealProducts = products.map(p => {
                return {
                    id: p.id,
                    qty: p.pivot.quantity,
                    price: p.pivot.price_at_sale,
                    search: ''
                };
            });
            this.dealFiles = [];
            this.filesLoading = false;
            if (this.activeTab === 'documents') {
                this.fetchFiles();
            }
            this.fetchActivity();
            this.open = true;
        },
        async fetchActivity() {
            if (!this.currentId) return;
            try {
                const res = await fetch(`/activity?type=App\\Models\\Deal&id=${this.currentId}`);
                const data = await res.json();
                this.activityLogs = data;
            } catch (e) { console.error('Fetch activity failed', e); }
        },
        async postComment() {
            const comment = this.newComment.trim();
            if (!comment || !this.currentId) return;
            try {
                const response = await fetch('/activity', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ loggable_type: 'App\\Models\\Deal', loggable_id: this.currentId, comment: comment })
                });
                if (response.ok) { this.newComment = ''; await this.fetchActivity(); }
            } catch (e) { console.error('Post comment error:', e); }
        },
        resetForm(stageId = '') {
            this.editMode = false;
            this.currentId = null;
            this.fields = { '2005': '{{ $currentPipeline?->id }}', '2006': stageId };
            this.dealProducts = [];
            this.activityLogs = [];
            this.newComment = '';
            this.open = true;
        },
        addProduct() {
            this.dealProducts.push({ id: null, qty: 1, price: 0, search: '' });
        },
        removeProduct(index) { this.dealProducts.splice(index, 1); },
        updateAmount() {
            const total = this.dealProducts.reduce((sum, p) => sum + (parseFloat(p.price) || 0) * (parseFloat(p.qty) || 0), 0);
            this.fields['2002'] = total.toFixed(2);
        },
        async fetchFiles() {
            if (!this.currentId) return;
            this.filesLoading = true;
            try {
                const res = await fetch(`/deals/${this.currentId}/files`);
                const data = await res.json();
                if (data.success) {
                    this.dealFiles = data.files;
                }
            } catch (e) {
                console.error('Fetch files failed', e);
            } finally {
                this.filesLoading = false;
            }
        },
        async uploadFile(event) {
            const file = event.target.files[0];
            if (!file || !this.currentId) return;

            const formData = new FormData();
            formData.append('file', file);

            this.filesLoading = true;
            try {
                const res = await fetch(`/deals/${this.currentId}/upload`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    await this.fetchFiles();
                } else {
                    alert(data.message || 'Upload failed');
                }
            } catch (e) {
                console.error('Upload failed', e);
                alert('Connection error');
            } finally {
                this.filesLoading = false;
                event.target.value = ''; // Reset input
            }
        },
        showAddAutomationModal(stageId) {
            this.automationStageId = stageId;
            this.automationEditMode = false;
            this.automationForm = { action_type: 'duplicate_deal', target_pipeline: '', target_stage: '', target_user_id: '', url: '', id: null };
            this.automationFormOpen = true;
        },
        async editAutomation(a) {
            this.automationStageId = a.pipeline_stage_id;
            this.automationEditMode = true;
            this.automationForm.id = a.id;
            this.automationForm.action_type = a.action_type;
            
            if (a.action_type === 'duplicate_deal') {
                this.automationForm.target_pipeline = String(a.action_payload.target_pipeline_id);
                this.$nextTick(() => {
                    this.automationForm.target_stage = String(a.action_payload.target_stage_id);
                    this.automationForm.target_user_id = a.action_payload.target_user_id ? String(a.action_payload.target_user_id) : '';
                    this.automationFormOpen = true;
                });
            } else if (a.action_type === 'send_webhook') {
                this.automationForm.url = a.action_payload.url || '';
                this.automationFormOpen = true;
            }
        },
        async saveAutomation() {
            // Валідація залежно від типу дії
            if (!this.automationStageId) return;
            
            if (this.automationForm.action_type === 'duplicate_deal') {
                if (!this.automationForm.target_pipeline || !this.automationForm.target_stage) return;
            } else if (this.automationForm.action_type === 'send_webhook') {
                if (!this.automationForm.url) return;
            }

            const url = this.automationEditMode ? `/automations/${this.automationForm.id}` : '/automations';
            const method = this.automationEditMode ? 'PUT' : 'POST';
            try {
                const res = await fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({
                        pipeline_id: '{{ $currentPipeline->id ?? '' }}',
                        pipeline_stage_id: this.automationStageId,
                        action_type: this.automationForm.action_type,
                        action_payload: {
                            target_pipeline_id: this.automationForm.action_type === 'duplicate_deal' ? parseInt(this.automationForm.target_pipeline) : null,
                            target_stage_id: this.automationForm.action_type === 'duplicate_deal' ? parseInt(this.automationForm.target_stage) : null,
                            target_user_id: this.automationForm.action_type === 'duplicate_deal' && this.automationForm.target_user_id ? parseInt(this.automationForm.target_user_id) : null,
                            url: this.automationForm.action_type === 'send_webhook' ? this.automationForm.url : null
                        }
                    })
                });
                if (res.ok) {
                    window.location.reload();
                } else {
                    const text = await res.text();
                    alert("Error saving automation rule. Server replied: " + text);
                }
            } catch (e) {
                alert("Network error saving automation.");
                console.error(e);
            }
        },
        async deleteAutomation(id) {
            if (!confirm("{{ __('Ви впевнені?') }}")) return;
            try {
                const res = await fetch(`/automations/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                if (res.ok) window.location.reload();
            } catch (e) { }
        },
        showQuickCounterparty() {
            this.cpForm = { name: '', phone: '', email: '' };
            this.quickCP = true;
        },
        async saveQuickCounterparty() {
            if (!this.cpForm.name) return;
            try {
                const res = await fetch('/counterparties/quick', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({
                        fields: {
                            '1001': this.cpForm.name,
                            '1003': this.cpForm.phone,
                            '1002': this.cpForm.email
                        }
                    })
                });
                const data = await res.json();
                if (data.success) {
                    const newCP = { id: data.id, name: data.name };
                    this.dynamicCounterparties.push(newCP);
                    this.newCounterparties.push(newCP);
                    this.fields['2004'] = data.id.toString();
                    this.quickCP = false;
                }
            } catch (e) { console.error('Quick CP Create failed', e); }
        },
        showQuickProduct(index) {
            this.prodForm = { name: '', price: 0, targetIndex: index };
            this.quickProd = true;
        },
        async saveQuickProduct() {
            if (!this.prodForm.name) return;
            try {
                const res = await fetch('/products/quick', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({
                        fields: {
                            '3001': this.prodForm.name,
                            '3003': this.prodForm.price
                        }
                    })
                });
                const data = await res.json();
                if (data.success) {
                    const newProd = { id: data.id, name: data.name, field_values: data.field_values };
                    this.allProducts.push(newProd);
                    this.newProductsAdded.push(newProd);
                    
                    const idx = this.prodForm.targetIndex;
                    this.dealProducts[idx].id = data.id.toString();
                    this.dealProducts[idx].price = data.price;
                    this.updateAmount();
                    
                    this.quickProd = false;
                }
            } catch (e) { console.error('Quick Product Create failed', e); }
        }
    }
}
document.addEventListener('DOMContentLoaded', () => {
    const containers = document.querySelectorAll("[id^='stage-']");
    containers.forEach(container => {
        container.addEventListener('dragover', e => { e.preventDefault(); const dragging = document.querySelector('.dragging'); if (dragging) container.appendChild(dragging); });
        container.addEventListener('drop', async e => {
            const dealId = e.dataTransfer.getData('text/plain');
            const stageId = container.dataset.stageId;
            const res = await fetch(`/deals/${dealId}/move`, {
                method: "PATCH",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": '{{ csrf_token() }}' },
                body: JSON.stringify({ stage_id: stageId })
            });
            if (res.ok) {
                window.location.reload();
            } else {
                const text = await res.text();
                alert("Move Error: " + text);
            }
        });
    });
    document.body.addEventListener('dragstart', e => { const card = e.target.closest('[data-id]'); if (card) { card.classList.add('dragging'); e.dataTransfer.setData('text/plain', card.dataset.id); } });
    document.body.addEventListener('dragend', e => { const card = e.target.closest('[data-id]'); if (card) card.classList.remove('dragging'); });
});
</script>
@endsection