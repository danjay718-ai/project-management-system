# Developer Guide: Project List CRUD with Livewire 4 MFC

This document walks through the complete process of building the **Projects** management page — from initial UI design to the final multi-file component (MFC) structure with policy-based authorization.

---

## 1. What We Built

The Projects page is a fully reactive CRUD interface built with **Livewire 4 Volt** conventions. It provides:

- A paginated, searchable project table
- Live status filter with stat cards
- A slide-over panel for creating and editing projects
- A centered modal for delete confirmation
- Policy-enforced authorization at the UI and server level

---

## 2. Starting Point: The Single-File Component (SFC)

The component started as a **Volt single-file component** (`⚡project-list.blade.php`) — a single file that holds both the PHP class and the Blade template separated by `<?php ... ?>` delimiters.

### The Volt SFC format

```php
<?php

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

new #[Layout('layouts.app')] #[Title('Projects')] class extends Component
{
    public array $projects = [];

    public function mount()
    {
        $this->projects = Project::all();
    }
};
?>

<div>
    {{-- Blade template here --}}
</div>
```

The `#[Layout]` and `#[Title]` PHP attributes tell Livewire which layout wrapper and browser title to apply automatically — no need to wrap `@extends` in the Blade file.

---

## 3. Routing: No Controller Needed

Like other pages in this app, the projects page is registered directly in `routes/web.php` using `Route::livewire()`:

```php
Route::livewire('/project/list', 'pages::projects.project-list')
    ->name('project.list');
```

- `pages::projects.project-list` is the Volt component locator
- Livewire resolves this to `resources/views/pages/projects/⚡project-list/`
- No Controller, no `return view(...)`, no manual data passing

---

## 4. Key Livewire 4 Features Used

### 4.1 `#[Computed]` — Replacing `mount()` for Derived Data

Rather than loading all data inside `mount()` (which runs once), we use `#[Computed]` properties. These are cached per-request and automatically re-evaluated when their dependencies change.

```php
#[Computed]
public function projects()
{
    return Project::with('owner')
        ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
        ->paginate(8);
}
```

In the Blade template, computed properties are accessed via `$this->`:

```blade
@forelse($this->projects as $project)
    ...
@endforelse
```

> [!TIP]
> Use `#[Computed]` over `mount()` for data that depends on live component state (like `$search` or `$statusFilter`). Memoization ensures the query only runs once per render cycle even if referenced multiple times in the template.

### 4.2 `WithPagination` — Server-Side Pagination

The `WithPagination` trait adds Livewire-aware pagination that resets correctly when filters change:

```php
use WithPagination;

public function updatingSearch(): void
{
    $this->resetPage(); // go back to page 1 on every new keystroke
}
```

The Blade output uses:

```blade
{{ $this->projects->links() }}
```

### 4.3 `wire:model.live.debounce` — Reactive Search

The search input sends live updates to the component, debounced by 300ms to avoid firing a database query on every keystroke:

```blade
<input wire:model.live.debounce.300ms="search" ...>
```

### 4.4 `wire:model.live` — Reactive Status Radio Buttons

The status radio group in the create/edit form updates the component state immediately so the selected card highlights reactively without a full re-render:

```blade
<input wire:model.live="status" type="radio" value="active">
```

### 4.5 `wire:loading` — Spinner Feedback

Spinner icons are shown while a Livewire action is in flight, scoped to a specific target:

```blade
<svg wire:loading wire:target="save" class="animate-spin ...">...</svg>
```

### 4.6 Alpine.js `$wire.entangle()` — Modal State Bridge

The modals are controlled by Alpine.js for smooth CSS transitions, but their open/closed state lives in the Livewire component. `$wire.entangle()` creates a two-way reactive bridge:

```blade
<div x-data="{ modalOpen: $wire.entangle('showModal'), deleteOpen: $wire.entangle('showDeleteModal') }">
```

This means:
- When Livewire sets `$showModal = true` (e.g., after `openCreate()`), Alpine's `modalOpen` becomes `true` and the slide-over animates in
- When Alpine sets `modalOpen = false` (e.g., pressing Escape), Livewire's `$showModal` also updates

> [!IMPORTANT]
> Because Livewire 4 manages its own Alpine.js instance, you must **not** call `Alpine.start()` manually in `resources/js/app.js`. Doing so creates a second Alpine instance and breaks `$wire` access entirely.

---

## 5. Converting SFC → MFC (Multi-File Component)

