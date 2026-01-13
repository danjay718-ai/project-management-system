<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

       $this->call([
            RolesTableSeeder::class,
            PermissionSeeder::class,
            RoleUserSeeder::class,
            PermissionRoleSeeder::class,
            UserSeeder::class,
            ProjectSeeder::class,
            TaskSeeder::class,
            ProjectUserSeeder::class,
        ]);
    }
}
