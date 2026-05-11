<div x-data="{ modalOpen: $wire.entangle('showModal'), deleteOpen: $wire.entangle('showDeleteModal') }">

    @if (session()->has('success'))
        <div
            x-data="{ show: true }"
            x-show="show"
            x-init="setTimeout(() => show = false, 4000)"
            x-transition
            class="fixed bottom-6 right-6 z-50 flex max-w-sm items-center gap-3 rounded-2xl bg-emerald-600 px-5 py-4 text-white shadow-xl shadow-emerald-900/20"
        >
            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="mb-8 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Users</h2>
            <p class="mt-1 text-sm text-slate-500">Manage accounts, roles, and role permissions.</p>
        </div>

        @if($this->canCreate)
            <button
                wire:click="openCreate"
                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-600/30 transition-all hover:bg-indigo-700 active:scale-95"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                New User
            </button>
        @endif
    </div>

    <div class="mb-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <button wire:click="$set('roleFilter', '')" class="group rounded-2xl border border-slate-100 bg-white p-5 text-left shadow-sm transition-all hover:-translate-y-0.5 hover:shadow-md {{ $roleFilter === '' ? 'ring-2 ring-indigo-500' : '' }}">
            <p class="mb-1 text-xs font-semibold uppercase tracking-widest text-indigo-500">Total Users</p>
            <p class="text-3xl font-bold text-slate-800">{{ $this->userCounts['total'] }}</p>
        </button>
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
            <p class="mb-1 text-xs font-semibold uppercase tracking-widest text-emerald-500">With Roles</p>
            <p class="text-3xl font-bold text-slate-800">{{ $this->userCounts['assigned'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
            <p class="mb-1 text-xs font-semibold uppercase tracking-widest text-amber-500">Roles</p>
            <p class="text-3xl font-bold text-slate-800">{{ $this->userCounts['roles'] }}</p>
        </div>
        <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
            <p class="mb-1 text-xs font-semibold uppercase tracking-widest text-rose-500">Permissions</p>
            <p class="text-3xl font-bold text-slate-800">{{ $this->userCounts['permissions'] }}</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-slate-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="relative w-full sm:max-w-xs">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none">
                        <path d="M21 21L15 15M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search users..."
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2 pl-9 pr-4 text-sm text-slate-700 transition-all focus:border-transparent focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
            </div>

            <div class="flex items-center gap-2">
                <select
                    wire:model.live="roleFilter"
                    class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700 transition-all focus:border-transparent focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                    <option value="">All Roles</option>
                    @foreach($this->roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
                @if($search || $roleFilter)
                    <button wire:click="$set('search', ''); $set('roleFilter', '')" class="text-xs text-slate-500 underline transition-colors hover:text-slate-800">
                        Clear
                    </button>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead class="border-b border-slate-100 bg-slate-50/70 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Roles</th>
                        <th class="px-6 py-4">Permissions</th>
                        <th class="px-6 py-4">Created</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($this->users as $user)
                        <tr class="group transition-colors hover:bg-slate-50/60">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img class="h-9 w-9 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=6366f1&color=fff&size=64" alt="{{ $user->name }}">
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex max-w-sm flex-wrap gap-1.5">
                                    @forelse($user->roles as $role)
                                        <span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">{{ $role->name }}</span>
                                    @empty
                                        <span class="text-xs text-slate-400">No roles</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                    {{ $user->roles->flatMap->permissions->unique('id')->count() }} permissions
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-400">{{ $user->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 transition-opacity group-hover:opacity-100">
                                    @can('update', $user)
                                        <button wire:click="openEdit({{ $user->id }})" class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-medium text-indigo-600 transition-colors hover:bg-indigo-100">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </button>
                                    @endcan

                                    @can('delete', $user)
                                        <button wire:click="confirmDelete({{ $user->id }})" class="inline-flex items-center gap-1.5 rounded-lg bg-rose-50 px-3 py-1.5 text-xs font-medium text-rose-600 transition-colors hover:bg-rose-100">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
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
                                    <svg class="h-12 w-12 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/>
                                    </svg>
                                    <p class="text-sm font-medium">No users found</p>
                                    <p class="text-xs">Try adjusting your search or filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($this->users->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $this->users->links() }}
            </div>
        @endif
    </div>

    <div
        x-show="modalOpen"
        x-transition.opacity
        class="fixed inset-0 z-40 flex items-start justify-end bg-slate-900/50 backdrop-blur-sm"
        style="display: none;"
        @keydown.escape.window="modalOpen = false; $wire.closeModal()"
    >
        <div class="absolute inset-0" @click="modalOpen = false; $wire.closeModal()"></div>

        <div
            x-show="modalOpen"
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="relative z-50 flex h-full w-full flex-col bg-white shadow-2xl sm:max-w-2xl"
        >
            <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/60 px-6 py-5">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800">{{ $editingId ? 'Edit User' : 'New User' }}</h3>
                    <p class="mt-0.5 text-xs text-slate-500">Set account details, roles, and role permissions.</p>
                </div>
                <button @click="modalOpen = false; $wire.closeModal()" class="rounded-lg p-1.5 text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto">
                <form wire:submit="save" class="border-b border-slate-100 px-6 py-6">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="user-name" class="mb-1.5 block text-sm font-medium text-slate-700">Name <span class="text-rose-500">*</span></label>
                            <input wire:model="name" id="user-name" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 transition-all focus:border-transparent focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('name') border-rose-400 ring-2 ring-rose-200 @enderror">
                            @error('name') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="user-email" class="mb-1.5 block text-sm font-medium text-slate-700">Email <span class="text-rose-500">*</span></label>
                            <input wire:model="email" id="user-email" type="email" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 transition-all focus:border-transparent focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('email') border-rose-400 ring-2 ring-rose-200 @enderror">
                            @error('email') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="user-password" class="mb-1.5 block text-sm font-medium text-slate-700">{{ $editingId ? 'New Password' : 'Password' }} @unless($editingId)<span class="text-rose-500">*</span>@endunless</label>
                            <input wire:model="password" id="user-password" type="password" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 transition-all focus:border-transparent focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('password') border-rose-400 ring-2 ring-rose-200 @enderror">
                            @error('password') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="user-password-confirmation" class="mb-1.5 block text-sm font-medium text-slate-700">Confirm Password</label>
                            <input wire:model="password_confirmation" id="user-password-confirmation" type="password" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 transition-all focus:border-transparent focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    @if($this->canAssignRole)
                        <div class="mt-6">
                            <label class="mb-2 block text-sm font-medium text-slate-700">Roles</label>
                            <div class="grid gap-2 sm:grid-cols-3">
                                @foreach($this->roles as $role)
                                    <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 transition-colors hover:bg-slate-50">
                                        <input wire:model="selectedRoles" type="checkbox" value="{{ $role->id }}" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="font-medium">{{ $role->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('selectedRoles') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button type="button" @click="modalOpen = false; $wire.closeModal()" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition-all hover:bg-slate-50 active:scale-95">Cancel</button>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-600/30 transition-all hover:bg-indigo-700 active:scale-95 disabled:opacity-70">
                            <svg wire:loading wire:target="save" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                            </svg>
                            {{ $editingId ? 'Save Changes' : 'Create User' }}
                        </button>
                    </div>
                </form>

                @if($this->canAssignRole)
                    <div class="space-y-6 px-6 py-6">
                        <div>
                            <h4 class="text-sm font-semibold text-slate-800">Add Roles And Permissions</h4>
                            <p class="mt-1 text-xs text-slate-500">Permissions are attached to roles, then roles are assigned to users.</p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <form wire:submit="createRole" class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                                <label for="new-role" class="mb-1.5 block text-sm font-medium text-slate-700">New Role</label>
                                <div class="flex gap-2">
                                    <input wire:model="newRoleName" id="new-role" type="text" placeholder="e.g. Auditor" class="min-w-0 flex-1 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <button type="submit" class="rounded-xl bg-indigo-600 px-3 py-2 text-sm font-semibold text-white transition-colors hover:bg-indigo-700">Add</button>
                                </div>
                                @error('newRoleName') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
                            </form>

                            <form wire:submit="createPermission" class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                                <label for="new-permission" class="mb-1.5 block text-sm font-medium text-slate-700">New Permission</label>
                                <div class="flex gap-2">
                                    <input wire:model="newPermissionName" id="new-permission" type="text" placeholder="e.g. reports.export" class="min-w-0 flex-1 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-transparent focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <button type="submit" class="rounded-xl bg-indigo-600 px-3 py-2 text-sm font-semibold text-white transition-colors hover:bg-indigo-700">Add</button>
                                </div>
                                @error('newPermissionName') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
                            </form>
                        </div>

                        <div class="rounded-2xl border border-slate-100 bg-white">
                            <div class="border-b border-slate-100 px-4 py-3">
                                <p class="text-sm font-semibold text-slate-800">Role Permissions</p>
                            </div>
                            <div class="divide-y divide-slate-100">
                                @foreach($this->roles as $role)
                                    <div class="px-4 py-4">
                                        <div class="mb-3 flex items-center justify-between gap-3">
                                            <div>
                                                <p class="text-sm font-semibold text-slate-800">{{ $role->name }}</p>
                                                <p class="text-xs text-slate-400">{{ $role->permissions->count() }} permissions</p>
                                            </div>
                                            <button wire:click="editRolePermissions({{ $role->id }})" type="button" class="rounded-lg bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700 transition-colors hover:bg-slate-200">Manage</button>
                                        </div>

                                        @if((string) $permissionRoleId === (string) $role->id)
                                            <div class="grid gap-2 sm:grid-cols-2">
                                                @foreach($this->permissions as $permission)
                                                    <label class="flex cursor-pointer items-center gap-2 rounded-lg bg-slate-50 px-3 py-2 text-xs text-slate-700">
                                                        <input wire:model="rolePermissions" type="checkbox" value="{{ $permission->id }}" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                                        <span>{{ $permission->name }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                            @error('rolePermissions') <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p> @enderror
                                            <button wire:click="saveRolePermissions" type="button" class="mt-3 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-indigo-700">Save Permissions</button>
                                        @else
                                            <div class="flex flex-wrap gap-1.5">
                                                @forelse($role->permissions->take(8) as $permission)
                                                    <span class="rounded-full bg-slate-100 px-2 py-1 text-xs text-slate-600">{{ $permission->name }}</span>
                                                @empty
                                                    <span class="text-xs text-slate-400">No permissions assigned.</span>
                                                @endforelse
                                                @if($role->permissions->count() > 8)
                                                    <span class="rounded-full bg-slate-100 px-2 py-1 text-xs text-slate-500">+{{ $role->permissions->count() - 8 }} more</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div
        x-show="deleteOpen"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4 backdrop-blur-sm"
        style="display: none;"
        @keydown.escape.window="deleteOpen = false; $wire.set('showDeleteModal', false)"
    >
        <div
            x-show="deleteOpen"
            x-transition.scale.origin.center
            class="relative w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl"
        >
            <div class="p-6">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-rose-100">
                    <svg class="h-7 w-7 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="mb-2 text-center text-lg font-bold text-slate-800">Delete User</h3>
                <p class="text-center text-sm text-slate-500">Are you sure you want to delete this user? This action cannot be undone.</p>
            </div>
            <div class="flex items-center gap-3 px-6 pb-6">
                <button @click="deleteOpen = false; $wire.set('showDeleteModal', false)" class="flex-1 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition-colors hover:bg-slate-50">Cancel</button>
                <button wire:click="delete" class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white transition-all hover:bg-rose-700 active:scale-95">
                    <svg wire:loading wire:target="delete" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    Yes, Delete
                </button>
            </div>
        </div>
    </div>
</div>
