<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Login' }} — TaskFlow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-950 min-h-screen flex">

    {{-- Left decorative panel --}}
    <div class="hidden lg:flex lg:w-1/2 relative bg-gradient-to-br from-indigo-900 via-slate-900 to-slate-950 overflow-hidden flex-col justify-between p-12">
        {{-- Background pattern --}}
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-72 h-72 bg-indigo-500 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-violet-600 rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10">
            {{-- Logo --}}
            <div class="flex items-center gap-3 mb-16">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-500/30">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <span class="text-2xl font-bold text-white">TaskFlow</span>
            </div>

            {{-- Hero text --}}
            <h2 class="text-4xl font-bold text-white leading-tight mb-4">
                Manage projects<br>with confidence.
            </h2>
            <p class="text-slate-400 text-lg leading-relaxed">
                Role-based access, deadline notifications, work logs, and real-time progress tracking — all in one place.
            </p>
        </div>

        {{-- Feature list --}}
        <div class="relative z-10 space-y-4">
            @foreach([
                ['icon' => '🛡️', 'title' => 'Role-Based Access Control', 'desc' => 'Admin, Project Manager & Employee roles'],
                ['icon' => '🔔', 'title' => 'Smart Deadline Alerts', 'desc' => '48h / 24h / 12h / 1h before deadline'],
                ['icon' => '📊', 'title' => 'Progress Reports', 'desc' => 'Completion %, employee metrics'],
            ] as $feature)
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 bg-white/10 rounded-lg flex items-center justify-center text-base flex-shrink-0">{{ $feature['icon'] }}</div>
                <div>
                    <p class="text-white font-medium text-sm">{{ $feature['title'] }}</p>
                    <p class="text-slate-400 text-xs mt-0.5">{{ $feature['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Right auth panel --}}
    <div class="flex-1 flex items-center justify-center p-8 bg-slate-950">
        <div class="w-full max-w-md">
            {{-- Mobile logo --}}
            <div class="lg:hidden flex items-center justify-center gap-3 mb-8">
                <div class="w-9 h-9 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <span class="text-xl font-bold text-white">TaskFlow</span>
            </div>

            {{ $slot }}
        </div>
    </div>
</body>
</html>
