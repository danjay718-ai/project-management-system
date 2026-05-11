<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * ProjectShow Livewire Component
 *
 * Provides a detailed view of a specific project, including:
 * - Project metadata & status
 * - Toggleable Task Management (Table vs Kanban)
 * - Collapsible Member Management section
 * - Task CRUD functionality (Create, Edit, Delete)
 */
new #[Layout('layouts.app')] class extends Component
{
    use WithPagination;

    // ── Route Model Binding ───────────────────────────────────────────────────
    public Project $project;

    // ── View States ───────────────────────────────────────────────────────────
    
    /** @var string Toggle between 'table' and 'kanban' views for tasks. */
    public string $taskView = 'table';

    /** @var bool Controls the visibility of the Member Management panel. */
    public bool $showMembers = false;

    // ── Task Filtering & Search ───────────────────────────────────────────────
    public string $taskSearch = '';
    public string $taskStatusFilter = '';

    // ── Modal / Action State ──────────────────────────────────────────────────
    public bool $showTaskModal = false;
    public bool $showDeleteTaskModal = false;
    public ?int $editingTaskId = null;
    public ?int $deletingTaskId = null;

    // ── Task Form Fields ──────────────────────────────────────────────────────
    public string $taskTitle = '';
    public string $taskDescription = '';
    public ?int $taskStatusId = null;
    public ?int $taskAssignedTo = null;
    public ?string $taskDueDate = null;

    // ── Member Management Fields ──────────────────────────────────────────────
    public ?int $selectedUserId = null;

    // ── Lifecycle ─────────────────────────────────────────────────────────────

    /**
     * Authorize access to the project before mounting.
     */
    public function mount(Project $project): void
    {
        $this->project = $project;
        Gate::authorize('view', $this->project);
        
        // Set default status to the first one in the lookup table
        $defaultStatus = TaskStatus::orderBy('position')->first();
        $this->taskStatusId = $defaultStatus?->id;
    }

    /**
     * Reset pagination when search term changes.
     */
    public function updatingTaskSearch(): void
    {
        $this->resetPage();
    }

    // ── Computed Properties ───────────────────────────────────────────────────

    /**
     * Set the page title dynamically.
     */
    #[Computed]
    public function title(): string
    {
        return "Project: {$this->project->name}";
    }

    /**
     * Return all task statuses from the lookup table, ordered by position.
     */
    #[Computed]
    public function statuses()
    {
        return TaskStatus::orderBy('position')->get();
    }

    /**
     * Returns the tasks for the project.
     * When in Kanban view, we load all tasks for status grouping.
     * When in Table view, we paginate.
     */
    #[Computed]
    public function tasks()
    {
        $query = $this->project->tasks()
            ->with(['assignee', 'creator', 'taskStatus'])
            ->when($this->taskSearch, fn($q) => $q->where('title', 'like', "%{$this->taskSearch}%"))
            ->when($this->taskStatusFilter, fn($q) => $q->where('task_status_id', $this->taskStatusFilter));

        if ($this->taskView === 'kanban') {
            return $query->latest()->get();
        }

        return $query->latest()->paginate(10);
    }

    /**
     * Group tasks by status for the Kanban board.
     * Uses dynamic statuses from the lookup table.
     */
    #[Computed]
    public function kanbanTasks(): array
    {
        $tasks = $this->tasks;
        $statuses = $this->statuses;
        
        $grouped = [];
        foreach ($statuses as $status) {
            $grouped[$status->id] = $tasks->where('task_status_id', $status->id);
        }
        
        return $grouped;
    }

    /**
     * Return all project members for management.
     */
    #[Computed]
    public function members()
    {
        return $this->project->users()->orderBy('name')->get();
    }

    /**
     * Return users available to be added to the project.
     * Excludes existing members and the owner.
     */
    #[Computed]
    public function availableUsers()
    {
        $memberIds = $this->project->users->pluck('id')->toArray();
        $memberIds[] = $this->project->owner_id;

        return User::whereNotIn('id', $memberIds)
            ->orderBy('name')
            ->get();
    }

    /**
     * Return task counts for stats.
     * Uses the 'completed' status from the lookup table.
     */
    #[Computed]
    public function taskStats(): array
    {
        $completedStatus = TaskStatus::where('name', 'completed')->first();

        return [
            'total'       => $this->project->tasks()->count(),
            'pending'     => $completedStatus
                ? $this->project->tasks()->where('task_status_id', '!=', $completedStatus->id)->count()
                : $this->project->tasks()->count(),
            'completed'   => $completedStatus
                ? $this->project->tasks()->where('task_status_id', $completedStatus->id)->count()
                : 0,
        ];
    }

    // ── Task CRUD ─────────────────────────────────────────────────────────────

    public function openTaskCreate(): void
    {
        Gate::authorize('create', [Task::class, $this->project]);
        $this->resetTaskForm();
        $this->showTaskModal = true;
    }

    public function openTaskEdit(int $id): void
    {
        $task = Task::findOrFail($id);
        Gate::authorize('update', $task);

        $this->editingTaskId   = $id;
        $this->taskTitle       = $task->title;
        $this->taskDescription = $task->description ?? '';
        $this->taskStatusId    = $task->task_status_id;
        $this->taskAssignedTo  = $task->assigned_to;
        $this->taskDueDate     = $task->due_date ? $task->due_date->format('Y-m-d') : null;
        $this->showTaskModal   = true;
    }

    public function saveTask(): void
    {
        $validStatusIds = TaskStatus::pluck('id')->toArray();

        $rules = [
            'taskTitle'       => ['required', 'string', 'max:255'],
            'taskDescription' => ['nullable', 'string', 'max:1000'],
            'taskStatusId'    => ['required', Rule::in($validStatusIds)],
            'taskAssignedTo'  => ['nullable', 'exists:users,id'],
            'taskDueDate'     => ['nullable', 'date'],
        ];

        if ($this->editingTaskId) {
            $task = Task::findOrFail($this->editingTaskId);
            Gate::authorize('update', $task);
            $this->validate($rules);

            $task->update([
                'title'          => $this->taskTitle,
                'description'    => $this->taskDescription,
                'task_status_id' => $this->taskStatusId,
                'assigned_to'    => $this->taskAssignedTo,
                'due_date'       => $this->taskDueDate,
            ]);
            session()->flash('task-success', 'Task updated successfully.');
        } else {
            Gate::authorize('create', [Task::class, $this->project]);
            $this->validate($rules);

            $this->project->tasks()->create([
                'title'          => $this->taskTitle,
                'description'    => $this->taskDescription,
                'task_status_id' => $this->taskStatusId,
                'assigned_to'    => $this->taskAssignedTo,
                'due_date'       => $this->taskDueDate,
                'created_by'     => auth()->id(),
            ]);
            session()->flash('task-success', 'Task created successfully.');
        }

        $this->closeTaskModal();
    }

    public function updateTaskStatus(int $taskId, int $statusId): void
    {
        $task = Task::findOrFail($taskId);
        Gate::authorize('update', $task);
        
        // Validate that the status ID exists in the lookup table
        $status = TaskStatus::find($statusId);
        if (!$status) {
            return;
        }

        $task->update(['task_status_id' => $statusId]);
        session()->flash('task-success', "Task status updated to {$status->label}.");
    }

    public function confirmTaskDelete(int $id): void
    {
        $task = Task::findOrFail($id);
        Gate::authorize('delete', $task);

        $this->deletingTaskId = $id;
        $this->showDeleteTaskModal = true;
    }

    public function deleteTask(): void
    {
        if ($this->deletingTaskId) {
            $task = Task::findOrFail($this->deletingTaskId);
            Gate::authorize('delete', $task);
            $task->delete();
            session()->flash('task-success', 'Task deleted successfully.');
        }

        $this->showDeleteTaskModal = false;
        $this->deletingTaskId = null;
    }

    private function resetTaskForm(): void
    {
        $this->editingTaskId   = null;
        $this->taskTitle       = '';
        $this->taskDescription = '';
        $this->taskAssignedTo  = null;
        $this->taskDueDate     = null;
        $this->resetValidation();

        // Reset to the first status in the lookup table
        $defaultStatus = TaskStatus::orderBy('position')->first();
        $this->taskStatusId = $defaultStatus?->id;
    }

    public function closeTaskModal(): void
    {
        $this->showTaskModal = false;
        $this->resetTaskForm();
    }

    // ── Member Management ─────────────────────────────────────────────────────

    public function addMember(): void
    {
        Gate::authorize('update', $this->project);
        
        $this->validate([
            'selectedUserId' => ['required', 'exists:users,id', Rule::unique('project_user', 'user_id')->where('project_id', $this->project->id)],
        ], [
            'selectedUserId.unique' => 'This user is already a member of this project.',
        ]);

        $this->project->users()->attach($this->selectedUserId);
        $this->selectedUserId = null;
        session()->flash('member-success', 'Member added successfully.');
    }

    public function removeMember(int $userId): void
    {
        Gate::authorize('update', $this->project);
        $this->project->users()->detach($userId);
        session()->flash('member-success', 'Member removed successfully.');
    }

    public function toggleMembers(): void
    {
        $this->showMembers = !$this->showMembers;
    }
    
    public function setTaskView(string $view): void
    {
        if (in_array($view, ['table', 'kanban'])) {
            $this->taskView = $view;
        }
    }
};
