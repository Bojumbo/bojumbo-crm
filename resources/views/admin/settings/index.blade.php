@extends('admin.settings.layout')
@section('settings_content')
<div class="space-y-6">
    <div class="bg-card border border-notion-border rounded-notion overflow-hidden shadow-sm">
        <div class="p-6 border-b border-notion-border bg-notion-hover/20">
            <h3 class="font-bold text-lg text-notion-text-primary">{{ __('General System Settings') }}</h3>
            <p class="text-xs text-notion-text-secondary">{{ __('Configure global defaults for the CRM.') }}</p>
        </div>
        <form action="{{ route('admin.settings.update') }}" method="POST" 
              x-data="{ selectedCurrency: '{{ $currentCurrency }}' }">
            @csrf
            <div class="p-6 space-y-12">
                <!-- Currency Setting -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                    <div class="space-y-1">
                        <h4 class="font-medium text-notion-text-primary uppercase text-[11px] tracking-wider">{{ __('System Currency') }}</h4>
                        <p class="text-[12px] text-notion-text-secondary leading-relaxed">{{ __('Select the default currency symbol used for Deals and Products across the workspace.') }}</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($currencies as $symbol => $label)
                            <label class="relative flex items-center gap-3 p-3 rounded-notion border cursor-pointer transition-all"
                                :class="selectedCurrency === '{{ $symbol }}' ? 'bg-notion-blue/10 border-notion-blue ring-1 ring-notion-blue/30' : 'bg-notion-hover/30 border-notion-border hover:bg-notion-hover'">
                                <input type="radio" name="crm_currency" value="{{ $symbol }}" x-model="selectedCurrency" class="hidden">
                                <span class="w-8 h-8 flex items-center justify-center rounded text-lg font-mono transition-colors"
                                      :class="selectedCurrency === '{{ $symbol }}' ? 'bg-notion-blue text-white' : 'bg-notion-hover text-notion-text-secondary'">
                                    {{ $symbol }}
                                </span>
                                <div class="flex-1">
                                    <div class="text-sm font-medium" :class="selectedCurrency === '{{ $symbol }}' ? 'text-notion-blue' : 'text-notion-text-primary'">
                                        {{ $label }}
                                    </div>
                                </div>
                                <template x-if="selectedCurrency === '{{ $symbol }}'">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="text-notion-blue"><path d="M20 6 9 17l-5-5"/></svg>
                                </template>
                            </label>
                        @endforeach
                    </div>
                </div>
                <!-- Google Integration -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start pt-8 border-t border-notion-border">
                    <div class="space-y-1">
                        <h4 class="font-medium text-notion-text-primary uppercase text-[11px] tracking-wider">{{ __('Google Integration') }}</h4>
                        <p class="text-[12px] text-notion-text-secondary leading-relaxed">{{ __('Connect your Google Drive to generate documents from templates.') }}</p>
                    </div>
                    
                    <div class="space-y-4">
                        @if(auth()->user()->google_refresh_token)
                            <div class="group relative flex items-center gap-4 p-4 rounded-notion border border-green-500/20 bg-green-500/5 transition-all hover:bg-green-500/10">
                                <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center text-green-500 shadow-lg shadow-green-500/10">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-semibold text-notion-text-primary tracking-tight">{{ __('Google Account Connected') }}</div>
                                    <div class="text-[11px] text-notion-text-secondary mt-0.5">{{ __('You can now generate documents from Google Doc templates.') }}</div>
                                </div>
                                <a href="{{ route('google.connect') }}" class="text-[11px] font-medium text-notion-text-secondary hover:text-notion-text-primary transition-colors bg-notion-hover/30 hover:bg-notion-hover px-3 py-1.5 rounded-notion border border-notion-border">
                                    {{ __('Reconnect') }}
                                </a>
                            </div>
                        @else
                            <a href="{{ route('google.connect') }}" class="group flex items-center gap-4 p-4 rounded-notion border border-notion-border bg-notion-hover/30 hover:bg-notion-hover/60 hover:border-notion-blue/30 transition-all">
                                <div class="w-10 h-10 rounded-full bg-notion-hover group-hover:bg-notion-blue/10 flex items-center justify-center text-notion-text-secondary group-hover:text-notion-blue transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
                                </div>
                                <div class="flex-1 text-left">
                                    <div class="text-sm font-medium text-notion-text-primary group-hover:text-notion-blue transition-colors">{{ __('Connect Google Drive') }}</div>
                                    <div class="text-[11px] text-notion-text-secondary">{{ __('Authorize access to manage contract templates.') }}</div>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-notion-text-secondary"><path d="m9 18 6-6-6-6"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
                <!-- Drive Storage Settings -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start pt-8 border-t border-notion-border">
                    <div class="space-y-1">
                        <h4 class="font-medium text-notion-text-primary uppercase text-[11px] tracking-wider">{{ __('Drive Storage Settings') }}</h4>
                        <p class="text-[12px] text-notion-text-secondary leading-relaxed">{{ __('Specify the Root Folder ID on Google Drive where all deal folders will be created.') }}</p>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-notion-text-secondary uppercase">{{ __('Root Folder ID') }}</label>
                            <input type="text" name="google_drive_root_folder_id" 
                                   value="{{ \App\Models\Setting::get('google_drive_root_folder_id') }}"
                                   class="notion-input w-full text-sm font-mono" placeholder="1abc...XYZ">
                            <p class="text-[10px] text-notion-text-secondary italic mt-1">{{ __('Copy the ID from the folder URL in Google Drive.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-notion-hover/20 border-t border-notion-border px-6 py-4 flex justify-end">
                <button type="submit" class="bg-notion-blue px-6 py-2 rounded-notion text-sm font-medium text-white hover:bg-blue-600 transition-all shadow-lg shadow-blue-500/20 active:scale-95">
                    {{ __('Save Changes') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection