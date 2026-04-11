<div x-data="{
    tab: $wire.entangle('taskView'),
    showMembers: $wire.entangle('showMembers'),
    taskModal: $wire.entangle('showTaskModal'),
    deleteTaskModal: $wire.entangle('showDeleteTaskModal')
}" class="min-h-full">

    {{-- ── Breadcrumbs & Header ────────────────────────────────────────────── --}}
    <div class="mb-8">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm font-medium">
                <li class="inline-flex items-center">
                    <a href="{{ route('project.list') }}" wire:navigate class="text-slate-500 hover:text-indigo-600 flex items-center gap-1.5 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        Projects
                    </a>
                </li>
                <li>
                    <div class="flex items-center text-slate-400">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                        <span class="ml-1 md:ml-2 text-slate-800 font-semibold">{{ $project->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-white rounded-2xl border border-slate-100 shadow-sm">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">{{ $project->name }}</h1>
                    <div class="flex items-center gap-3 mt-1">
                        @php
                            $pBadge = match($project->status) {
                                'active'   => 'bg-emerald-100 text-emerald-700',
                                'inactive' => 'bg-amber-100 text-amber-700',
                                'archived' => 'bg-slate-100 text-slate-500',
                                default    => 'bg-slate-100 text-slate-500',
                            };
                        @endphp
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] uppercase font-bold tracking-wider {{ $pBadge }}">
                            {{ $project->status }}
                        </span>
                        <span class="text-xs text-slate-400 font-medium flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            {{ $project->owner->name }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                {{-- View Toggle --}}
                <div class="bg-white border border-slate-100 rounded-xl p-1 flex shadow-sm mr-2">
                    <button
                        @click="tab = 'table'"
                        class="p-2 rounded-lg transition-all"
                        :class="tab === 'table' ? 'bg-indigo-50 text-indigo-600 shadow-sm' : 'text-slate-400 hover:text-slate-600'"
                        title="Table View"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    </button>
                    <button
                        @click="tab = 'kanban'"
                        class="p-2 rounded-lg transition-all"
                        :class="tab === 'kanban' ? 'bg-indigo-50 text-indigo-600 shadow-sm' : 'text-slate-400 hover:text-slate-600'"
                        title="Board View"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h2a2 2 0 00-2 2"/></svg>
                    </button>
                </div>

                {{-- Members Toggle --}}
                <button
                    wire:click="toggleMembers"
                    class="p-2.5 rounded-xl border border-slate-100 shadow-sm transition-all"
                    :class="showMembers ? 'bg-indigo-600 text-white shadow-indigo-600/30 ring-2 ring-indigo-500/20' : 'bg-white text-slate-500 hover:text-indigo-600 hover:bg-slate-50'"
                    title="Manage Members"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>

                {{-- New Task Button --}}
                @can('create', [App\Models\Task::class, $project])
                <button
                    wire:click="openTaskCreate"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-600/30 hover:bg-indigo-700 active:scale-95 transition-all"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Add Task
                </button>
                @endcan
            </div>
        </div>
    </div>

    {{-- ── Flash Messages ─────────────────────────────────────────────────── --}}
    @if (session()->has('task-success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="fixed bottom-6 right-6 z-50 flex items-center gap-3 bg-emerald-600 text-white px-5 py-4 rounded-2xl shadow-xl animate-bounce-subtle">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="text-sm font-medium">{{ session('task-success') }}</span>
        </div>
    @endif

    <div class="flex flex-col gap-6 lg:flex-row items-start relative">
        
        {{-- ── Main Content Area (Tasks) ────────────────────────────────────── --}}
        <div class="flex-1 w-full order-2 lg:order-1 transition-all duration-300">
            
            {{-- Task Stats Row --}}
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                    <p class="text-[10px] uppercase font-bold tracking-widest text-slate-400 mb-1">Total</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $this->taskStats['total'] }}</p>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                    <p class="text-[10px] uppercase font-bold tracking-widest text-amber-500 mb-1">Open</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $this->taskStats['pending'] }}</p>
                </div>
                <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                    <p class="text-[10px] uppercase font-bold tracking-widest text-emerald-500 mb-1">Done</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $this->taskStats['completed'] }}</p>
                </div>
            </div>

            {{-- Task Table View --}}
            <div x-show="tab === 'table'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                    <div class="relative w-full max-w-xs">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input wire:model.live.debounce.300ms="taskSearch" type="text" placeholder="Search tasks..." class="w-full pl-9 pr-4 py-2 text-sm text-slate-700 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all">
                    </div>
                </div>
                <div class="overflow-x-auto text-sm">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead class="bg-slate-50/50 text-[10px] uppercase font-bold tracking-wider text-slate-400">
                            <tr>
                                <th class="px-6 py-4">Task</th>
                                <th class="px-6 py-4 text-center">Assignee</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-center">Due Date</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($this->tasks as $task)
                            <tr class="hover:bg-slate-50/50 group transition-colors">
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-slate-800">{{ $task->title }}</p>
                                    @if($task->description)
                                        <p class="text-[11px] text-slate-400 line-clamp-1 max-w-sm">{{ $task->description }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($task->assignee)
                                        <div class="flex flex-col items-center">
                                            <img class="w-6 h-6 rounded-full ring-2 ring-white" src="https://ui-avatars.com/api/?name={{ urlencode($task->assignee->name) }}&background=6366f1&color=fff" title="{{ $task->assignee->name }}">
                                            <span class="text-[10px] text-slate-500 mt-1">{{ explode(' ', $task->assignee->name)[0] }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-300 italic">Unassigned</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $tBadge = match($task->status) {
                                            'not started' => 'bg-slate-100 text-slate-600',
                                            'in progress' => 'bg-indigo-100 text-indigo-700',
                                            'on review'   => 'bg-amber-100 text-amber-700',
                                            'done'        => 'bg-emerald-100 text-emerald-700',
                                            default       => 'bg-slate-100 text-slate-600',
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $tBadge }}">
                                        {{ str_replace(' ', '', $task->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-xs text-slate-500">
                                    {{ $task->due_date ? $task->due_date->format('M d') : '—' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        @can('update', $task)
                                        <button wire:click="openTaskEdit({{ $task->id }})" class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit Task">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        @endcan
                                        @can('delete', $task)
                                        <button wire:click="confirmTaskDelete({{ $task->id }})" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Delete Task">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                    <p class="text-sm">No tasks found in this project.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($taskView === 'table' && $this->tasks->hasPages())
                    <div class="px-6 py-4 bg-slate-50/30 border-t border-slate-100">
                        {{ $this->tasks->links() }}
                    </div>
                @endif
            </div>

            {{-- Task Kanban View --}}
            <div x-show="tab === 'kanban'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-[0.98]" x-transition:enter-end="opacity-100 scale-100" class="w-full">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 items-start">
                    @foreach(['not started' => 'bg-slate-50 border-slate-200', 'in progress' => 'bg-indigo-50/50 border-indigo-100', 'on review' => 'bg-amber-50/50 border-amber-100', 'done' => 'bg-emerald-50/50 border-emerald-100'] as $status => $style)
                    <div class="flex flex-col h-full min-h-[400px] rounded-2xl border-2 {{ $style }} border-dashed p-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xs font-black uppercase tracking-widest text-slate-500">{{ $status }}</h3>
                            <span class="bg-white px-2 py-0.5 rounded-lg text-[10px] font-bold shadow-sm border border-slate-100">{{ $this->kanbanTasks[$status]->count() }}</span>
                        </div>
                        
                        <div class="space-y-3 flex-1 overflow-y-auto max-h-[60vh] scrollbar-hide">
                            @foreach($this->kanbanTasks[$status] as $task)
                            <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all group relative cursor-pointer" wire:click="openTaskEdit({{ $task->id }})">
                                <p class="text-sm font-bold text-slate-700 leading-tight pr-5">{{ $task->title }}</p>
                                <p class="text-[11px] text-slate-400 mt-2 line-clamp-2 leading-relaxed">{{ $task->description ?: 'No description' }}</p>
                                
                                <div class="mt-4 pt-3 border-t border-slate-50 flex items-center justify-between">
                                    <div class="flex items-center gap-1.5">
                                        @if($task->assignee)
                                            <img class="w-5 h-5 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($task->assignee->name) }}&background=6366f1&color=fff" title="{{ $task->assignee->name }}">
                                        @else
                                            <div class="w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center text-[8px] text-slate-400 border border-dashed border-slate-300">?</div>
                                        @endif
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">{{ $task->due_date ? $task->due_date->format('M d') : 'OPEN' }}</span>
                                    </div>
                                    
                                    {{-- Quick status move (Mobile/Basic) --}}
                                    <div class="flex gap-1">
                                        @if($status !== 'not started')
                                            <button @click.stop wire:click="updateTaskStatus({{ $task->id }}, '{{ array_keys($this->kanbanTasks)[array_search($status, array_keys($this->kanbanTasks)) - 1] }}')" class="p-1 hover:bg-slate-100 rounded text-slate-300 hover:text-indigo-600 transition-colors">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            </button>
                                        @endif
                                        @if($status !== 'done')
                                            <button @click.stop wire:click="updateTaskStatus({{ $task->id }}, '{{ array_keys($this->kanbanTasks)[array_search($status, array_keys($this->kanbanTasks)) + 1] }}')" class="p-1 hover:bg-slate-100 rounded text-slate-300 hover:text-indigo-600 transition-colors">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach

                            @can('create', [App\Models\Task::class, $project])
                            <button wire:click="openTaskCreate" class="w-full py-2 border-2 border-dashed border-slate-200 rounded-xl text-slate-300 hover:border-indigo-300 hover:text-indigo-400 hover:bg-white text-[10px] font-black uppercase tracking-widest transition-all">
                                + Add Task
                            </button>
                            @endcan
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── Member Management Side Panel ─────────────────────────────────── --}}
        <div 
            x-show="showMembers" 
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-x-full opacity-0"
            x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="translate-x-full opacity-0"
            class="w-full lg:w-80 p-6 bg-white rounded-2xl border border-slate-100 shadow-xl lg:sticky lg:top-24 order-1 lg:order-2"
        >
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-sm font-black uppercase tracking-[0.2em] text-slate-400">Project Members</h3>
                <button @click="showMembers = false" class="lg:hidden text-slate-400 p-1 hover:bg-slate-50 rounded">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            @if (session()->has('member-success'))
                <div class="mb-4 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-3 py-2 rounded-lg text-center">
                    {{ session('member-success') }}
                </div>
            @endif

            {{-- Members List --}}
            <div class="space-y-4 mb-8">
                {{-- Owner --}}
                <div class="flex items-center gap-3">
                    <img class="w-8 h-8 rounded-full border-2 border-indigo-100" src="https://ui-avatars.com/api/?name={{ urlencode($project->owner->name) }}&background=6366f1&color=fff">
                    <div class="flex-1 overflow-hidden">
                        <p class="text-xs font-bold text-slate-700 truncate">{{ $project->owner->name }}</p>
                        <span class="text-[9px] font-black uppercase text-indigo-500 tracking-tighter">Owner</span>
                    </div>
                </div>

                {{-- Group Members --}}
                @foreach($this->members as $member)
                <div class="flex items-center gap-3 group">
                    <img class="w-8 h-8 rounded-full border border-slate-100" src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&background=f1f5f9&color=64748b">
                    <div class="flex-1 overflow-hidden">
                        <p class="text-xs font-medium text-slate-600 truncate">{{ $member->name }}</p>
                        <span class="text-[9px] font-bold text-slate-300 uppercase">Member</span>
                    </div>
                    @can('update', $project)
                    <button wire:click="removeMember({{ $member->id }})" class="opacity-0 group-hover:opacity-100 p-1 text-slate-300 hover:text-rose-500 transition-all" title="Remove Member">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                    @endcan
                </div>
                @endforeach
            </div>

            {{-- Add Member Action --}}
            @can('update', $project)
            <div class="pt-6 border-t border-slate-50">
                <p class="text-[9px] font-black uppercase text-slate-400 mb-3 tracking-widest">Assign New Member</p>
                <div class="space-y-3">
                    <select wire:model="selectedUserId" class="w-full text-xs bg-slate-50 border-slate-100 rounded-xl focus:ring-indigo-500 py-2">
                        <option value="">Select a user...</option>
                        @foreach($this->availableUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    <button wire:click="addMember" class="w-full py-2 bg-slate-900 text-white rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-slate-800 transition-all active:scale-95 disabled:opacity-50" @if(!$selectedUserId) disabled @endif>
                        Add to Project
                    </button>
                </div>
                @error('selectedUserId') <p class="mt-2 text-[10px] text-rose-500 font-medium">{{ $message }}</p> @enderror
            </div>
            @endcan
        </div>
    </div>

    {{-- ── Task Upsert Slide-over ────────────────────────────────────────── --}}
    <div x-show="taskModal" class="fixed inset-0 z-[60] overflow-hidden" style="display: none;">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" @click="taskModal = false"></div>
        <div class="fixed inset-y-0 right-0 flex max-w-full pl-10" x-show="taskModal" x-transition:enter="transition ease-out duration-300 sm:duration-500" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-300 sm:duration-500" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
            <div class="w-screen max-w-md bg-white shadow-2xl flex flex-col">
                <div class="px-6 py-6 bg-slate-50 border-b border-slate-100">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-800">{{ $editingTaskId ? 'Edit Task' : 'New Task' }}</h2>
                        <button @click="taskModal = false" class="text-slate-400 hover:text-slate-600"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">{{ $editingTaskId ? 'Update task requirements and assignment.' : 'Fill in the details for the new task.' }}</p>
                </div>

                <form wire:submit="saveTask" class="flex-1 overflow-y-auto px-6 py-8 space-y-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">Task Title</label>
                        <input wire:model="taskTitle" type="text" placeholder="e.g. Design Login Page" class="w-full px-4 py-3 text-sm bg-slate-50 border-slate-100 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all">
                        @error('taskTitle') <p class="mt-1 text-[10px] text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">Description</label>
                        <textarea wire:model="taskDescription" rows="4" placeholder="Briefly describe what needs to be done..." class="w-full px-4 py-3 text-sm bg-slate-50 border-slate-100 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all resize-none"></textarea>
                        @error('taskDescription') <p class="mt-1 text-[10px] text-rose-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">Assignee</label>
                            <select wire:model="taskAssignedTo" class="w-full text-xs bg-slate-50 border-slate-100 rounded-xl focus:ring-indigo-500 py-3">
                                <option value="">Select user...</option>
                                <option value="{{ $project->owner_id }}">{{ $project->owner->name }} (Owner)</option>
                                @foreach($this->members as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">Due Date</label>
                            <input wire:model="taskDueDate" type="date" class="w-full text-xs bg-slate-50 border-slate-100 rounded-xl focus:ring-indigo-500 py-2.5">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-400 mb-2 tracking-widest">Status</label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach(['not started', 'in progress', 'on review', 'done'] as $status)
                            <label class="relative flex items-center gap-2 p-3 rounded-xl border-2 transition-all cursor-pointer {{ $taskStatus === $status ? 'bg-indigo-50 border-indigo-500' : 'bg-white border-slate-100 hover:border-slate-200' }}">
                                <input type="radio" wire:model.live="taskStatus" value="{{ $status }}" class="sr-only">
                                <span class="w-2.5 h-2.5 rounded-full {{ match($status){'not started'=>'bg-slate-300','in progress'=>'bg-indigo-400','on review'=>'bg-amber-400','done'=>'bg-emerald-400'} }}"></span>
                                <span class="text-[10px] font-bold text-slate-700 capitalize break-all">{{ $status }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100 flex gap-3">
                        <button type="button" @click="taskModal = false" class="flex-1 py-3 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-bold uppercase transition-all hover:bg-slate-50 active:scale-95">Cancel</button>
                        <button type="submit" class="flex-1 py-3 bg-indigo-600 text-white rounded-xl text-xs font-bold uppercase shadow-lg shadow-indigo-600/30 transition-all hover:bg-indigo-700 active:scale-95">
                            <span wire:loading.remove wire:target="saveTask">{{ $editingTaskId ? 'Save Changes' : 'Create Task' }}</span>
                            <span wire:loading wire:target="saveTask">Processing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Task Delete Modal ────────────────────────────────────────────── --}}
    <div x-show="deleteTaskModal" class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display: none;">
        <div class="bg-white rounded-3xl p-8 max-w-sm w-full text-center shadow-2xl animate-in zoom-in duration-300">
            <div class="w-16 h-16 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-rose-100">
                <svg class="w-8 h-8 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">Delete Task?</h3>
            <p class="text-xs text-slate-500 mb-8 leading-relaxed">Are you certain you want to remove this task? This action is permanent and cannot be reversed.</p>
            <div class="flex gap-3">
                <button @click="deleteTaskModal = false" class="flex-1 py-3 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-bold uppercase transition-all hover:bg-slate-50">Dismiss</button>
                <button wire:click="deleteTask" class="flex-1 py-3 bg-rose-600 text-white rounded-xl text-xs font-bold uppercase shadow-lg shadow-rose-600/30 transition-all hover:bg-rose-700 active:scale-95">Confirm</button>
            </div>
        </div>
    </div>

</div>
