<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Admin Dashboard')] class extends Component
{
    public $stats = [
        ['title' => 'Total Users', 'value' => '1,429', 'increase' => '+12%', 'trend' => 'up', 'icon' => 'user-group'],
        ['title' => 'Active Roles', 'value' => '8', 'increase' => 'No change', 'trend' => 'neutral', 'icon' => 'shield-check'],
        ['title' => 'Permissions', 'value' => '42', 'increase' => '+3', 'trend' => 'up', 'icon' => 'key'],
        ['title' => 'Active Projects', 'value' => '12', 'increase' => '-5%', 'trend' => 'down', 'icon' => 'briefcase'],
    ];

    public $recentActivities = [
        ['user' => 'Sarah Connor', 'email' => 'sarah@example.com', 'action' => 'Assigned Role', 'target' => 'Admin', 'time' => '2 mins ago', 'status' => 'Success'],
        ['user' => 'John Doe', 'email' => 'john@example.com', 'action' => 'Updated Policy', 'target' => 'User Management', 'time' => '1 hour ago', 'status' => 'Pending'],
        ['user' => 'Jane Smith', 'email' => 'jane@example.com', 'action' => 'Created Project', 'target' => 'Alpha Phase', 'time' => '3 hours ago', 'status' => 'Success'],
        ['user' => 'Mike Ross', 'email' => 'mike@example.com', 'action' => 'Revoked Access', 'target' => 'Beta API', 'time' => '5 hours ago', 'status' => 'Failed'],
    ];
};
?>

<div class="h-screen flex overflow-hidden bg-slate-50 font-sans text-slate-900" x-data="{ sidebarOpen: false }">
    
    <!-- Mobile sidebar backdrop -->
    <div x-show="sidebarOpen" class="fixed inset-0 z-20 bg-black/50 transition-opacity lg:hidden" @click="sidebarOpen = false" x-transition.opacity></div>

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-30 w-64 bg-slate-900 transition-transform duration-300 lg:static lg:translate-x-0 flex flex-col border-r border-slate-800">
        <div class="flex items-center justify-center h-20 border-b border-slate-800 px-6">
            <span class="text-2xl font-bold text-white tracking-wider flex items-center gap-2">
                <svg class="w-8 h-8 text-indigo-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                RBAC<span class="text-indigo-400">Pro</span>
            </span>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto w-full">
            <a href="#" class="flex items-center px-4 py-3 text-white bg-indigo-600 rounded-xl transition-all shadow-lg shadow-indigo-600/30 font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl transition-all font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Users
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl transition-all font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Roles
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl transition-all font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                Permissions
            </a>
            <a href="{{ route('project.list') }}" class="flex items-center px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-xl transition-all font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Projects
            </a>
        </nav>
        
        <!-- User Profile Area in Sidebar -->
        <div class="px-6 py-6 border-t border-slate-800">
            <div class="flex items-center gap-3">
                <img class="w-10 h-10 rounded-full border-2 border-slate-700 p-0.5" src="https://ui-avatars.com/api/?name=Admin+User&background=6366f1&color=fff" alt="User">
                <div>
                    <p class="text-sm font-medium text-white">{{ auth()->user()->name ?? 'Admin User' }}</p>
                    <p class="text-xs text-slate-400">System Administrator</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="w-full text-left text-sm text-slate-400 hover:text-white flex items-center transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden">
        
        <!-- Header -->
        <header class="h-20 bg-white/70 backdrop-blur-md border-b border-slate-200 flex items-center justify-between px-6 lg:px-10 z-10">
            <div class="flex items-center">
                <button @click="sidebarOpen = true" class="text-slate-500 focus:outline-none lg:hidden mr-4">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-2xl font-semibold text-slate-800 hidden sm:block">Overview</h1>
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

        <!-- Page Content -->
        <div class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-6 lg:p-10">
            
            <!-- Welcome Banner -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 mb-8 text-white shadow-xl shadow-indigo-200 flex items-center justify-between relative overflow-hidden">
                <div class="relative z-10">
                    <h2 class="text-3xl font-bold mb-2">Welcome back, {{ auth()->user()->name ?? 'Admin' }}! 👋</h2>
                    <p class="text-indigo-100">Here's what's happening with your projects today.</p>
                </div>
                <!-- Decorative SVG -->
                <svg class="absolute right-0 bottom-0 top-0 h-full opacity-20 transform translate-x-1/4 scale-150" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <circle cx="50" cy="50" r="40" stroke="currentColor" stroke-width="4" stroke-dasharray="10 10"/>
                  <circle cx="50" cy="50" r="25" stroke="currentColor" stroke-width="4" stroke-dasharray="5 5"/>
                </svg>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
                @foreach($stats as $stat)
                <div class="flex items-center p-6 bg-white rounded-2xl shadow-sm border border-slate-100 hover:shadow-md transition-shadow group">
                    <div class="p-4 mr-4 text-indigo-600 bg-indigo-50 rounded-xl group-hover:scale-110 transition-transform">
                        @if($stat['icon'] == 'user-group')
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        @elseif($stat['icon'] == 'shield-check')
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        @elseif($stat['icon'] == 'key')
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" /></svg>
                        @else
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        @endif
                    </div>
                    <div>
                        <p class="mb-1 text-sm font-medium text-slate-500">{{ $stat['title'] }}</p>
                        <p class="text-2xl font-bold text-slate-800">{{ $stat['value'] }}</p>
                    </div>
                    <div class="ml-auto flex items-center text-sm font-bold {{ $stat['trend'] == 'up' ? 'text-emerald-500' : ($stat['trend'] == 'down' ? 'text-rose-500' : 'text-slate-400') }}">
                        {{ $stat['increase'] }}
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Activity Table -->
            <div class="bg-white border border-slate-100 shadow-sm rounded-2xl overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="text-lg font-semibold text-slate-800">Recent Activity</h3>
                    <button class="text-sm font-medium text-indigo-600 hover:text-indigo-700">View All</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full whitespace-nowrap">
                        <thead class="bg-slate-50 border-b border-slate-100 text-xs uppercase tracking-wider text-slate-500 font-semibold text-left">
                            <tr>
                                <th class="px-6 py-4">User</th>
                                <th class="px-6 py-4">Action</th>
                                <th class="px-6 py-4">Target</th>
                                <th class="px-6 py-4">Time</th>
                                <th class="px-6 py-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @foreach($recentActivities as $activity)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs">
                                            {{ substr($activity['user'], 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-slate-900">{{ $activity['user'] }}</p>
                                            <p class="text-xs text-slate-500">{{ $activity['email'] }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-700">{{ $activity['action'] }}</td>
                                <td class="px-6 py-4 text-slate-500">{{ $activity['target'] }}</td>
                                <td class="px-6 py-4 text-slate-400">{{ $activity['time'] }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full {{ $activity['status'] == 'Success' ? 'bg-emerald-100 text-emerald-700' : ($activity['status'] == 'Pending' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                        {{ $activity['status'] }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>