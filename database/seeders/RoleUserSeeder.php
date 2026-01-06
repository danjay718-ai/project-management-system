<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
        $roleUserMap = [
            'Super Admin'  => ['super.admin@example.com'],
            'Manager' => ['manager@example.com'],
            'Staff'   => ['staff@example.com'],
        ];

        foreach ($roleUserMap as $roleName => $emails) {
            $role = Role::where('name', $roleName)->first();

            if (! $role) {
                continue;
            }

            foreach ($emails as $email) {
                $user = User::where('email', $email)->first();

                if (! $user) {
                    continue;
                }

                // ✅ prevents duplicates
                $user->roles()->syncWithoutDetaching([$role->id]);
            }
        }
    }
}
