<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('users.view') || $user->hasRole('Admin');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('users.create') || $user->hasRole('Admin');
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasPermission('users.update') || $user->hasRole('Admin');
    }

    public function delete(User $user, User $model): bool
    {
        return $user->id !== $model->id
            && ($user->hasPermission('users.delete') || $user->hasRole('Admin'));
    }

    public function assignRole(User $user): bool
    {
        return $user->hasPermission('users.assign_role') || $user->hasRole('Admin');
    }
}
