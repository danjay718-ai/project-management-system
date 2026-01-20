<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model

{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
        'owner_id',
        'status',
    ];

    /**
     * Get the owner of the project.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the tasks for the project.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * The users that belong to the project.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'project_user');
    }

    /**
     * Check if a user is a member of the project.
     */
    public function hasMember(User $user): bool
    {
        return $this->owner_id === $user->id
            || $this->users->contains($user->id);
    }

}
