<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin can see all
        if ($user->hasRole('Admin')) {
            return true;
        }

        // Manager can see all
        if ($user->hasRole('Manager')) {
            return true;
        }

        // Members → can see their assigned projects
        return $user->projects()->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {

        // Admin can view any project
        if ($user->hasRole('Admin')) {
            return true;
        }

       // 2. Otherwise, user must be part of the project
        return $project->users->contains($user->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only Admin and Manager can create projects
        return $user->hasRole('Admin') || $user->hasRole('Manager');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        // 1. Admin can update any project
        if ($user->hasRole('Admin')) {
            return true;
        }

        // 2. Manager can update if they are the owner
        return $project->owner_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        // Only Admin can delete projects
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Project $project): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Project $project): bool
    {
        return false;
    }
}
