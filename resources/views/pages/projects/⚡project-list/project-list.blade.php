<div x-data="{ modalOpen: $wire.entangle('showModal'), deleteOpen: $wire.entangle('showDeleteModal') }">

    {{-- ── Flash Message ─────────────────────────────────────────────────── --}}
    @if (session()->has('success'))
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
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- ── Page Header ──────────────────────────────────────────────────────── --}}
    <div class="mb-8 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Projects</h2>
            <p class="mt-1 text-sm text-slate-500">Manage all your team projects in one place.</p>
        </div>
        <button
            wire:click="openCreate"
            id="btn-create-project"
            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-600/30 hover:bg-indigo-700 active:scale-95 transition-all"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            New Project
        </button>
    </div>

    {{-- ── Stat Cards ───────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 gap-4 mb-8 lg:grid-cols-4">
        @php
            $cards = [
                ['label' => 'Total',    'count' => $this->statusCounts['total'],    'color' => 'indigo',   'filter' => ''],
                ['label' => 'Active',   'count' => $this->statusCounts['active'],   'color' => 'emerald',  'filter' => 'active'],
                ['label' => 'Inactive', 'count' => $this->statusCounts['inactive'], 'color' => 'amber',    'filter' => 'inactive'],
                ['label' => 'Archived', 'count' => $this->statusCounts['archived'], 'color' => 'slate',    'filter' => 'archived'],
            ];
        @endphp
        @foreach($cards as $card)
        <button
            wire:click="$set('statusFilter', '{{ $card['filter'] }}')"
            class="group text-left p-5 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all {{ $statusFilter === $card['filter'] ? 'ring-2 ring-indigo-500' : '' }}"
        >
            <p class="text-xs font-semibold uppercase tracking-widest text-{{ $card['color'] }}-500 mb-1">{{ $card['label'] }}</p>
            <p class="text-3xl font-bold text-slate-800">{{ $card['count'] }}</p>
        </button>
        @endforeach
    </div>

    {{-- ── Table Card ───────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

        {{-- Table Toolbar --}}
        <div class="flex flex-col gap-3 px-6 py-4 border-b border-slate-100 sm:flex-row sm:items-center sm:justify-between">
            {{-- Search --}}
            <div class="relative w-full sm:max-w-xs">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none">
                        <path d="M21 21L15 15M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <input
                    wire:model.live.debounce.300ms="search"
                    id="search-projects"
                    type="text"
                    placeholder="Search projects…"
                    class="w-full pl-9 pr-4 py-2 text-sm text-slate-700 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition-all"
                >
            </div>

            {{-- Status Filter --}}
            <div class="flex items-center gap-2">
                <select
                    wire:model.live="statusFilter"
                    id="filter-status"
                    class="text-sm text-slate-700 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition-all"
                >
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="archived">Archived</option>
                </select>
                @if($search || $statusFilter)
                    <button wire:click="$set('search', ''); $set('statusFilter', '')" class="text-xs text-slate-500 hover:text-slate-800 underline transition-colors">
                        Clear
                    </button>
                @endif
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead class="bg-slate-50/70 border-b border-slate-100 text-xs uppercase tracking-wider text-slate-500 font-semibold text-left">
                    <tr>
                        <th class="px-6 py-4">Project</th>
                        <th class="px-6 py-4">Owner</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Created</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($this->projects as $project)
                    <tr class="hover:bg-slate-50/60 transition-colors group">
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-semibold text-slate-800">{{ $project->name }}</p>
                                @if($project->description)
                                    <p class="text-xs text-slate-400 mt-0.5 max-w-xs truncate">{{ $project->description }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <img
                                    class="w-7 h-7 rounded-full"
                                    src="https://ui-avatars.com/api/?name={{ urlencode($project->owner->name ?? 'U') }}&background=6366f1&color=fff&size=64"
                                    alt="{{ $project->owner->name ?? 'Unknown' }}"
                                >
                                <span class="text-slate-700">{{ $project->owner->name ?? '—' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $badge = match($project->status) {
                                    'active'   => 'bg-emerald-100 text-emerald-700',
                                    'inactive' => 'bg-amber-100 text-amber-700',
                                    'archived' => 'bg-slate-100 text-slate-500',
                                    default    => 'bg-slate-100 text-slate-500',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }} capitalize">
                                {{ $project->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-400 text-xs">
                            {{ $project->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button
                                    wire:click="openEdit({{ $project->id }})"
                                    id="btn-edit-project-{{ $project->id }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition-colors"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </button>
                                <button
                                    wire:click="confirmDelete({{ $project->id }})"
                                    id="btn-delete-project-{{ $project->id }}"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-rose-600 bg-rose-50 hover:bg-rose-100 transition-colors"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3 text-slate-400">
                                <svg class="w-12 h-12 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-sm font-medium">No projects found</p>
                                <p class="text-xs">Try adjusting your search or filters, or create a new project.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($this->projects->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $this->projects->links() }}
        </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- ── Create / Edit Modal (Slide-over) ───────────────────────────────── --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div
        x-show="modalOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-40 flex items-start justify-end bg-slate-900/50 backdrop-blur-sm"
        style="display: none;"
        @keydown.escape.window="modalOpen = false; $wire.closeModal()"
    >
        {{-- Backdrop click --}}
        <div class="absolute inset-0" @click="modalOpen = false; $wire.closeModal()"></div>

        {{-- Slide-over Panel --}}
        <div
            x-show="modalOpen"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="relative z-50 flex h-full w-full flex-col bg-white shadow-2xl sm:max-w-lg"
        >
            {{-- Slide-over Header --}}
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5 bg-slate-50/60">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">
                        {{ $editingId ? 'Edit Project' : 'New Project' }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $editingId ? 'Update the details of your project.' : 'Fill in the details to create a new project.' }}
                    </p>
                </div>
                <button
                    @click="modalOpen = false; $wire.closeModal()"
                    class="rounded-lg p-1.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Form Body --}}
            <form wire:submit="save" class="flex flex-col flex-1 overflow-y-auto">
                <div class="flex-1 px-6 py-6 space-y-6">

                    {{-- Project Name --}}
                    <div>
                        <label for="project-name" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Project Name <span class="text-rose-500">*</span>
                        </label>
                        <input
                            wire:model="name"
                            id="project-name"
                            type="text"
                            placeholder="e.g. Website Redesign"
                            class="w-full px-4 py-2.5 text-sm text-slate-800 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition-all @error('name') border-rose-400 ring-2 ring-rose-200 @enderror"
                        >
                        @error('name')
                            <p class="mt-1.5 text-xs text-rose-500 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="project-description" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Description
                        </label>
                        <textarea
                            wire:model="description"
                            id="project-description"
                            rows="4"
                            placeholder="Brief description of the project…"
                            class="w-full px-4 py-2.5 text-sm text-slate-800 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition-all resize-none @error('description') border-rose-400 ring-2 ring-rose-200 @enderror"
                        ></textarea>
                        @error('description')
                            <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Owner --}}
                    <div>
                        <label for="project-owner" class="block text-sm font-medium text-slate-700 mb-1.5">
                            Owner <span class="text-rose-500">*</span>
                        </label>
                        <select
                            wire:model="owner_id"
                            id="project-owner"
                            class="w-full px-4 py-2.5 text-sm text-slate-800 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent focus:outline-none transition-all @error('owner_id') border-rose-400 ring-2 ring-rose-200 @enderror"
                        >
                            <option value="">— Select an owner —</option>
                            @foreach($this->users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('owner_id')
                            <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Status <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-3">
                            @foreach(['active' => ['color' => 'emerald', 'label' => 'Active'], 'inactive' => ['color' => 'amber', 'label' => 'Inactive'], 'archived' => ['color' => 'slate', 'label' => 'Archived']] as $value => $cfg)
                            <label
                                for="status-{{ $value }}"
                                class="relative flex cursor-pointer flex-col items-center gap-1 rounded-xl border-2 p-3 text-center transition-all
                                    {{ $status === $value ? 'border-indigo-500 bg-indigo-50' : 'border-slate-200 bg-white hover:border-slate-300' }}"
                            >
                                <input
                                    wire:model.live="status"
                                    type="radio"
                                    id="status-{{ $value }}"
                                    name="status"
                                    value="{{ $value }}"
                                    class="sr-only"
                                >
                                <span class="w-2.5 h-2.5 rounded-full bg-{{ $cfg['color'] }}-400"></span>
                                <span class="text-xs font-semibold text-slate-700">{{ $cfg['label'] }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('status')
                            <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Form Footer --}}
                <div class="flex items-center justify-end gap-3 border-t border-slate-100 px-6 py-4 bg-slate-50/60 shrink-0">
                    <button
                        type="button"
                        @click="modalOpen = false; $wire.closeModal()"
                        class="px-4 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 active:scale-95 transition-all"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        id="btn-save-project"
                        class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-xl shadow-lg shadow-indigo-600/30 hover:bg-indigo-700 active:scale-95 transition-all disabled:opacity-70"
                    >
                        <svg wire:loading wire:target="save" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                        {{ $editingId ? 'Save Changes' : 'Create Project' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- ── Delete Confirmation Modal ───────────────────────────────────────── --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div
        x-show="deleteOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm px-4"
        style="display: none;"
        @keydown.escape.window="deleteOpen = false; $wire.set('showDeleteModal', false)"
    >
        <div
            x-show="deleteOpen"
            x-transition:enter="transition ease-out duration-200 transform"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150 transform"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden"
        >
            <div class="p-6">
                <div class="flex items-center justify-center w-14 h-14 mx-auto rounded-full bg-rose-100 mb-4">
                    <svg class="w-7 h-7 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 text-center mb-2">Delete Project</h3>
                <p class="text-sm text-slate-500 text-center">Are you sure you want to delete this project? This action <strong class="text-slate-700">cannot be undone</strong> and all associated data will be permanently removed.</p>
            </div>
            <div class="flex items-center gap-3 px-6 pb-6">
                <button
                    @click="deleteOpen = false; $wire.set('showDeleteModal', false)"
                    class="flex-1 px-4 py-2.5 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-colors"
                >
                    Cancel
                </button>
                <button
                    wire:click="delete"
                    id="btn-confirm-delete"
                    class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-rose-600 rounded-xl hover:bg-rose-700 active:scale-95 transition-all"
                >
                    <svg wire:loading wire:target="delete" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    Yes, Delete
                </button>
            </div>
        </div>
    </div>

</div>