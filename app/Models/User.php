<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

     /**
     * A user may belong to many roles
     *
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Check if user has a specific role
     *
     * Best practice:
     * - Automatically load roles if not already loaded.
     * - Avoids false negatives in tests or policy checks.
     */
    public function hasRole(string $roleName): bool
    {
        //v1 original content
        // return $this->roles->contains('name', $roleName);

         // Lazy-load roles only if not loaded
        if (! $this->relationLoaded('roles')) {
            $this->load('roles');
        }

        //v1.1 updated content to optimize query
        return $this->roles()->where('name', $roleName)->exists();
    }

     /**
     * Check if user role has specific permission
     *
     *  @return bool true or false
     */
    public function hasPermission(string $permissionName): bool
    {
        foreach ($this->roles as $role) {
            if ($role->permissions->contains('name', $permissionName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * A user may belong to many projects
     *
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_user');
    }


}
