<x-app-layout>
    <x-slot name="title">Access Denied</x-slot>

    <div class="flex items-center justify-center min-h-[calc(100vh-8rem)]">
        <div class="text-center px-4 w-full max-w-lg bg-white rounded-3xl p-10 shadow-sm border border-slate-100">
            <div class="w-24 h-24 mx-auto bg-rose-50 rounded-full flex items-center justify-center mb-8 border-4 border-rose-100">
                <svg class="w-12 h-12 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight sm:text-4xl mb-4">
                Access Denied
            </h1>
            
            <p class="text-sm text-slate-500 mb-8 leading-relaxed">
                You don't have the required permissions to view this page or perform this action. If you believe this is a mistake, please reach out to your administrator to request access.
            </p>
            
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 text-sm font-semibold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-lg shadow-indigo-600/30">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Return to Dashboard
            </a>
        </div>
    </div>
</x-app-layout>
