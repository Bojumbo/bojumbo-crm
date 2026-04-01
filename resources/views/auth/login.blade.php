<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Bojumbo CRM</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-canvas text-notion-text-primary antialiased flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-[320px] space-y-8">
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
