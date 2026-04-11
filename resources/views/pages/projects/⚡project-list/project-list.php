<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\Project;
use App\Models\User;
use Illuminate\Validation\Rule;

new #[Layout('layouts.app')] #[Title('Projects')] class extends Component
{
    use WithPagination;

    // ── Filter & Search ──────────────────────────────────────────────────────
    public string $search = '';
    public string $statusFilter = '';

    // ── Modal / Form state ────────────────────────────────────────────────────
    public bool $showModal = false;
    public bool $showDeleteModal = false;

    public ?int $editingId = null;
    public ?int $deletingId = null;

    // ── Form Fields ───────────────────────────────────────────────────────────
    public string $name = '';
    public string $description = '';
    public int|string $owner_id = '';
    public string $status = 'active';

    // ── Lifecycle ─────────────────────────────────────────────────────────────
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    // ── Computed ──────────────────────────────────────────────────────────────
    #[Computed]
    public function projects()
    {
        return Project::with('owner')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('description', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(8);
    }

    #[Computed]
    public function users()
    {
        return User::orderBy('name')->get();
    }

    #[Computed]
    public function statusCounts()
    {
        return [
            'total'     => Project::count(),
            'active'    => Project::where('status', 'active')->count(),
            'inactive'  => Project::where('status', 'inactive')->count(),
            'archived'  => Project::where('status', 'archived')->count(),
        ];
    }

    // ── Create ────────────────────────────────────────────────────────────────
    public function openCreate(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showModal = true;
    }

    // ── Edit ──────────────────────────────────────────────────────────────────
    public function openEdit(int $id): void
    {
        $project = Project::findOrFail($id);
        $this->editingId = $id;
        $this->name        = $project->name;
        $this->description = $project->description ?? '';
        $this->owner_id    = $project->owner_id;
        $this->status      = $project->status;
        $this->showModal   = true;
    }

    // ── Save (create or update) ────────────────────────────────────────────────
    public function save(): void
    {
        $validated = $this->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'owner_id'    => ['required', 'exists:users,id'],
            'status'      => ['required', Rule::in(['active', 'inactive', 'archived'])],
        ]);

        if ($this->editingId) {
            Project::findOrFail($this->editingId)->update($validated);
            session()->flash('success', 'Project updated successfully.');
        } else {
            Project::create($validated);
            session()->flash('success', 'Project created successfully.');
        }

        $this->closeModal();
    }

    // ── Delete ────────────────────────────────────────────────────────────────
    public function confirmDelete(int $id): void
    {
        $this->deletingId      = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            Project::findOrFail($this->deletingId)->delete();
            session()->flash('success', 'Project deleted successfully.');
        }
        $this->showDeleteModal = false;
        $this->deletingId      = null;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    private function resetForm(): void
    {
        $this->name        = '';
        $this->description = '';
        $this->owner_id    = '';
        $this->status      = 'active';
        $this->editingId   = null;
    }
};
