<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.app')] #[Title('Users')] class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $roleFilter = '';

    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public array $selectedRoles = [];

    public string $newRoleName = '';
    public string $newPermissionName = '';
    public int|string $permissionRoleId = '';
    public array $rolePermissions = [];

    public function mount(): void
    {
        Gate::authorize('viewAny', User::class);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function users()
    {
        return User::with('roles.permissions')
            ->when($this->search, function ($query) {
                $query->where(function ($inner) {
                    $inner->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->roleFilter, fn ($query) => $query->whereHas('roles', fn ($roleQuery) => $roleQuery->where('roles.id', $this->roleFilter)))
            ->latest()
            ->paginate(8);
    }

    #[Computed]
    public function roles()
    {
        return Role::with('permissions')->orderBy('name')->get();
    }

    #[Computed]
    public function permissions()
    {
        return Permission::orderBy('name')->get();
    }

    #[Computed]
    public function userCounts(): array
    {
        return [
            'total' => User::count(),
            'roles' => Role::count(),
            'permissions' => Permission::count(),
            'assigned' => User::whereHas('roles')->count(),
        ];
    }

    #[Computed]
    public function canCreate(): bool
    {
        return Gate::allows('create', User::class);
    }

    #[Computed]
    public function canAssignRole(): bool
    {
        return Gate::allows('assignRole', User::class);
    }

    public function openCreate(): void
    {
        Gate::authorize('create', User::class);

        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $user = User::with('roles')->findOrFail($id);
        Gate::authorize('update', $user);

        $this->editingId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->password_confirmation = '';
        $this->selectedRoles = $user->roles->pluck('id')->map(fn ($id) => (string) $id)->all();
        $this->showModal = true;
    }

    public function save(): void
    {
        $user = $this->editingId ? User::findOrFail($this->editingId) : null;

        if ($user) {
            Gate::authorize('update', $user);
        } else {
            Gate::authorize('create', User::class);
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->editingId)],
            'password' => [$this->editingId ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
        ];

        if ($this->canAssignRole) {
            $rules['selectedRoles'] = ['array'];
            $rules['selectedRoles.*'] = ['integer', 'exists:roles,id'];
        }

        $validated = $this->validate($rules);

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if ($this->password !== '') {
            $payload['password'] = $this->password;
        }

        if ($user) {
            $user->update($payload);
            session()->flash('success', 'User updated successfully.');
        } else {
            $user = User::create($payload);
            session()->flash('success', 'User created successfully.');
        }

        if ($this->canAssignRole) {
            Gate::authorize('assignRole', User::class);
            $user->roles()->sync($this->selectedRoles);
        }

        $this->closeModal();
    }

    public function confirmDelete(int $id): void
    {
        $user = User::findOrFail($id);
        Gate::authorize('delete', $user);

        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if (! $this->deletingId) {
            return;
        }

        $user = User::findOrFail($this->deletingId);
        Gate::authorize('delete', $user);

        $user->delete();
        session()->flash('success', 'User deleted successfully.');

        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function createRole(): void
    {
        Gate::authorize('assignRole', User::class);

        $validated = $this->validate([
            'newRoleName' => ['required', 'string', 'max:255', 'unique:roles,name'],
        ]);

        Role::create(['name' => $validated['newRoleName']]);
        $this->newRoleName = '';
        unset($this->roles);

        session()->flash('success', 'Role added successfully.');
    }

    public function createPermission(): void
    {
        Gate::authorize('assignRole', User::class);

        $validated = $this->validate([
            'newPermissionName' => ['required', 'string', 'max:255', 'unique:permissions,name'],
        ]);

        Permission::create(['name' => $validated['newPermissionName']]);
        $this->newPermissionName = '';
        unset($this->permissions);

        session()->flash('success', 'Permission added successfully.');
    }

    public function editRolePermissions(int $roleId): void
    {
        Gate::authorize('assignRole', User::class);

        $role = Role::with('permissions')->findOrFail($roleId);
        $this->permissionRoleId = $role->id;
        $this->rolePermissions = $role->permissions->pluck('id')->map(fn ($id) => (string) $id)->all();
    }

    public function saveRolePermissions(): void
    {
        Gate::authorize('assignRole', User::class);

        $validated = $this->validate([
            'permissionRoleId' => ['required', 'integer', 'exists:roles,id'],
            'rolePermissions' => ['array'],
            'rolePermissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        Role::findOrFail($validated['permissionRoleId'])->permissions()->sync($validated['rolePermissions'] ?? []);
        unset($this->roles);

        session()->flash('success', 'Role permissions updated successfully.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->selectedRoles = [];
    }
};
