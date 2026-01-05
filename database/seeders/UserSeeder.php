<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        $adminRole = \App\Models\Role::where('name', 'Super Admin')->first();

        if ($user && $adminRole) {
            $user->roles()->syncWithoutDetaching($adminRole);
        }

    }
}
