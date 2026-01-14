<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        // 1. Admins can view all tasks
        if ($user->hasRole('Admin')) {
            return true;
        }

        // 2. Users with permission to view the project can view tasks within it
        if ($user->can('view', $task->project)) {
            return true;
        }

        // 3. Otherwise, only assignee or creator can view the task
        return $task->assignee->id === $user->id || $task->creator->id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Project $project): bool
    {
        // 1. Admins can create tasks in any project
        if ($user->hasRole('Admin')) {
            return true;
        }

        // 2. Project owners can create tasks in their projects
        return $project->owner_id === $user->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }

        if (($user->can('update', $task->project))) {
            return true;
        }

        return $task->assigned_to === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return false;
    }
}
