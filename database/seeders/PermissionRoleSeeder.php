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
        $roles = DB::table('roles')
            ->whereIn('name', ['Super Admin', 'Manager', 'Staff'])
            ->pluck('id', 'name');

        $adminPermissions = DB::table('permissions')->pluck('id');

        $managerPermissions = DB::table('permissions')
            ->whereIn('name', [
                'dashboard.access',
                'users.view',
                'users.update',
                'reports.view',
                'reports.generate',
            ])
            ->pluck('id');

        $staffPermissions = DB::table('permissions')
            ->whereIn('name', [
                'dashboard.access',
                'reports.view',
            ])
            ->pluck('id');

        DB::table('permission_role')->insertOrIgnore(
            $adminPermissions->map(fn ($id) => [
                'role_id' => $roles['Super Admin'],
                'permission_id' => $id,
            ])->toArray()
        );

        DB::table('permission_role')->insertOrIgnore(
            $managerPermissions->map(fn ($id) => [
                'role_id' => $roles['Manager'],
                'permission_id' => $id,
            ])->toArray()
        );

        DB::table('permission_role')->insertOrIgnore(
            $staffPermissions->map(fn ($id) => [
                'role_id' => $roles['Staff'],
                'permission_id' => $id,
            ])->toArray()
        );
    }

}
