<!-- Header -->
<header class="h-20 bg-white/70 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-6 lg:px-10 z-10 shrink-0">
    <div class="flex items-center">
        <button @click="sidebarOpen = true" class="text-slate-500 focus:outline-none lg:hidden mr-4">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <h1 class="text-2xl font-semibold text-slate-800 hidden sm:block">{{ $title ?? 'Overview' }}</h1>
    </div>

    <!-- Header Right -->
    <div class="flex items-center gap-6">
        <!-- Search -->
        <div class="relative hidden py-1 md:block">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none"><path d="M21 21L15 15M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
            </span>
            <input type="text" class="w-full py-2 pl-10 pr-4 text-sm text-slate-700 bg-slate-100 border-none rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all" placeholder="Search...">
        </div>

        <!-- Notifications -->
        <button class="relative text-slate-400 hover:text-slate-600 transition-colors">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <span class="absolute top-0 right-0 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white"></span>
        </button>
    </div>
</header>
