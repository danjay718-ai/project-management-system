<?php

namespace Database\Seeders;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        // SUPER ADMIN → ALL PERMISSIONS
        $adminRole = Role::where('name', 'Super Admin')->first();

        if ($adminRole) {
            $adminPermissionIds = DB::table('permissions')->pluck('id')->toArray();

            $adminRole->permissions()->syncWithoutDetaching($adminPermissionIds);
        }

        // MANAGER → LIMITED PERMISSIONS
        $managerRole = Role::where('name', 'Manager')->first();

        if ($managerRole) {
            $managerPermissionIds = DB::table('permissions')
                ->whereIn('name', [
                    'dashboard.access',
                    'users.view',
                    'users.update',
                    'reports.view',
                    'reports.generate',
                ])
                ->pluck('id')
                ->toArray();

            $managerRole->permissions()->syncWithoutDetaching($managerPermissionIds);
        }

        // STAFF → MINIMAL PERMISSIONS
        $staffRole = Role::where('name', 'Staff')->first();

        if ($staffRole) {
            $staffPermissionIds = DB::table('permissions')
                ->whereIn('name', [
                    'dashboard.access',
                    'reports.view',
                ])
                ->pluck('id')
                ->toArray();

            $staffRole->permissions()->syncWithoutDetaching($staffPermissionIds);
        }
    }
}
