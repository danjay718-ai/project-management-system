<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Project;

new #[Layout('layouts.app')] #[Title('Admin Dashboard')] class extends Component
{
    public array $stats = [];
    public array $recentActivities = [];

    public function mount()
    {
        $this->stats = [
            ['title' => 'Total Users', 'value' => User::count(), 'increase' => 'Active', 'trend' => 'neutral', 'icon' => 'user-group'],
            ['title' => 'Active Roles', 'value' => Role::count(), 'increase' => 'Defined', 'trend' => 'neutral', 'icon' => 'shield-check'],
            ['title' => 'Permissions', 'value' => Permission::count(), 'increase' => 'System', 'trend' => 'neutral', 'icon' => 'key'],
            ['title' => 'Active Projects', 'value' => Project::count(), 'increase' => 'Ongoing', 'trend' => 'neutral', 'icon' => 'briefcase'],
        ];

        $this->recentActivities = User::latest()->take(4)->get()->map(function($user) {
            return [
                'user' => $user->name, 
                'email' => $user->email, 
                'action' => 'Registered', 
                'target' => 'System', 
                'time' => $user->created_at->diffForHumans(), 
                'status' => 'Success'
            ];
        })->toArray();
    }
};
?>

<div>
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