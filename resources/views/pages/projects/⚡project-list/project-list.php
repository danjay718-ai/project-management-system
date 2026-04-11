<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\Project;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

/**
 * ProjectList Livewire Component
 *
 * Renders the full CRUD interface for Projects.
 * Authorization is enforced via ProjectPolicy on every action:
 *
 *   viewAny → controls page access and query scoping
 *   create  → Admin and Manager only
 *   update  → Admin always; Manager only if project owner
 *   delete  → Admin only
 */
new #[Layout('layouts.app')] #[Title('Projects')] class extends Component
{
    use WithPagination;

    // ── Filter & Search ──────────────────────────────────────────────────────

    /** @var string Live search term applied across name and description columns. */
    public string $search = '';

    /** @var string When set, filters the project list to a specific status value. */
    public string $statusFilter = '';

    // ── Modal / Form state ────────────────────────────────────────────────────

    /** @var bool Controls visibility of the create / edit slide-over modal. */
    public bool $showModal = false;

    /** @var bool Controls visibility of the delete confirmation modal. */
    public bool $showDeleteModal = false;

    /** @var int|null The ID of the project currently being edited; null when creating. */
    public ?int $editingId = null;

    /** @var int|null The ID of the project staged for deletion. */
    public ?int $deletingId = null;

    // ── Form Fields ───────────────────────────────────────────────────────────

    /** @var string Bound to the project name input field. */
    public string $name = '';

    /** @var string Bound to the project description textarea. */
    public string $description = '';

    /** @var int|string Bound to the owner select; empty string before a value is chosen. */
    public int|string $owner_id = '';

    /** @var string Bound to the status radio group; defaults to 'active'. */
    public string $status = 'active';

    // ── Lifecycle ─────────────────────────────────────────────────────────────

    /**
     * Bootstrap the component.
     *
     * Enforces the ProjectPolicy::viewAny gate so that users who do not
     * have any project membership and are not Admin / Manager are denied
     * access to the page entirely (403 response).
     *
     * Policy logic:
     *   - Admin  → always allowed
     *   - Manager → always allowed
     *   - Others → allowed only if they belong to at least one project
     */
    public function mount(): void
    {
        // Abort with 403 if the authenticated user cannot list projects.
        Gate::authorize('viewAny', Project::class);
    }

    /**
     * Reset pagination to page 1 whenever the search term changes.
     * Prevents the user from landing on a non-existent page after filtering.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination to page 1 whenever the status filter changes.
     */
    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    // ── Computed Properties ───────────────────────────────────────────────────

    /**
     * Return a paginated query of projects the current user may view.
     *
     * Scoping rules (mirrors ProjectPolicy::viewAny):
     *   - Admin / Manager → see all projects
     *   - Member          → see only projects they are assigned to
     *
     * Additional runtime filters (search, status) are layered on top.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    #[Computed]
    public function projects()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        return Project::with('owner')
            // ── Policy scope: non-admin/manager sees only their projects ──────
            ->when(
                ! $user->hasRole('Admin') && ! $user->hasRole('Manager'),
                fn($q) => $q->whereHas('users', fn($u) => $u->where('users.id', $user->id))
            )
            // ── Live search across name and description ────────────────────────
            ->when(
                $this->search,
                fn($q) => $q->where('name', 'like', "%{$this->search}%")
                             ->orWhere('description', 'like', "%{$this->search}%")
            )
            // ── Optional status filter ────────────────────────────────────────
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(8);
    }

    /**
     * Return all users for the "Owner" select in the create / edit form.
     * Cached per-request by the #[Computed] attribute.
     *
     * @return \Illuminate\Database\Eloquent\Collection<\App\Models\User>
     */
    #[Computed]
    public function users()
    {
        return User::orderBy('name')->get();
    }

    /**
     * Return project counts grouped by status for the stat-card row.
     *
     * Note: counts reflect the full dataset regardless of the user's
     * visibility scope so that admins always see totals.
     *
     * @return array{total: int, active: int, inactive: int, archived: int}
     */
    #[Computed]
    public function statusCounts(): array
    {
        return [
            'total'    => Project::count(),
            'active'   => Project::where('status', 'active')->count(),
            'inactive' => Project::where('status', 'inactive')->count(),
            'archived' => Project::where('status', 'archived')->count(),
        ];
    }

    /**
     * Expose whether the current user may create a project.
     * Used by the Blade template to conditionally show the "New Project" button.
     *
     * Policy: ProjectPolicy::create → Admin or Manager only.
     *
     * @return bool
     */
    #[Computed]
    public function canCreate(): bool
    {
        return Gate::allows('create', Project::class);
    }

    // ── Create ────────────────────────────────────────────────────────────────

    /**
     * Open the create slide-over modal.
     *
     * Authorization: ProjectPolicy::create
     * Aborts with 403 if the user is not an Admin or Manager.
     */
    public function openCreate(): void
    {
        // Enforce create policy before touching any state.
        Gate::authorize('create', Project::class);

        $this->resetForm();
        $this->editingId = null;
        $this->showModal = true;
    }

    // ── Edit ──────────────────────────────────────────────────────────────────

    /**
     * Load a project into the form and open the edit slide-over modal.
     *
     * Authorization: ProjectPolicy::update
     *   - Admin can edit any project.
     *   - Manager can edit only projects they own (owner_id === user->id).
     *   - Members cannot edit.
     *
     * @param int $id The primary key of the project to edit.
     */
    public function openEdit(int $id): void
    {
        $project = Project::findOrFail($id);

        // Enforce update policy; throws AuthorizationException (403) on failure.
        Gate::authorize('update', $project);

        // Populate form fields with the project's current values.
        $this->editingId   = $id;
        $this->name        = $project->name;
        $this->description = $project->description ?? '';
        $this->owner_id    = $project->owner_id;
        $this->status      = $project->status;
        $this->showModal   = true;
    }

    // ── Save (create or update) ────────────────────────────────────────────────

    /**
     * Validate form input then persist the project (create or update).
     *
     * Authorization is re-checked here server-side before writing to the
     * database to prevent forged Livewire calls from bypassing the UI guards.
     *
     * Validation rules:
     *   - name        required, max 255
     *   - description optional, max 1000
     *   - owner_id    required, must exist in users table
     *   - status      required, must be one of: active | inactive | archived
     */
    public function save(): void
    {
        $validated = $this->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'owner_id'    => ['required', 'exists:users,id'],
            'status'      => ['required', Rule::in(['active', 'inactive', 'archived'])],
        ]);

        if ($this->editingId) {
            // ── Update path: re-authorize update before saving ───────────────
            $project = Project::findOrFail($this->editingId);
            Gate::authorize('update', $project);

            $project->update($validated);
            session()->flash('success', 'Project updated successfully.');
        } else {
            // ── Create path: re-authorize create before saving ───────────────
            Gate::authorize('create', Project::class);

            Project::create($validated);
            session()->flash('success', 'Project created successfully.');
        }

        $this->closeModal();
    }

    // ── Delete ────────────────────────────────────────────────────────────────

    /**
     * Stage a project for deletion and open the confirmation modal.
     *
     * Authorization: ProjectPolicy::delete → Admin only.
     * The policy is checked here eagerly so non-admin users receive a 403
     * immediately rather than after confirming the dialog.
     *
     * @param int $id The primary key of the project to delete.
     */
    public function confirmDelete(int $id): void
    {
        $project = Project::findOrFail($id);

        // Enforce delete policy before showing the confirmation modal.
        Gate::authorize('delete', $project);

        $this->deletingId      = $id;
        $this->showDeleteModal = true;
    }

    /**
     * Permanently delete the staged project after user confirms.
     *
     * Authorization is re-checked to guard against forged Livewire calls.
     * Resets deletion state and closes the modal on completion.
     */
    public function delete(): void
    {
        if ($this->deletingId) {
            $project = Project::findOrFail($this->deletingId);

            // Re-authorize delete; policy = Admin only.
            Gate::authorize('delete', $project);

            $project->delete();
            session()->flash('success', 'Project deleted successfully.');
        }

        $this->showDeleteModal = false;
        $this->deletingId      = null;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Close the create / edit slide-over modal and reset all form state.
     * Also clears any pending validation errors accumulated during the session.
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    /**
     * Reset all form-bound properties back to their initial empty / default values.
     * Called both when opening a fresh create form and when closing the modal.
     */
    private function resetForm(): void
    {
        $this->name        = '';
        $this->description = '';
        $this->owner_id    = '';
        $this->status      = 'active';
        $this->editingId   = null;
    }
};
