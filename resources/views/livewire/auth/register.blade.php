<div class="grid grid-cols-1 lg:grid-cols-2 min-h-screen bg-gray-50 border-t-4 border-cyan-600">
    <!-- Left Column: Form -->
    <div class="flex flex-col justify-center px-8 sm:px-12 lg:px-24">
        <div class="mb-10 text-center lg:text-left">
            <x-application-logo class="w-12 h-12 text-cyan-600 mx-auto lg:mx-0 fill-current" />
            <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900">Initialize Your Workspace</h2>
            <p class="mt-2 text-sm text-gray-600">Join our platform and orchestrate your projects flawlessly.</p>
        </div>

        <form wire:submit="register" class="space-y-5 max-w-md mx-auto lg:mx-0 w-full">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                <div class="mt-2">
                    <!-- using wire:model.blur validates the field exactly when it loses focus! a great trick for registering. -->
                    <input id="name" wire:model.blur="name" type="text" required autofocus
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-cyan-600 sm:text-sm sm:leading-6">
                </div>
                @error('name') <span class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</span> @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                <div class="mt-2">
                    <input id="email" wire:model.blur="email" type="email" required
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-cyan-600 sm:text-sm sm:leading-6">
                </div>
                @error('email') <span class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</span> @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <div class="mt-2">
                    <input id="password" wire:model.blur="password" type="password" required
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-cyan-600 sm:text-sm sm:leading-6">
                </div>
                @error('password') <span class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</span> @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <div class="mt-2">
                    <input id="password_confirmation" wire:model.blur="password_confirmation" type="password" required
                        class="block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-cyan-600 sm:text-sm sm:leading-6">
                </div>
                @error('password_confirmation') <span class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</span> @enderror
            </div>

            <div class="pt-2">
                <button type="submit" wire:loading.attr="disabled"
                    class="flex w-full justify-center rounded-md bg-cyan-600 px-3 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-cyan-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-600 disabled:opacity-50 transition-colors">
                    <span wire:loading.remove wire:target="register">Create Account</span>
                    <span wire:loading wire:target="register">Setting Up Workspace...</span>
                </button>
            </div>
            
            <p class="text-center text-sm text-gray-500">
                Already registered?
                <a href="{{ route('login') }}" wire:navigate class="font-semibold leading-6 text-cyan-600 hover:text-cyan-500">Sign in to existing workspace</a>
            </p>
        </form>
    </div>

    <!-- Right Column: Project Management Art -->
    <div class="hidden lg:flex relative w-full h-full bg-slate-900 items-center justify-center p-12 overflow-hidden">
        <!-- Abstract gradient background for visual interest using Tailwind v4 -->
        <div class="absolute inset-0 bg-gradient-to-bl from-cyan-600 via-slate-800 to-indigo-700 opacity-60"></div>
        
        <div class="relative z-10 w-full max-w-xl mx-auto">
            <!-- Mock metrics dashboard purely with Tailwind -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-5 border border-white/20 hover:bg-white/20 transition-colors">
                    <h5 class="text-sm font-medium text-gray-300 uppercase tracking-widest">Active Projects</h5>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-4xl font-bold text-white">42</span>
                        <span class="text-xs text-emerald-400">↑ 12%</span>
                    </div>
                </div>
                <div class="bg-white/10 backdrop-blur-md rounded-xl p-5 border border-white/20 hover:bg-white/20 transition-colors">
                    <h5 class="text-sm font-medium text-gray-300 uppercase tracking-widest">Team Velocity</h5>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-4xl font-bold text-white">128</span>
                        <span class="text-xs text-white/50">pts/sprint</span>
                    </div>
                </div>
            </div>

            <!-- Activity Feed Illusion -->
            <div class="bg-white/5 backdrop-blur-sm rounded-xl p-6 border border-white/10">
                <h4 class="text-lg font-semibold text-white mb-4">Recent Commits</h4>
                <div class="space-y-4">
                    <div class="flex items-center gap-3 opacity-90">
                        <div class="w-8 h-8 rounded-full bg-cyan-500/20 flex items-center justify-center text-cyan-300 text-xs border border-cyan-500/50">JD</div>
                        <div class="flex-1">
                            <div class="h-2 w-32 bg-white/20 rounded mb-2"></div>
                            <div class="h-1.5 w-24 bg-white/10 rounded"></div>
                        </div>
                        <span class="text-xs text-white/40">2m ago</span>
                    </div>
                    <div class="flex items-center gap-3 opacity-70">
                        <div class="w-8 h-8 rounded-full bg-indigo-500/20 flex items-center justify-center text-indigo-300 text-xs border border-indigo-500/50">SM</div>
                        <div class="flex-1">
                            <div class="h-2 w-48 bg-white/20 rounded mb-2"></div>
                            <div class="h-1.5 w-16 bg-white/10 rounded"></div>
                        </div>
                        <span class="text-xs text-white/40">1h ago</span>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
