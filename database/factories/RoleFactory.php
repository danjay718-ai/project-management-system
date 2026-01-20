<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Admin',
                'Manager',
                'Member',
            ]),
        ];
    }

    public function admin()
    {
        return $this->state(fn () => [
            'name' => 'Admin',
        ]);
    }

    public function manager()
    {
        return $this->state(fn () => [
            'name' => 'Manager',
        ]);
    }

    public function member()
    {
        return $this->state(fn () => [
            'name' => 'Member',
        ]);
    }
}
