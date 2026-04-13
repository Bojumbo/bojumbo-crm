@extends('admin.settings.layout')
@section('settings_content')
<div x-data="{ 
    open: false, 
    isEdit: false,
    editId: null,
    tableName: '',
    showTotal: true,
    cols: [], 
    styles: { font_size: 10, border_width: 1, border_color: '#000000', header_bg: '#ffffff', cell_padding: 3 },
    
    openNew() {
        this.isEdit = false;
        this.editId = null;
        this.tableName = '';
        this.showTotal = true;
        this.cols = [];
        this.styles = { font_size: 10, border_width: 1, border_color: '#000000', header_bg: '#ffffff', cell_padding: 3 };
        this.open = true;
    },
    
    openEdit(table) {
        this.isEdit = true;
        this.editId = table.id;
        this.tableName = table.name;
        this.showTotal = !!table.show_total;
        this.styles = Object.assign({}, this.styles, table.styles || {});
        // Ensure columns have style objects
        this.cols = (table.columns || []).map(col => ({
            label: col.label,
            static_id: col.static_id,
            styles: Object.assign({ width: '', align: 'left', header_bold: true, content_bold: false }, col.styles || {})
        }));
        this.open = true;
    }
}" @open-modal.window="if($event.detail === 'add-table-config') openNew()">

    <div class="bg-card border border-notion-border rounded-notion overflow-hidden shadow-sm">
        <div class="p-6 border-b border-notion-border bg-notion-hover/20 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-lg text-notion-text-primary">{{ __('Document Tables') }}</h3>
                <p class="text-xs text-notion-text-secondary mt-1">{{ __('Configure tables to be inserted into Google Docs using') }} @{{table:Name}} {{ __('tags.') }}</p>
            </div>
            <button @click="openNew()" class="notion-btn-primary px-4 py-1.5 bg-notion-blue text-white rounded-notion text-sm font-bold shadow-lg shadow-blue-500/20 active:scale-95 transition-all text-center">
                + {{ __('New Table Config') }}
            </button>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($tables as $table)
                <div class="p-4 rounded-xl border border-notion-border bg-notion-hover/10 space-y-4 hover:border-notion-blue/30 transition-colors cursor-pointer group" @click="openEdit({{ json_encode($table) }})">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-notion-text-primary text-base group-hover:text-notion-blue transition-colors">{{ $table->name }}</h4>
                        <span class="text-[10px] bg-notion-blue/20 text-notion-blue px-2 py-0.5 rounded font-mono border border-notion-blue/30">
                            {!! '{{table:' . $table->name . '}}' !!}
                        </span>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-notion-text-secondary uppercase tracking-wider">{{ __('Columns') }}</label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($table->columns as $col)
                                <div class="bg-notion-hover px-2 py-1 rounded text-[10px] text-notion-text-secondary truncate border border-notion-border flex items-center justify-between" title="{{ $col['label'] }}">
                                    <span>{{ $col['label'] }}</span>
                                    @if(isset($col['styles']['align']))
                                        <span class="opacity-30 text-[8px]">{{ strtoupper($col['styles']['align'][0]) }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-2 border-t border-notion-border/30 flex justify-between items-center">
                        <div class="flex gap-2">
                            <span class="text-[9px] text-notion-text-secondary">Font: {{ $table->styles['font_size'] ?? '10' }}pt</span>
                            <span class="text-[9px] text-notion-text-secondary">Total: {{ $table->show_total ? 'Yes' : 'No' }}</span>
                        </div>
                        <form action="{{ route('admin.settings.document_tables.destroy', $table) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}')" @click.stop>
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400/50 hover:text-red-400 text-[10px] transition-colors">{{ __('Delete') }}</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal for New/Edit Table -->
    <div x-show="open" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-[var(--color-overlay)] backdrop-blur-sm"
         x-cloak>
        <div class="bg-card border border-notion-border w-full max-w-4xl rounded-xl shadow-2xl overflow-hidden p-8 space-y-6 max-h-[90vh] overflow-y-auto custom-scrollbar" @click.away="open = false">
            <h3 class="text-xl font-bold text-notion-text-primary tracking-tight" x-text="isEdit ? '{{ __('Edit Table Configuration') }}' : '{{ __('Create Table Configuration') }}'"></h3>
            
            <form :action="isEdit ? '/admin/document-tables/' + editId : '{{ route('admin.settings.document_tables.store') }}'" method="POST" class="space-y-8">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                
                <!-- Basic Data -->
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-1.5">
                        <label class="text-[11px] font-bold text-notion-text-secondary uppercase tracking-wider">{{ __('Table Name (Tag)') }}</label>
                        <input type="text" name="name" x-model="tableName" required class="notion-input w-full text-sm" placeholder="e.g. contract_items">
                        <p class="text-[10px] text-notion-text-secondary">Used as {!! '{{table:NAME}}' !!}</p>
                    </div>
                    <div class="flex items-center gap-2 pt-6">
                        <input type="checkbox" name="show_total" value="1" x-model="showTotal" id="show_total" class="rounded border-notion-border bg-notion-hover text-notion-blue focus:ring-notion-blue">
                        <label for="show_total" class="text-sm text-notion-text-primary select-none">{{ __('Show Total Row?') }}</label>
                    </div>
                </div>

                <!-- Global Styles -->
                <div class="bg-notion-hover/20 p-4 rounded-xl border border-notion-border/50">
                    <label class="text-[11px] font-bold text-notion-blue uppercase tracking-wider mb-4 block">{{ __('Global Table Styles') }}</label>
                    <div class="grid grid-cols-5 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[10px] text-notion-text-secondary">{{ __('Font Size (pt)') }}</label>
                            <input type="number" name="styles[font_size]" x-model="styles.font_size" class="notion-input w-full text-xs" step="0.5">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] text-notion-text-secondary">{{ __('Border (pt)') }}</label>
                            <input type="number" name="styles[border_width]" x-model="styles.border_width" class="notion-input w-full text-xs" step="0.5">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] text-notion-text-secondary">{{ __('Border Color') }}</label>
                            <input type="color" x-model="styles.border_color" class="w-full h-8 rounded bg-transparent cursor-pointer">
                            <input type="hidden" name="styles[border_color]" :value="styles.border_color">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] text-notion-text-secondary">{{ __('Header BG') }}</label>
                            <input type="color" x-model="styles.header_bg" class="w-full h-8 rounded bg-transparent cursor-pointer">
                            <input type="hidden" name="styles[header_bg]" :value="styles.header_bg">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] text-notion-text-secondary">{{ __('Padding (pt)') }}</label>
                            <input type="number" name="styles[cell_padding]" x-model="styles.cell_padding" class="notion-input w-full text-xs" step="1">
                        </div>
                    </div>
                </div>

                <!-- Columns Section -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b border-notion-border pb-2">
                        <label class="text-[11px] font-bold text-notion-text-secondary uppercase tracking-wider">{{ __('Columns Configuration') }}</label>
                        <button type="button" @click="cols.push({label: '', static_id: '', styles: { width: '', align: 'left', header_bold: true, content_bold: false }})" class="text-[11px] text-notion-blue font-bold px-3 py-1 bg-notion-blue/10 rounded-full hover:bg-notion-blue/20 transition-colors">+ {{ __('Add Column') }}</button>
                    </div>
                    
                    <div class="space-y-3">
                        <template x-for="(col, index) in cols" :key="index">
                            <div class="bg-notion-hover/20 p-4 rounded-xl border border-notion-border space-y-3">
                                <div class="flex gap-3 items-center">
                                    <div class="w-6 h-6 rounded-full bg-notion-hover flex items-center justify-center text-[10px] text-notion-text-secondary font-bold" x-text="index + 1"></div>
                                    <div class="flex-1">
                                        <input type="text" x-model="col.label" :name="'columns['+index+'][label]'" required class="notion-input w-full text-xs" placeholder="{{ __('Header Label') }}">
                                    </div>
                                    <div class="w-48">
                                        <select x-model="col.static_id" :name="'columns['+index+'][static_id]'" required class="notion-input w-full text-xs">
                                            <option value="">{{ __('Select Field...') }}</option>
                                            <optgroup label="System Fields">
                                                <option value="qty">{{ __('Quantity (Auto)') }}</option>
                                                <option value="subtotal">{{ __('Subtotal (Auto)') }}</option>
                                            </optgroup>
                                            <optgroup label="Product Fields">
                                                @foreach($productFields as $pf)
                                                    <option value="{{ $pf->static_id }}">{{ $pf->label_en }}</option>
                                                @endforeach
                                            </optgroup>
                                        </select>
                                    </div>
                                    <button type="button" @click="cols.splice(index, 1)" class="text-red-400 p-2 hover:bg-red-400/10 rounded-lg transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                    </button>
                                </div>
                                
                                 <!-- Individual Column Styles -->
                                 <div class="grid grid-cols-5 gap-3 ps-9 items-end">
                                     <div class="space-y-1">
                                         <label class="text-[9px] text-notion-text-secondary uppercase">{{ __('Width') }}</label>
                                         <input type="text" x-model="col.styles.width" :name="'columns['+index+'][styles][width]'" class="notion-input w-full text-[10px] py-1" placeholder="e.g. 100px or 20%">
                                     </div>
                                     <div class="space-y-1">
                                         <label class="text-[9px] text-notion-text-secondary uppercase">{{ __('Alignment') }}</label>
                                         <select x-model="col.styles.align" :name="'columns['+index+'][styles][align]'" class="notion-input w-full text-[10px] py-1">
                                             <option value="left">{{ __('Left') }}</option>
                                             <option value="center">{{ __('Center') }}</option>
                                             <option value="right">{{ __('Right') }}</option>
                                         </select>
                                     </div>
                                     <div class="space-y-1">
                                         <label class="text-[9px] text-notion-text-secondary uppercase">{{ __('Pad Left') }} (pt)</label>
                                         <input type="number" x-model="col.styles.padding_left" :name="'columns['+index+'][styles][padding_left]'" class="notion-input w-full text-[10px] py-1" placeholder="3">
                                     </div>
                                     <div class="space-y-1">
                                         <label class="text-[9px] text-notion-text-secondary uppercase">{{ __('Pad Right') }} (pt)</label>
                                         <input type="number" x-model="col.styles.padding_right" :name="'columns['+index+'][styles][padding_right]'" class="notion-input w-full text-[10px] py-1" placeholder="3">
                                     </div>
                                     <div class="flex items-center gap-2 pb-1.5 overflow-hidden">
                                         <div class="flex items-center gap-1.5">
                                             <input type="checkbox" :name="'columns['+index+'][styles][header_bold]'" x-model="col.styles.header_bold" value="1" :id="'h_bold_'+index" class="rounded border-notion-border bg-notion-hover text-notion-blue">
                                             <label :for="'h_bold_'+index" class="text-[9px] text-notion-text-primary whitespace-nowrap">{{ __('H.Bold') }}</label>
                                         </div>
                                         <div class="flex items-center gap-1.5">
                                             <input type="checkbox" :name="'columns['+index+'][styles][content_bold]'" x-model="col.styles.content_bold" value="1" :id="'c_bold_'+index" class="rounded border-notion-border bg-notion-hover text-notion-blue">
                                             <label :for="'c_bold_'+index" class="text-[9px] text-notion-text-primary whitespace-nowrap">{{ __('C.Bold') }}</label>
                                         </div>
                                     </div>
                                 </div>
                            </div>
                        </template>
                        <div x-show="cols.length === 0" class="text-center py-8 text-xs text-notion-text-secondary italic bg-notion-hover/10 rounded-xl border border-dashed border-notion-border">
                            {{ __('No columns added yet. Click + Add Column to begin.') }}
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex gap-4 pt-6 border-t border-notion-border">
                    <button type="button" @click="open = false" class="flex-1 px-4 py-2.5 border border-notion-border rounded-notion text-sm font-medium hover:bg-notion-hover transition-colors text-notion-text-primary">{{ __('Cancel') }}</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 bg-notion-blue text-white rounded-notion text-sm font-bold hover:bg-blue-600 transition-colors shadow-lg shadow-blue-500/20 active:scale-95" x-text="isEdit ? '{{ __('Update Table') }}' : '{{ __('Save Table') }}'"></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection