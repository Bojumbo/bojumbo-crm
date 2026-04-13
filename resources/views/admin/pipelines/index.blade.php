@extends('admin.settings.layout')

@section('settings_content')
<div x-data="{ 
    open: false,
    editMode: false,
    pipelineId: null,
    newPipelineName: '',
    stages: [
        { id: null, name: 'Lead', color: '#e3e2e0', is_won: false, is_lost: false },
        { id: null, name: 'Negotiation', color: '#d3e5ef', is_won: false, is_lost: false },
        { id: null, name: 'Closed Won', color: '#dbeddb', is_won: true, is_lost: false }
    ],
    addStage() {
        this.stages.push({ id: null, name: '', color: '#e3e2e0', is_won: false, is_lost: false });
    },
    removeStage(index) {
        this.stages.splice(index, 1);
    },
    openCreate() {
        this.editMode = false;
        this.pipelineId = null;
        this.newPipelineName = '';
        this.stages = [
            { id: null, name: 'Lead', color: '#e3e2e0', is_won: false, is_lost: false },
            { id: null, name: 'Negotiation', color: '#d3e5ef', is_won: false, is_lost: false },
            { id: null, name: 'Closed Won', color: '#dbeddb', is_won: true, is_lost: false }
        ];
        this.open = true;
    },
    openEdit(id, name, stagesData) {
        this.editMode = true;
        this.pipelineId = id;
        this.newPipelineName = name;
        this.stages = stagesData.map(s => ({ id: s.id, name: s.name, color: s.color, is_won: !!s.is_won, is_lost: !!s.is_lost }));
        this.open = true;
    }
}" class="space-y-6">
    
    <div class="flex items-center justify-between">
        <h3 class="font-bold text-lg text-notion-text-primary">{{ __('Pipelines & Sales Cycles') }}</h3>
        <button @click="openCreate()" class="flex items-center gap-1.5 px-3 py-1.5 bg-notion-blue text-white rounded-notion text-sm font-medium hover:bg-blue-600 transition-colors shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            {{ __('New Pipeline') }}
        </button>
    </div>

    <!-- Pipelines List -->
    <div class="grid grid-cols-1 gap-6">
        @foreach($pipelines as $pipeline)
            <div class="bg-card border border-notion-border rounded-notion overflow-hidden shadow-sm">
                <div class="p-4 border-b border-notion-border flex items-center justify-between bg-notion-hover/20">
                    <h3 class="font-bold text-md text-notion-text-primary">{{ $pipeline->name }}</h3>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="openEdit({{ $pipeline->id }}, '{{ addslashes($pipeline->name) }}', {{ $pipeline->stages->toJson() }})" class="text-notion-text-secondary hover:text-notion-text-primary p-1 rounded transition-colors" title="{{ __('Edit Pipeline') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/></svg>
                        </button>
                        <form action="{{ route('admin.pipelines.destroy', $pipeline) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-notion-text-secondary hover:text-red-400 p-1 rounded transition-colors" onclick="return confirm('{{ __('Видалити воронку?') }}')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="p-4 bg-notion-hover/10">
                    <div class="flex flex-wrap gap-3 items-center">
                        @foreach($pipeline->stages as $stage)
                            <div class="flex items-center gap-2 px-2 py-1 rounded border border-notion-border text-xs font-bold transition-all hover:scale-105" style="background-color: {{ $stage->color }}40; color: {{ $stage->color }}; border-color: {{ $stage->color }}80;">
                                <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $stage->color }}"></span>
                                {{ $stage->name }}
                                @if($stage->is_won)
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" class="text-green-500 ml-1"><path d="M20 6 9 17l-5-5"/></svg>
                                @endif
                                @if($stage->is_lost)
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" class="text-red-500 ml-1"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                @endif
                            </div>
                            @if(!$loop->last)
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-notion-text-secondary opacity-30"><path d="m9 18 6-6-6-6"/></svg>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Create Pipeline Sidebar -->
    <div x-show="open" class="fixed inset-y-0 right-0 w-[550px] bg-card border-inline-start border-notion-border shadow-2xl z-50 flex flex-col" x-cloak>
        <div class="h-12 border-b border-notion-border ps-4 pe-4 flex items-center justify-between bg-notion-hover/20">
            <span class="text-sm font-bold tracking-tight" x-text="editMode ? '{{ __('Edit Pipeline') }}' : '{{ __('New Pipeline') }}'"></span>
            <button @click="open = false" class="p-1.5 hover:bg-notion-hover rounded transition-colors text-notion-text-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <form :action="editMode ? '/admin/pipelines/' + pipelineId : '{{ route('admin.pipelines.store') }}'" method="POST" class="flex-1 overflow-y-auto ps-12 pe-12 pb-12 pt-8 space-y-8">
            @csrf
            <template x-if="editMode">
                <input type="hidden" name="_method" value="PUT">
            </template>
            <div class="space-y-4">
                <div class="space-y-1">
                    <label class="text-[10px] text-notion-text-secondary font-bold uppercase tracking-widest">{{ __('Pipeline Name') }}</label>
                    <input type="text" name="name" x-model="newPipelineName" required class="notion-input w-full text-lg font-bold border-b border-transparent hover:border-notion-border focus:border-notion-blue transition-all" placeholder="{{ __('e.g. B2B Sales') }}">
                </div>

                <div class="space-y-4 pt-4">
                    <div class="flex items-center justify-between border-b border-notion-border pb-2">
                        <label class="text-[10px] text-notion-text-secondary font-bold uppercase tracking-widest">{{ __('Pipeline Stages') }}</label>
                        <button type="button" @click="addStage()" class="text-xs text-notion-blue hover:underline font-bold">+ {{ __('Add Stage') }}</button>
                    </div>
                    
                    <div class="space-y-3">
                        <template x-for="(stage, index) in stages" :key="index">
                            <div class="grid grid-cols-[20px_1fr_40px_100px_30px] items-center gap-3 group bg-notion-hover/10 p-1 rounded hover:bg-notion-hover/30 transition-all">
                                <template x-if="stage.id">
                                    <input type="hidden" :name="'stage_ids[' + index + ']'" :value="stage.id">
                                </template>
                                <span class="text-[10px] text-notion-text-secondary font-mono" x-text="index + 1"></span>
                                <input type="text" :name="'stages[' + index + ']'" x-model="stage.name" required class="notion-input flex-1 text-sm py-1 bg-transparent border-none focus:ring-0" placeholder="{{ __('Stage Name') }}">
                                <input type="color" :name="'colors[' + index + ']'" x-model="stage.color" class="w-6 h-6 rounded border-0 bg-transparent cursor-pointer p-0 overflow-hidden shadow-inner">
                                
                                <div class="flex items-center gap-4">
                                    <!-- Win Flag -->
                                    <label class="relative flex items-center cursor-pointer group/win" title="{{ __('Mark as Won') }}">
                                        <input type="checkbox" :name="'is_won[' + index + ']'" value="1" x-model="stage.is_won" @change="if(stage.is_won) stage.is_lost = false" class="hidden">
                                        <div class="w-7 h-7 rounded border border-notion-border flex items-center justify-center transition-all"
                                             :class="stage.is_won ? 'bg-green-500/20 border-green-500 text-green-500 shadow-lg shadow-green-500/10' : 'bg-notion-hover text-notion-text-secondary hover:border-notion-blue/50'">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                        </div>
                                    </label>
                                    <!-- Loss Flag -->
                                    <label class="relative flex items-center cursor-pointer group/loss" title="{{ __('Mark as Lost') }}">
                                        <input type="checkbox" :name="'is_lost[' + index + ']'" value="1" x-model="stage.is_lost" @change="if(stage.is_lost) stage.is_won = false" class="hidden">
                                        <div class="w-7 h-7 rounded border border-notion-border flex items-center justify-center transition-all"
                                             :class="stage.is_lost ? 'bg-red-500/20 border-red-500 text-red-500 shadow-lg shadow-red-500/10' : 'bg-notion-hover text-notion-text-secondary hover:border-red-500/50'">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                        </div>
                                    </label>
                                </div>

                                <button type="button" @click="removeStage(index)" class="opacity-0 group-hover:opacity-100 p-1.5 hover:bg-red-500/10 text-red-400 rounded transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <div class="pt-8 flex justify-end">
                <button type="submit" class="bg-notion-blue px-6 py-2 rounded-notion text-sm font-bold text-white hover:bg-blue-600 transition-all shadow-lg shadow-blue-500/20 active:scale-95">
                    <span x-text="editMode ? '{{ __('Save Changes') }}' : '{{ __('Create Pipeline') }}'"></span>
                </button>
            </div>
        </form>
    </div>
    <div x-show="open" @click="open = false" class="fixed inset-0 bg-[var(--color-overlay)] backdrop-blur-sm z-40 transition-all" x-cloak></div>
</div>
@endsection
