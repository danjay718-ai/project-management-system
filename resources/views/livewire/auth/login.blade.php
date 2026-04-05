<div class="grid grid-cols-1 lg:grid-cols-2 min-h-screen bg-gray-50 border-t-4 border-indigo-600">
    <!-- Left Column: Form -->
    <div class="flex flex-col justify-center px-8 sm:px-12 lg:px-24">
        <div class="mb-10 text-center lg:text-left">
            <x-application-logo class="w-12 h-12 text-indigo-600 mx-auto lg:mx-0 fill-current" />
            <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900">Sign in to your Workspace</h2>
            <p class="mt-2 text-sm text-gray-600">Manage tasks, collaborate efficiently, and ship faster.</p>
        </div>

        <form wire:submit="login" class="space-y-6 max-w-md mx-auto lg:mx-0 w-full">
            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                <div class="mt-2">
                    <!-- Note the wire:model here. This synchronizes the input directly with Livewire backend! -->
                    <input id="email" wire:model="email" type="email" required autofocus
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
                @error('email') <span class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</span> @enderror
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <div class="text-sm">
                        <!-- Using wire:navigate makes this transition instant like a SPA -->
                        <a href="{{ route('password.request') }}" wire:navigate class="font-semibold text-indigo-600 hover:text-indigo-500">Forgot password?</a>
                    </div>
                </div>
                <div class="mt-2">
                    <input id="password" wire:model="password" type="password" required
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                </div>
                @error('password') <span class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</span> @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <input id="remember" wire:model="remember" type="checkbox"
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                <label for="remember" class="ml-3 block text-sm leading-6 text-gray-900">Remember me</label>
            </div>

            <div>
                <!-- Display a loading state on the button when wire:submit is executing to prevent doubles -->
                <button type="submit" wire:loading.attr="disabled"
                    class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-50">
                    <span wire:loading.remove wire:target="login">Sign in</span>
                    <span wire:loading wire:target="login">Authenticating...</span>
                </button>
            </div>
            
            <p class="text-center text-sm text-gray-500">
                Don't have an account?
                <a href="{{ route('register') }}" wire:navigate class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">Create a workspace</a>
            </p>
        </form>
    </div>

    <!-- Right Column: Project Management Art -->
    <div class="hidden lg:flex relative w-full h-full bg-slate-900 items-center justify-center p-12 overflow-hidden">
        <!-- Abstract gradient background for visual interest using Tailwind v4 -->
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-slate-800 to-cyan-700 opacity-60"></div>
        
        <!-- Live demonstration of a Project Management aesthetic UI block -->
        <div class="relative z-10 w-full max-w-xl mx-auto rounded-xl p-8 bg-white/10 backdrop-blur-md border border-white/20 shadow-2xl">
            <h3 class="text-2xl font-bold text-white mb-6">Kanban Overview</h3>
            
            <!-- Mock Kanban Board purely with Tailwind Grid ->
            <div class="grid grid-cols-3 gap-4">
                <!-- Column 1 -->
                <div class="bg-slate-800/80 rounded-lg p-4 flex flex-col gap-3">
                    <h4 class="text-sm font-semibold text-indigo-300 uppercase tracking-wider">To Do</h4>
                    <div class="bg-white/5 rounded p-3 text-sm text-gray-300 border border-white/10 shadow-sm blur-[0.5px]">API Implementation</div>
                    <div class="bg-white/5 rounded p-3 text-sm text-gray-300 border border-white/10 shadow-sm blur-[0.5px]">Database Schema Design</div>
                </div>
                
                <!-- Column 2 -->
                <div class="bg-slate-800/80 rounded-lg p-4 flex flex-col gap-3">
                    <h4 class="text-sm font-semibold text-yellow-300 uppercase tracking-wider">In Progress</h4>
                    <div class="bg-indigo-600/30 rounded p-3 text-sm text-white border border-indigo-500/50 shadow-md transform hover:scale-105 transition-transform cursor-pointer">
                        <div class="h-2 w-12 bg-indigo-500 rounded mb-2"></div>
                        Livewire Authentication Flow
                    </div>
                </div>

                <!-- Column 3 -->
                <div class="bg-slate-800/80 rounded-lg p-4 flex flex-col gap-3">
                    <h4 class="text-sm font-semibold text-emerald-300 uppercase tracking-wider">Done</h4>
                    <div class="bg-emerald-900/40 rounded p-3 text-sm text-gray-300 border border-emerald-500/30 shadow-sm blur-[0.5px] line-through decoration-emerald-500/50">Repository Architecture setup</div>
                </div>
            </div>
            
            <p class="mt-8 text-indigo-100 font-light text-lg">"Built for scale, designed for speed. Orchestrate your team's workflow in real-time."</p>
        </div>
    </div>
</div>
