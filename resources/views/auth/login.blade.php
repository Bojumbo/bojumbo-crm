<html lang="en">
<head>
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Bojumbo CRM</title>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-canvas text-notion-text-primary antialiased flex items-center justify-center min-h-screen p-4" x-data="{ 
    dark: document.documentElement.classList.contains('dark'),
    toggleTheme() {
        this.dark = !this.dark;
        if (this.dark) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }
    }
}">

    <div class="w-full max-w-[320px] space-y-8">
        <!-- Theme Switcher -->
        <div class="flex justify-center">
            <div class="bg-card border border-notion-border rounded-full p-1 flex gap-1 shadow-sm">
                <button @click="toggleTheme()" :class="!dark ? 'bg-notion-blue text-white' : 'text-notion-text-secondary hover:bg-notion-hover'" class="px-3 py-1 rounded-full text-[10px] font-bold transition-all flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
                    LIGHT
                </button>
                <button @click="toggleTheme()" :class="dark ? 'bg-notion-blue text-white' : 'text-notion-text-secondary hover:bg-notion-hover'" class="px-3 py-1 rounded-full text-[10px] font-bold transition-all flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                    DARK
                </button>
            </div>
        </div>

        <!-- Logo -->
        <div class="flex flex-col items-center gap-4">
            <div class="w-12 h-12 bg-notion-blue rounded-xl flex items-center justify-center text-white text-2xl font-bold shadow-lg shadow-notion-blue/20">B</div>
            <h1 class="text-2xl font-bold tracking-tight">Log in to Bojumbo</h1>
        </div>

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="space-y-1">
                <label class="text-xs text-notion-text-secondary ml-1">Email address</label>
                <input type="email" name="email" required autofocus
                    class="w-full bg-card border border-notion-border rounded-notion px-3 py-2 text-sm focus:ring-1 focus:ring-notion-blue/50 outline-none transition-all placeholder:text-white/10"
                    placeholder="name@company.com">
                @error('email')
                    <p class="text-xs text-red-500 mt-1 ml-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1">
                <label class="text-xs text-notion-text-secondary ml-1">Password</label>
                <input type="password" name="password" required
                    class="w-full bg-card border border-notion-border rounded-notion px-3 py-2 text-sm focus:ring-1 focus:ring-notion-blue/50 outline-none transition-all placeholder:text-white/10"
                    placeholder="••••••••">
            </div>

            <div class="flex items-center justify-between ml-1">
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" name="remember" class="w-3 h-3 rounded bg-card border-notion-border text-notion-blue focus:ring-0">
                    <span class="text-[11px] text-notion-text-secondary group-hover:text-notion-text-primary transition-colors">Remember me</span>
                </label>
                <a href="#" class="text-[11px] text-notion-text-secondary hover:text-notion-blue transition-colors">Forgot password?</a>
            </div>

            <button type="submit" class="w-full bg-notion-blue hover:bg-blue-600 text-white text-sm font-semibold py-2 rounded-notion transition-all shadow-sm shadow-blue-900/20 active:scale-[0.98]">
                Continue
            </button>
        </form>

        <div class="pt-4 text-center">
            <p class="text-xs text-notion-text-secondary">
                New to Bojumbo? <a href="#" class="text-notion-blue hover:underline">Create an account</a>
            </p>
        </div>
    </div>

</body>
</html>
