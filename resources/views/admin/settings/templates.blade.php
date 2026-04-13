@extends('admin.settings.layout')
@section('settings_content')
<div x-data="{ 
    open: false, 
    isEdit: false,
    editId: null,
    template: { name: '', google_drive_id: '', entity_type: 'deal', orientation: 'portrait', content: '' },
    
    openNew() {
        this.isEdit = false;
        this.editId = null;
        this.template = { name: '', google_drive_id: '', entity_type: 'deal', orientation: 'portrait', content: '' };
        this.open = true;
    },
    
    openEdit(t) {
        this.isEdit = true;
        this.editId = t.id;
        this.template = { 
            name: t.name, 
            google_drive_id: t.google_drive_id, 
            entity_type: t.entity_type, 
            orientation: t.orientation || 'portrait',
            content: t.content || ''
        };
        this.open = true;
    }
}" @open-modal.window="if($event.detail === 'add-template') openNew()">

    <div class="bg-card border border-notion-border rounded-notion overflow-hidden shadow-sm">
        <div class="p-6 border-b border-notion-border bg-notion-hover/20 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-lg text-notion-text-primary">{{ __('Document Templates') }}</h3>
                <p class="text-xs text-notion-text-secondary mt-1">{{ __('Manage your Google Document templates and HTML designs.') }}</p>
            </div>
            <button @click="openNew()" class="bg-notion-blue text-white px-4 py-1.5 rounded-notion text-sm font-medium hover:bg-blue-600 transition-colors shadow-lg shadow-blue-500/10">
                + {{ __('Add Template') }}
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-notion-border bg-notion-hover/10">
                        <th class="px-6 py-4 text-[10px] uppercase tracking-wider text-notion-text-secondary font-bold">{{ __('Template Name') }}</th>
                        <th class="px-6 py-4 text-[10px] uppercase tracking-wider text-notion-text-secondary font-bold">{{ __('Google Drive ID') }}</th>
                        <th class="px-6 py-4 text-[10px] uppercase tracking-wider text-notion-text-secondary font-bold">{{ __('Orientation') }}</th>
                        <th class="px-6 py-4 text-[10px] uppercase tracking-wider text-notion-text-secondary font-bold">{{ __('Design') }}</th>
                        <th class="px-6 py-4 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-notion-border">
                    @forelse($templates as $template)
                        <tr class="hover:bg-notion-hover transition-colors group cursor-pointer" @click="openEdit({{ json_encode($template) }})">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-notion-blue/10 flex items-center justify-center text-notion-blue flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    </div>
                                    <span class="text-sm font-medium text-notion-text-primary group-hover:text-notion-blue transition-colors">{{ $template->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <code class="text-[10px] text-notion-text-secondary bg-notion-hover px-2 py-1 rounded break-all max-w-[150px] inline-block">{{ $template->google_drive_id }}</code>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs text-notion-text-primary capitalize">{{ $template->orientation }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($template->content)
                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-green-500/10 border border-green-500/20 text-green-400 font-bold uppercase">{{ __('CRM HTML') }}</span>
                                @else
                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-notion-blue/10 border border-notion-blue/20 text-notion-blue font-bold uppercase">{{ __('Google Doc') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.settings.templates.destroy', $template) }}" method="POST" onsubmit="return confirm('{{ __('Delete template?') }}')" class="inline" @click.stop>
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-400 hover:bg-red-500/10 rounded-notion transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-notion-text-secondary text-sm italic">
                                {{ __('No templates found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Create/Edit -->
    <div x-show="open" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-[var(--color-overlay)] backdrop-blur-sm"
         x-cloak>
        <div @click.away="open = false" class="bg-card border border-notion-border w-full max-w-4xl rounded-xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="p-4 border-b border-notion-border flex items-center justify-between">
                <h3 class="text-sm font-bold text-notion-text-primary" x-text="isEdit ? '{{ __('Edit Template') }}' : '{{ __('Add Document Template') }}'"></h3>
                <button @click="open = false" class="text-notion-text-secondary hover:text-notion-text-primary transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            
            <form :action="isEdit ? '/admin/templates/' + editId : '{{ route('admin.settings.templates.store') }}'" method="POST" class="flex-1 flex flex-col min-h-0 overflow-hidden">
                @csrf
                <template x-if="isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="p-6 space-y-6 overflow-y-auto custom-scrollbar flex-1">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label class="text-[11px] font-bold text-notion-text-secondary uppercase tracking-wider">{{ __('Template Name') }}</label>
                            <input type="text" name="name" x-model="template.name" required class="notion-input w-full text-sm" placeholder="e.g. Sales Contract">
                        </div>
                        <div class="space-y-1 text-right">
                             <a x-show="template.google_drive_id" :href="'https://docs.google.com/document/d/' + template.google_drive_id + '/edit'" target="_blank" class="text-[10px] text-notion-blue hover:underline">
                                {{ __('Open in Google Docs') }} ↗
                             </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label class="text-[11px] font-bold text-notion-text-secondary uppercase tracking-wider">{{ __('Google Doc ID') }} ({{ __('Base Template') }})</label>
                            <input type="text" name="google_drive_id" x-model="template.google_drive_id" class="notion-input w-full text-sm font-mono" placeholder="1IJ4...">
                            <p class="text-[10px] text-notion-text-secondary italic">Used for fonts and page styles if HTML is empty.</p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[11px] font-bold text-notion-text-secondary uppercase tracking-wider">{{ __('Entity Type') }}</label>
                            <select name="entity_type" x-model="template.entity_type" class="notion-input w-full text-sm">
                                <option value="deal">{{ __('Deal') }}</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[11px] font-bold text-notion-text-secondary uppercase tracking-wider">{{ __('Orientation') }}</label>
                            <select name="orientation" x-model="template.orientation" class="notion-input w-full text-sm">
                                <option value="portrait">{{ __('Portrait') }}</option>
                                <option value="landscape">{{ __('Landscape') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Custom HTML Content Editor -->
                    <div class="space-y-2 border-t border-notion-border pt-6">
                        <div class="flex items-center justify-between">
                            <label class="text-[11px] font-bold text-notion-blue uppercase tracking-wider">{{ __('Custom Design (HTML)') }}</label>
                            <span class="text-[10px] text-notion-text-secondary font-mono italic">Support: @{{2001}}, @{{table:Name}}, @{{date}}</span>
                        </div>
                        <p class="text-[10px] text-notion-text-secondary mb-2">{{ __('If you provide HTML here, it will be used instead of the Google Doc content. Leave empty to use the Google Doc as source.') }}</p>
                        <textarea name="content" x-model="template.content" rows="12" class="notion-input w-full text-[12px] font-mono leading-relaxed bg-notion-hover/30" placeholder="<p>Рахунок №@{{2001}}</p> ... @{{table:products}}"></textarea>
                        @error('content')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="p-6 border-t border-notion-border bg-notion-hover/10 flex gap-4">
                    <button type="button" @click="open = false" class="flex-1 border border-notion-border text-notion-text-primary py-2 rounded-notion text-sm font-medium hover:bg-notion-hover transition-all">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="flex-1 bg-notion-blue text-white py-2 rounded-notion text-sm font-bold hover:bg-blue-600 shadow-lg shadow-blue-500/20 active:scale-95 transition-all">
                        {{ __('Save Template') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection