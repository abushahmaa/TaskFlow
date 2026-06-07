<x-guest-layout>
    <x-slot name="title">Sign In</x-slot>

    <div class="mb-8">
        <h3 class="text-2xl font-bold text-white mb-2">Welcome back</h3>
        <p class="text-slate-400 text-sm">Enter your credentials to access your account.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="label text-slate-300">Email address</label>
            <input id="email" class="input bg-slate-900 border-slate-700 text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-indigo-500" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="name@company.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-sm font-medium text-slate-300">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-xs font-medium text-indigo-400 hover:text-indigo-300 transition-colors" href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>
            
            <input id="password" class="input bg-slate-900 border-slate-700 text-white placeholder-slate-500 focus:border-indigo-500 focus:ring-indigo-500"
                            type="password"
                            name="password"
                            required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400" />
        </div>

        <!-- Remember Me -->
        <div class="block">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded bg-slate-900 border-slate-700 text-indigo-600 shadow-sm focus:ring-indigo-500/20" name="remember">
                <span class="text-sm text-slate-400">Remember me</span>
            </label>
        </div>

        <div class="pt-2">
            <button class="btn-primary w-full justify-center py-2.5 text-base" type="submit">
                Sign In
            </button>
        </div>
        
        <div class="mt-6 pt-6 border-t border-slate-800">
            <p class="text-sm text-slate-400 mb-3">Test Accounts (password: password)</p>
            <div class="flex flex-wrap gap-2 text-xs">
                <span class="px-2 py-1 bg-slate-900 border border-slate-800 rounded text-slate-300">admin@taskflow.com</span>
                <span class="px-2 py-1 bg-slate-900 border border-slate-800 rounded text-slate-300">alice.pm@taskflow.com</span>
                <span class="px-2 py-1 bg-slate-900 border border-slate-800 rounded text-slate-300">charlie@taskflow.com</span>
            </div>
        </div>
    </form>
</x-guest-layout>
