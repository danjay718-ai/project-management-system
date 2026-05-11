<div x-data="{
    tab: $wire.entangle('taskView'),
    showMembers: $wire.entangle('showMembers'),
    taskModal: $wire.entangle('showTaskModal'),
    deleteTaskModal: $wire.entangle('showDeleteTaskModal'),
    dragTaskId: null,
    dragOverStatus: null
}" class="min-h-full">

    {{-- ── Flash Message ─────────────────────────────────────────────────── --}}
    @if (session()->has('task-success'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 4000)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed bottom-6 right-6 z-50 flex items-center gap-3 bg-emerald-600 text-white px-5 py-4 rounded-2xl shadow-xl shadow-emerald-900/20 max-w-sm"
        >
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-sm font-medium">{{ session('task-success') }}</span>
        </div>
    @endif

    @if (session()->has('member-success'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 4000)"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed bottom-6 right-6 z-50 flex items-center gap-3 bg-emerald-600 text-white px-5 py-4 rounded-2xl shadow-xl shadow-emerald-900/20 max-w-sm"
        >
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-sm font-medium">{{ session('member-success') }}</span>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- ── Page Header ───────────────────────────────────────────────────── --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div class="mb-8">
        {{-- Breadcrumb --}}
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1.5 text-sm">
                <li>
                    <a href="{{ route('project.list') }}" wire:navigate class="text-slate-400 hover:text-indigo-600 transition-colors font-medium">Projects</a>
                </li>
                <li class="flex items-center text-slate-300">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                    <span class="ml-1 text-slate-700 font-semibold">{{ $project->name }}</span>
                </li>
            </ol>
        </nav>

        {{-- Title Row --}}
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-500/25">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
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
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $pBadge }} capitalize">
                            {{ $project->status }}
                        </span>
                        <div class="flex items-center gap-1.5 text-xs text-slate-500">
                            <img class="w-5 h-5 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($project->owner->name) }}&background=6366f1&color=fff&size=40" alt="{{ $project->owner->name }}">
                            <span>{{ $project->owner->name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2">
                {{-- View Toggle --}}
                <div class="flex bg-slate-100 p-1 rounded-xl">
                    <button @click="tab = 'table'; $wire.setTaskView('table')"
                            class="px-3.5 py-2 rounded-lg text-xs font-semibold transition-all"
                            :class="tab === 'table' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'">
                        <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        List
                    </button>
                    <button @click="tab = 'kanban'; $wire.setTaskView('kanban')"
                            class="px-3.5 py-2 rounded-lg text-xs font-semibold transition-all"
                            :class="tab === 'kanban' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'">
                        <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                        Board
                    </button>
                </div>

                {{-- Members Button --}}
                <button wire:click="toggleMembers"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:border-slate-300 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Members
                </button>

                {{-- Add Task Button --}}
                @can('create', [App\Models\Task::class, $project])
                <button wire:click="openTaskCreate"
                        id="btn-create-task"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-600/30 hover:bg-indigo-700 active:scale-95 transition-all">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    New Task
                </button>
                @endcan
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- ── Stat Cards ────────────────────────────────────────────────────── --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-3 gap-4 mb-8">
        @foreach ([
            ['label' => 'Total Tasks', 'value' => $this->taskStats['total'],     'color' => 'indigo',  'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['label' => 'In Progress', 'value' => $this->taskStats['pending'],   'color' => 'amber',   'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Completed',   'value' => $this->taskStats['completed'], 'color' => 'emerald', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z']
        ] as $stat)
        <div class="group p-5 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-{{ $stat['color'] }}-100 text-{{ $stat['color'] }}-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-{{ $stat['color'] }}-500 mb-0.5">{{ $stat['label'] }}</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $stat['value'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- ── Content Area ──────────────────────────────────────────────────── --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}

    {{-- ── List View ── Table ────────────────────────────────────────────── --}}
    <div x-show="tab === 'table'" x-transition class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

        {{-- Toolbar --}}
        <div class="flex flex-col gap-3 px-6 py-4 border-b border-slate-100 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-800">Task List</h2>
                <p class="text-xs text-slate-500 mt-0.5">All tasks for this project</p>
            </div>
            <div class="flex items-center gap-3">
                {{-- Search --}}
                <div class="relative w-full sm:max-w-xs">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none">
                            <path d="M21 21L15 15M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <input wire:model.live.debounce.300ms="taskSearch" type="text" placeholder="Search tasks…"
                           id="search-tasks"
                           class="w-full pl-9 pr-4 py-2 text-sm text-slate-700 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition-all">
                </div>

                {{-- Status Filter (Dynamic from DB) --}}
                <select wire:model.live="taskStatusFilter"
                        id="filter-task-status"
                        class="text-sm text-slate-700 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition-all">
                    <option value="">All Statuses</option>
                    @foreach($this->statuses as $statusOption)
                        <option value="{{ $statusOption->id }}">{{ $statusOption->label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead class="bg-slate-50/70 border-b border-slate-100 text-xs uppercase tracking-wider text-slate-500 font-semibold text-left">
                    <tr>
                        <th class="px-6 py-4">Task</th>
                        <th class="px-6 py-4">Assignee</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Due Date</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($this->tasks as $task)
                    <tr class="hover:bg-slate-50/60 transition-colors group">
                        <td class="px-6 py-4">
                            <div>
                                <span class="font-semibold text-slate-800 group-hover:text-indigo-600 transition-colors">{{ $task->title }}</span>
                                @if($task->description)
                                    <p class="text-xs text-slate-400 mt-0.5 max-w-xs truncate">{{ $task->description }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($task->assignee)
                                <div class="flex items-center gap-2">
                                    <img class="w-7 h-7 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($task->assignee->name) }}&background=6366f1&color=fff&size=64" alt="{{ $task->assignee->name }}">
                                    <span class="text-slate-700">{{ $task->assignee->name }}</span>
                                </div>
                            @else
                                <span class="text-xs text-slate-400 italic">Unassigned</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            {{-- Dynamic status badge using color from the lookup table --}}
                            @if($task->taskStatus)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-{{ $task->taskStatus->color }}-100 text-{{ $task->taskStatus->color }}-700 capitalize">
                                    {{ $task->taskStatus->label }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                    Unknown
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-400 text-xs">
                            {{ $task->due_date ? $task->due_date->format('M d, Y') : '—' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                @can('update', $task)
                                <button wire:click="openTaskEdit({{ $task->id }})"
                                        id="btn-edit-task-{{ $task->id }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </button>
                                @endcan
                                @can('delete', $task)
                                <button wire:click="confirmTaskDelete({{ $task->id }})"
                                        id="btn-delete-task-{{ $task->id }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Delete
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3 text-slate-400">
                                <svg class="w-12 h-12 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-sm font-medium">No tasks found</p>
                                <p class="text-xs">Create your first task to get started.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($taskView === 'table' && $this->tasks->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $this->tasks->links() }}
            </div>
        @endif
    </div>

    {{-- ── Board View ── Kanban with Drag & Drop (Dynamic Columns) ───────── --}}
    <div x-show="tab === 'kanban'" x-transition class="w-full">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-{{ min(count($this->statuses), 4) }} gap-5 items-start">
            @foreach($this->statuses as $status)
            <div class="flex flex-col bg-slate-50/80 rounded-2xl border border-slate-200/60 min-h-[420px] transition-all"
                 x-on:dragover.prevent="dragOverStatus = {{ $status->id }}"
                 x-on:dragleave="dragOverStatus = null"
                 x-on:drop.prevent="
                     if (dragTaskId) {
                         $wire.updateTaskStatus(dragTaskId, {{ $status->id }});
                         dragTaskId = null;
                         dragOverStatus = null;
                     }
                 "
                 :class="dragOverStatus === {{ $status->id }} ? 'drag-over' : ''">

                {{-- Column Header --}}
                <div class="flex items-center justify-between px-4 py-3.5 border-b border-slate-200/60">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-{{ $status->color }}-500"></span>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">{{ $status->label }}</h3>
                    </div>
                    <span class="flex items-center justify-center min-w-[1.5rem] h-6 px-1.5 bg-white rounded-md text-xs font-bold text-slate-500 border border-slate-200 shadow-sm">
                        {{ $this->kanbanTasks[$status->id]->count() }}
                    </span>
                </div>

                {{-- Cards Container --}}
                <div class="flex-1 overflow-y-auto p-3 space-y-3 scrollbar-hide">
                    @foreach($this->kanbanTasks[$status->id] as $task)
                    <div class="kanban-card bg-white p-4 rounded-xl border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all group/card"
                         draggable="true"
                         x-on:dragstart="dragTaskId = {{ $task->id }}; $event.target.classList.add('dragging')"
                         x-on:dragend="$event.target.classList.remove('dragging'); dragOverStatus = null"
                         wire:key="kanban-task-{{ $task->id }}">

                        {{-- Card Header --}}
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <h4 class="text-sm font-semibold text-slate-800 leading-snug group-hover/card:text-indigo-600 transition-colors cursor-pointer"
                                wire:click="openTaskEdit({{ $task->id }})">
                                {{ $task->title }}
                            </h4>
                            {{-- Quick move arrows --}}
                            <div class="flex gap-0.5 opacity-0 group-hover/card:opacity-100 transition-opacity shrink-0">
                                @php
                                    $statusList = $this->statuses;
                                    $currentIndex = $statusList->search(fn($s) => $s->id === $status->id);
                                @endphp
                                @if($currentIndex > 0)
                                    <button wire:click="updateTaskStatus({{ $task->id }}, {{ $statusList[$currentIndex - 1]->id }})"
                                            class="p-1 rounded-md text-slate-300 hover:text-indigo-600 hover:bg-indigo-50 transition-colors" title="Move left">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                                    </button>
                                @endif
                                @if($currentIndex < $statusList->count() - 1)
                                    <button wire:click="updateTaskStatus({{ $task->id }}, {{ $statusList[$currentIndex + 1]->id }})"
                                            class="p-1 rounded-md text-slate-300 hover:text-indigo-600 hover:bg-indigo-50 transition-colors" title="Move right">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{-- Description --}}
                        @if($task->description)
                            <p class="text-xs text-slate-400 line-clamp-2 mb-3 leading-relaxed">{{ $task->description }}</p>
                        @endif

                        {{-- Card Footer --}}
                        <div class="flex items-center justify-between pt-3 border-t border-slate-50">
                            <div class="flex items-center gap-2">
                                @if($task->assignee)
                                    <img class="w-6 h-6 rounded-full ring-2 ring-white" src="https://ui-avatars.com/api/?name={{ urlencode($task->assignee->name) }}&background=6366f1&color=fff&size=48" alt="{{ $task->assignee->name }}">
                                    <span class="text-[11px] text-slate-500 font-medium">{{ explode(' ', $task->assignee->name)[0] }}</span>
                                @else
                                    <span class="text-[11px] text-slate-400 italic">Unassigned</span>
                                @endif
                            </div>
                            @if($task->due_date)
                                <span class="text-[11px] text-slate-400 font-medium">{{ $task->due_date->format('M d') }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach

                    {{-- Add Task in Column --}}
                    @can('create', [App\Models\Task::class, $project])
                    <button wire:click="openTaskCreate"
                            class="w-full py-3.5 border-2 border-dashed border-slate-200 rounded-xl text-xs font-semibold text-slate-400 hover:text-indigo-600 hover:border-indigo-300 hover:bg-white transition-all">
                        + Add Task
                    </button>
                    @endcan
                </div>
            </div>
            @endforeach
        </div>
    </div>


    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- ── Shared Backdrop ───────────────────────────────────────────────── --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="showMembers || taskModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 bg-slate-900/50 backdrop-blur-sm"
         style="display: none;"
         @click="showMembers = false; taskModal = false; $wire.closeTaskModal()"></div>


    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- ── Members Slide-over ────────────────────────────────────────────── --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="showMembers"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 z-50 flex h-full w-full flex-col bg-white shadow-2xl sm:max-w-md"
         style="display: none;"
         @keydown.escape.window="showMembers = false">

        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5 bg-slate-50/60">
            <div>
                <h3 class="text-lg font-semibold text-slate-800">Project Members</h3>
                <p class="text-xs text-slate-500 mt-0.5">Manage team collaborators</p>
            </div>
            <button @click="showMembers = false" class="rounded-lg p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Members List --}}
        <div class="flex-1 overflow-y-auto px-6 py-6 space-y-6">

            {{-- Owner --}}
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-3">Project Owner</p>
                <div class="flex items-center gap-3 p-3 bg-indigo-50/50 rounded-xl border border-indigo-100">
                    <img class="w-10 h-10 rounded-full ring-2 ring-indigo-200" src="https://ui-avatars.com/api/?name={{ urlencode($project->owner->name) }}&background=6366f1&color=fff&size=64" alt="{{ $project->owner->name }}">
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-slate-800">{{ $project->owner->name }}</p>
                        <span class="text-xs text-indigo-600 font-medium">Owner</span>
                    </div>
                    <svg class="w-5 h-5 text-indigo-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.9L9.03 1.703a2.4 2.4 0 011.939 0L17.833 4.9A2.4 2.4 0 0119.2 7.047V14a2.4 2.4 0 01-1.367 2.148l-6.866 3.197a2.4 2.4 0 01-1.934 0l-6.866-3.197A2.4 2.4 0 011.8 14V7.048c0-.943.555-1.789 1.367-2.148z" clip-rule="evenodd"/></svg>
                </div>
            </div>

            {{-- Team Members --}}
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-3">Team Members</p>
                <div class="space-y-2">
                    @forelse($this->members as $member)
                    <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group">
                        <img class="w-10 h-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($member->name) }}&background=f1f5f9&color=94a3b8&size=64" alt="{{ $member->name }}">
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-slate-700">{{ $member->name }}</p>
                            <span class="text-xs text-slate-400">Member</span>
                        </div>
                        @can('update', $project)
                        <button wire:click="removeMember({{ $member->id }})"
                                class="opacity-0 group-hover:opacity-100 inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Remove
                        </button>
                        @endcan
                    </div>
                    @empty
                    <p class="text-sm text-slate-400 text-center py-6">No team members added yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- Add Member --}}
            @can('update', $project)
            <div class="pt-4 border-t border-slate-100">
                <p class="text-xs font-semibold uppercase tracking-widest text-slate-400 mb-3">Add Member</p>
                <div class="space-y-3">
                    <select wire:model="selectedUserId"
                            id="add-member-select"
                            class="w-full px-4 py-2.5 text-sm text-slate-800 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition-all">
                        <option value="">— Select a user —</option>
                        @foreach($this->availableUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>

                    <button wire:click="addMember"
                            id="btn-add-member"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-xl shadow-lg shadow-indigo-600/30 hover:bg-indigo-700 active:scale-95 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            @if(!$selectedUserId) disabled @endif>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Member
                    </button>

                    @error('selectedUserId')
                        <p class="text-xs text-rose-500 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
            @endcan
        </div>
    </div>


    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- ── Task Create / Edit Slide-over ─────────────────────────────────── --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="taskModal"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 z-50 flex h-full w-full flex-col bg-white shadow-2xl sm:max-w-lg"
         style="display: none;"
         @keydown.escape.window="taskModal = false; $wire.closeTaskModal()">

        {{-- Header --}}
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5 bg-slate-50/60">
            <div>
                <h3 class="text-lg font-semibold text-slate-800">
                    {{ $editingTaskId ? 'Edit Task' : 'New Task' }}
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    {{ $editingTaskId ? 'Update task details.' : 'Fill in the details to create a new task.' }}
                </p>
            </div>
            <button @click="taskModal = false; $wire.closeTaskModal()" class="rounded-lg p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Form Body --}}
        <form wire:submit="saveTask" class="flex flex-col flex-1 overflow-y-auto">
            <div class="flex-1 px-6 py-6 space-y-6">

                {{-- Task Title --}}
                <div>
                    <label for="task-title" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Task Title <span class="text-rose-500">*</span>
                    </label>
                    <input wire:model="taskTitle" id="task-title" type="text" placeholder="e.g. Design homepage"
                           class="w-full px-4 py-2.5 text-sm text-slate-800 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition-all @error('taskTitle') border-rose-400 ring-2 ring-rose-200 @enderror">
                    @error('taskTitle')
                        <p class="mt-1.5 text-xs text-rose-500 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="task-description" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Description
                    </label>
                    <textarea wire:model="taskDescription" id="task-description" rows="4" placeholder="Brief description of the task…"
                              class="w-full px-4 py-2.5 text-sm text-slate-800 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition-all resize-none @error('taskDescription') border-rose-400 ring-2 ring-rose-200 @enderror"></textarea>
                    @error('taskDescription')
                        <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Assignee & Due Date --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="task-assignee" class="block text-sm font-medium text-slate-700 mb-1.5">Assignee</label>
                        <select wire:model="taskAssignedTo" id="task-assignee"
                                class="w-full px-4 py-2.5 text-sm text-slate-800 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition-all">
                            <option value="">Unassigned</option>
                            @foreach($this->members as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                            <option value="{{ $project->owner_id }}">{{ $project->owner->name }} (Owner)</option>
                        </select>
                    </div>
                    <div>
                        <label for="task-due-date" class="block text-sm font-medium text-slate-700 mb-1.5">Due Date</label>
                        <input wire:model="taskDueDate" id="task-due-date" type="date"
                               class="w-full px-4 py-2.5 text-sm text-slate-800 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition-all">
                    </div>
                </div>

                {{-- Status (Dynamic from DB) --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Status <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($this->statuses as $statusOption)
                            <label for="status-{{ $statusOption->id }}"
                                   class="relative flex cursor-pointer items-center gap-2.5 rounded-xl border-2 p-3 transition-all
                                       {{ $taskStatusId == $statusOption->id ? 'border-indigo-500 bg-indigo-50' : 'border-slate-200 bg-white hover:border-slate-300' }}">
                                <input wire:model.live="taskStatusId" type="radio" id="status-{{ $statusOption->id }}" name="taskStatusId" value="{{ $statusOption->id }}" class="sr-only">
                                <span class="w-2.5 h-2.5 rounded-full bg-{{ $statusOption->color }}-400"></span>
                                <span class="text-xs font-semibold text-slate-700">{{ $statusOption->label }}</span>
                                @if($taskStatusId == $statusOption->id)
                                    <svg class="w-4 h-4 text-indigo-600 ml-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                @endif
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Form Footer --}}
            <div class="flex items-center justify-end gap-3 border-t border-slate-100 px-6 py-4 bg-slate-50/60 shrink-0">
                <button type="button" @click="taskModal = false; $wire.closeTaskModal()"
                        class="px-4 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 active:scale-95 transition-all">
                    Cancel
                </button>
                <button type="submit"
                        id="btn-save-task"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-xl shadow-lg shadow-indigo-600/30 hover:bg-indigo-700 active:scale-95 transition-all">
                    <svg wire:loading wire:target="saveTask" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    {{ $editingTaskId ? 'Save Changes' : 'Create Task' }}
                </button>
            </div>
        </form>
    </div>


    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- ── Delete Confirmation Modal ─────────────────────────────────────── --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="deleteTaskModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[60] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm px-4"
         style="display: none;"
         @keydown.escape.window="deleteTaskModal = false; $wire.set('showDeleteTaskModal', false)">

        <div x-show="deleteTaskModal"
             x-transition:enter="transition ease-out duration-200 transform"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">

            <div class="p-6">
                <div class="flex items-center justify-center w-14 h-14 mx-auto rounded-full bg-rose-100 mb-4">
                    <svg class="w-7 h-7 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 text-center mb-2">Delete Task</h3>
                <p class="text-sm text-slate-500 text-center">Are you sure you want to delete this task? This action <strong class="text-slate-700">cannot be undone</strong>.</p>
            </div>

            <div class="flex items-center gap-3 px-6 pb-6">
                <button @click="deleteTaskModal = false; $wire.set('showDeleteTaskModal', false)"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors">
                    Cancel
                </button>
                <button wire:click="deleteTask"
                        id="btn-confirm-delete-task"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-rose-600 rounded-xl hover:bg-rose-700 active:scale-95 transition-all">
                    <svg wire:loading wire:target="deleteTask" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    Yes, Delete
                </button>
            </div>
        </div>
    </div>

</div>