Once the component grew beyond ~200 lines, it was converted to a **Multi-File Component** so the PHP class and Blade template live in separate, independently editable files. This was done with a single artisan command:

```bash
php artisan livewire:convert pages::projects.project-list --mfc
```

This command:
1. **Parsed** the single file, splitting the `<?php ?>` block from the Blade template
2. **Created** a new directory: `resources/views/pages/projects/⚡project-list/`
3. **Wrote** `project-list.php` (the class) and `project-list.blade.php` (the template)
4. **Deleted** the original `⚡project-list.blade.php`

### Resulting MFC structure

```
resources/views/pages/projects/⚡project-list/
├── project-list.php          ← PHP class (all component logic)
└── project-list.blade.php    ← Blade template (UI only)
```

The route registration in `web.php` requires **no changes** — Livewire's component resolver detects the directory automatically.

---

## 6. Authorization with `ProjectPolicy`

Every action in the component is enforced by `App\Policies\ProjectPolicy`. The policy is applied at two layers:

| Layer | Tool | Why |
|---|---|---|
| UI (Blade) | `@can`, `@if($this->canCreate)` | Hides buttons the user cannot use |
| Server (PHP) | `Gate::authorize()` | Prevents forged Livewire calls |

### 6.1 Page Access — `viewAny`

```php
public function mount(): void
{
    Gate::authorize('viewAny', Project::class);
}
```

- Admin and Manager → always allowed
- Members → allowed only if they belong to at least one project
- Others → 403

### 6.2 Query Scoping — mirrors `viewAny`

The `projects()` computed property scopes the SQL query to match the policy:

```php
->when(
    ! $user->hasRole('Admin') && ! $user->hasRole('Manager'),
    fn($q) => $q->whereHas('users', fn($u) => $u->where('users.id', $user->id))
)
```

### 6.3 Create — `create` policy

```php
// Server: inside openCreate() and save() create branch
Gate::authorize('create', Project::class);

// UI: New Project button
@if($this->canCreate)
    <button wire:click="openCreate">New Project</button>
@endif
```

Policy: **Admin or Manager** only.

### 6.4 Edit — `update` policy

```php
// Server: inside openEdit() and save() update branch
Gate::authorize('update', $project);

// UI: Edit button per row
@can('update', $project)
    <button wire:click="openEdit({{ $project->id }})">Edit</button>
@endcan
```

Policy: **Admin** can edit any project; **Manager** can edit only projects they own (`owner_id === user->id`).

### 6.5 Delete — `delete` policy

```php
// Server: inside confirmDelete() and delete()
Gate::authorize('delete', $project);

// UI: Delete button per row
@can('delete', $project)
    <button wire:click="confirmDelete({{ $project->id }})">Delete</button>
@endcan
```

Policy: **Admin only**.

> [!IMPORTANT]
> The `Gate::authorize()` call inside the action methods is not redundant — it is essential. `@can` / `@if` only control whether a button *renders*, they do not prevent a determined user from sending the Livewire action directly via the network. Always re-check authorization server-side before any write operation.

---

## 7. Design System Choices

| Element | Approach |
|---|---|
| Layout shell | `layouts.app` (dark sidebar + frosted topbar) |
| Colours | Indigo-600 primary, Emerald for active, Amber for inactive, Slate for archived |
| Table rows | `opacity-0 group-hover:opacity-100` action buttons (progressive disclosure) |
| Create/Edit form | Right-side slide-over panel (`translate-x-full → translate-x-0`) |
| Delete confirmation | Centered scale-in modal (`scale-95 → scale-100`) |
| Success toast | Fixed bottom-right toast, auto-dismissed after 4 seconds via Alpine `setTimeout` |
| Stat cards | Clickable filter shortcuts that set `$statusFilter` via `wire:click="$set(...)"` |

---

## 8. Files Reference

| File | Purpose |
|---|---|
| `resources/views/pages/projects/⚡project-list/project-list.php` | Livewire component class — all PHP logic, properties, computed methods, authorization |
| `resources/views/pages/projects/⚡project-list/project-list.blade.php` | Blade template — UI, Alpine.js bindings, modals |
| `app/Policies/ProjectPolicy.php` | Authorization rules for all project CRUD actions |
| `app/Models/Project.php` | Eloquent model — fillable fields, `owner()`, `tasks()`, `users()` relationships |
| `routes/web.php` | Route registration via `Route::livewire()` |
