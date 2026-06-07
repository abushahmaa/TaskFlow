<x-app-layout>
    <x-slot name="pageTitle">{{ $title }}</x-slot>

    <div class="card p-12 flex flex-col items-center justify-center text-center">
        <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
            <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
        </div>
        <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-2">{{ $title }} Module</h3>
        <p class="text-slate-500 max-w-sm mx-auto">
            This module is fully functional via the API endpoints. The web UI view is currently a placeholder and can be implemented in future phases.
        </p>
    </div>
</x-app-layout>
