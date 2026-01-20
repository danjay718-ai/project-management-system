<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

   /* ⚠️v1 Original Content⚠️:
    * This tries to fetch an Admin role from the DB:
    * Role::where('name','Admin')->first()
    * In tests with RefreshDatabase, the DB starts empty,
    * so this returns null, and $user->roles()->attach(null)
    * silently fails. The user ends up with NO roles attached.
    * As a result, hasRole('Admin') or policy checks fail.
    */
    // public function admin(): static
    // {
    //     return $this->afterCreating(function (User $user) {

    //         $adminRole = Role::where('name', 'Admin')->first();
    //         if ($adminRole) {
    //             $user->roles()->attach($adminRole);
    //         }
    //     });
    // }


    /** v1.1 Revised Content:
     * Attach admin role after creating the user.
     * We use afterCreating because the user must exist in the DB
     * to attach a many-to-many relationship.
     */

    public function admin(): static
    {
        return $this->afterCreating(function (User $user) {
            $adminRole = Role::firstOrCreate(['name' => 'Admin']); // ensure it exists
            $user->roles()->attach($adminRole);
        });
    }

    public function manager(): static
    {
        return $this->afterCreating(function (User $user) {
            $role = Role::firstOrCreate(['name' => 'Manager']);
            $user->roles()->attach($role);
        });
    }

    public function member(): static
    {
        return $this->afterCreating(function (User $user) {
            $role = Role::firstOrCreate(['name' => 'Member']);
            $user->roles()->attach($role);
        });
    }
}
