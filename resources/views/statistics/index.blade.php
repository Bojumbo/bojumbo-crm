@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto space-y-8 animate-in fade-in duration-500" x-data="{ 
    selectedManagerId: {{ auth()->id() }},
    managerStats: @js($managerStats),
    get selectedManager() {
        return this.managerStats.find(m => m.id == this.selectedManagerId) || this.managerStats[0];
    },
    expandedPipeline: null
}">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-white">{{ __('Statistics & Analytics') }}</h1>
            <p class="text-notion-text-secondary mt-1">{{ __('Real-time overview of your sales performance.') }}</p>
        </div>
        <form action="{{ route('statistics.index') }}" method="GET" class="flex flex-wrap items-center gap-3 bg-white/5 p-2 rounded-xl border border-white/5">
            <div class="flex items-center gap-2">
                <label class="text-[10px] uppercase tracking-widest text-notion-text-secondary font-bold ml-2">{{ __('From') }}</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="bg-notion-hover border-none rounded-md px-3 py-1.5 text-sm text-white focus:ring-1 focus:ring-notion-blue">
            </div>
            <div class="flex items-center gap-2">
                <label class="text-[10px] uppercase tracking-widest text-notion-text-secondary font-bold ml-2">{{ __('To') }}</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="bg-notion-hover border-none rounded-md px-3 py-1.5 text-sm text-white focus:ring-1 focus:ring-notion-blue">
            </div>
            <button type="submit" class="bg-notion-blue px-4 py-1.5 rounded-lg text-sm font-bold text-white hover:bg-blue-600 transition-all flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                {{ __('Filter') }}
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- BLOCK 1: Manager Stats -->
        <div class="lg:col-span-1 bg-notion-sidebar border border-white/5 rounded-2xl p-6 shadow-xl flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-sm font-bold text-notion-text-secondary uppercase tracking-widest">{{ __('Manager Stats') }}</h3>
                <select x-model="selectedManagerId" class="bg-notion-hover border-none rounded-lg text-xs text-white py-1 px-3 focus:ring-1 focus:ring-notion-blue">
                    <template x-for="manager in managerStats" :key="manager.id">
                        <option :value="manager.id" x-text="manager.name"></option>
                    </template>
                </select>
            </div>
            <div class="space-y-6">
                <div class="text-center py-4 bg-white/5 rounded-2xl border border-white/5">
                    <div class="text-[10px] text-notion-text-secondary uppercase font-bold">{{ __('Total Managed') }}</div>
                    <div class="text-3xl font-black text-white mt-1" x-text="selectedManager.total"></div>
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center p-3 bg-green-500/10 rounded-xl border border-green-500/20">
                        <div class="text-[9px] text-green-400 uppercase font-bold">{{ __('Won') }}</div>
                        <div class="text-xl font-bold text-green-500" x-text="selectedManager.won"></div>
                    </div>
                    <div class="text-center p-3 bg-notion-blue/10 rounded-xl border border-notion-blue/20">
                        <div class="text-[9px] text-notion-blue uppercase font-bold">{{ __('Work') }}</div>
                        <div class="text-xl font-bold text-notion-blue" x-text="selectedManager.in_work"></div>
                    </div>
                    <div class="text-center p-3 bg-red-500/10 rounded-xl border border-red-500/20">
                        <div class="text-[9px] text-red-400 uppercase font-bold">{{ __('Lost') }}</div>
                        <div class="text-xl font-bold text-red-500" x-text="selectedManager.lost"></div>
                    </div>
                </div>
                <!-- Mini conversion bar -->
                <div class="space-y-2">
                    <div class="flex justify-between text-[10px] font-bold uppercase tracking-tight">
                        <span class="text-notion-text-secondary">{{ __('Win Rate') }}</span>
                        <span class="text-green-500" x-text="selectedManager.total > 0 ? Math.round((selectedManager.won / selectedManager.total) * 100) + '%' : '0%'"></span>
                    </div>
                    <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden">
                        <div class="h-full bg-green-500 transition-all duration-500" :style="'width: ' + (selectedManager.total > 0 ? (selectedManager.won / selectedManager.total) * 100 : 0) + '%'"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BLOCK 2: Top Clients -->
        <div class="lg:col-span-2 bg-notion-sidebar border border-white/5 rounded-2xl shadow-xl flex flex-col overflow-hidden">
            <div class="px-6 py-5 border-b border-white/5 flex items-center justify-between">
                <h3 class="text-sm font-bold text-notion-text-secondary uppercase tracking-widest">{{ __('Top 10 Clients') }}</h3>
                <span class="text-[10px] text-notion-text-secondary uppercase">{{ __('By Deal Count') }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-white/[0.02] text-[10px] font-bold text-notion-text-secondary uppercase tracking-widest">
                            <th class="px-6 py-4">{{ __('Client Name') }}</th>
                            <th class="px-6 py-4 text-center">{{ __('Total') }}</th>
                            <th class="px-6 py-4 text-center text-green-500">{{ __('Won') }}</th>
                            <th class="px-6 py-4 text-center text-notion-blue">{{ __('Work') }}</th>
                            <th class="px-6 py-4 text-right">{{ __('Revenue') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($clientStats as $client)
                            <tr class="hover:bg-white/[0.03] transition-colors border-l-2 border-transparent hover:border-notion-blue">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-white">{{ $client['name'] }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-sm font-bold text-white">{{ $client['total'] }}</span>
                                </td>
                                <td class="px-6 py-4 text-center text-green-500 font-bold text-sm">
                                    {{ $client['won'] }}
                                </td>
                                <td class="px-6 py-4 text-center text-notion-blue font-bold text-sm">
                                    {{ $client['in_work'] }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-bold text-white">
                                    ${{ number_format($client['amount'], 0) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- BLOCK 3: Pipelines List -->
    <div class="space-y-4">
        <h3 class="text-sm font-bold text-notion-text-secondary uppercase tracking-widest ml-1">{{ __('Pipelines & Detailed Stages') }}</h3>
        <div class="grid grid-cols-1 gap-4">
            @foreach($performanceData as $p)
                <div class="bg-notion-sidebar border border-white/5 rounded-2xl overflow-hidden shadow-lg transition-all" 
                     :class="expandedPipeline === {{ $p['id'] }} ? 'ring-1 ring-notion-blue/50' : ''">
                    <!-- Pipeline Header -->
                    <div @click="expandedPipeline = (expandedPipeline === {{ $p['id'] }} ? null : {{ $p['id'] }})" 
                         class="p-6 cursor-pointer flex items-center justify-between hover:bg-white/5 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-notion-blue/10 flex items-center justify-center text-notion-blue">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-white">{{ $p['name'] }}</h4>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-xs text-notion-text-secondary">{{ __('Deals total') }}: <b class="text-white">{{ $p['total'] }}</b></span>
                                    <span class="w-1 h-1 rounded-full bg-white/20"></span>
                                    <span class="text-xs text-green-500 font-bold">{{ $p['won'] }} {{ __('Won') }}</span>
                                    <span class="w-1 h-1 rounded-full bg-white/20"></span>
                                    <span class="text-xs text-red-500 font-bold">{{ $p['lost'] }} {{ __('Lost') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="text-right hidden md:block">
                                <div class="text-[10px] text-notion-text-secondary font-bold uppercase">{{ __('Conversion') }}</div>
                                <div class="text-sm font-bold text-white">{{ $p['total'] > 0 ? round(($p['won'] / $p['total']) * 100, 1) : 0 }}%</div>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" 
                                 class="text-notion-text-secondary transition-transform duration-300"
                                 :class="expandedPipeline === {{ $p['id'] }} ? 'rotate-180' : ''">
                                <path d="m6 9 6 6 6-6"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Pipeline Detail (Expandable) -->
                    <div x-show="expandedPipeline === {{ $p['id'] }}" 
                         x-collapse
                         class="bg-black/20 border-t border-white/5 p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                            <div class="p-4 bg-white/5 rounded-xl border border-white/5">
                                <div class="text-[10px] text-notion-text-secondary font-bold uppercase">{{ __('New in Period') }}</div>
                                <div class="text-2xl font-bold text-white mt-1">{{ $p['total'] }}</div>
                            </div>
                            <div class="p-4 bg-notion-blue/10 rounded-xl border border-notion-blue/20">
                                <div class="text-[10px] text-notion-blue font-bold uppercase">{{ __('In Work Now') }}</div>
                                <div class="text-2xl font-bold text-notion-blue mt-1">{{ $p['in_work'] }}</div>
                            </div>
                            <div class="p-4 bg-green-500/10 rounded-xl border border-green-500/20">
                                <div class="text-[10px] text-green-400 font-bold uppercase">{{ __('Won') }}</div>
                                <div class="text-2xl font-bold text-green-500 mt-1">{{ $p['won'] }}</div>
                            </div>
                            <div class="p-4 bg-red-500/10 rounded-xl border border-red-500/20">
                                <div class="text-[10px] text-red-400 font-bold uppercase">{{ __('Lost') }}</div>
                                <div class="text-2xl font-bold text-red-500 mt-1">{{ $p['lost'] }}</div>
                            </div>
                        </div>

                        <h5 class="text-xs font-bold text-notion-text-secondary uppercase tracking-widest mb-4 ml-1">{{ __('Stage Breakdown') }}</h5>
                        <div class="space-y-3">
                            @foreach($p['stages'] as $stage)
                                <div class="flex items-center gap-4 group">
                                    <div class="w-32 shrink-0 truncate text-xs font-medium text-notion-text-secondary" title="{{ $stage['name'] }}">
                                        {{ $stage['name'] }}
                                    </div>
                                    <div class="flex-1 h-3 bg-white/5 rounded-full overflow-hidden relative">
                                        <div class="h-full bg-notion-blue/40 group-hover:bg-notion-blue/60 transition-all" 
                                             style="width: {{ $p['total'] > 0 ? ($stage['count'] / $p['total']) * 100 : 0 }}%"></div>
                                    </div>
                                    <div class="w-12 text-right text-xs font-bold text-white">
                                        {{ $stage['count'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
    .notion-sidebar { background-color: #202020; }
    [x-cloak] { display: none !important; }
</style>
@endsection
