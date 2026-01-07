<?php

namespace Database\Seeders;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class PermissionRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = DB::table('roles')
            ->whereIn('name', ['Admin', 'Manager', 'Member'])
            ->pluck('id', 'name');

        $allPermissions = DB::table('permissions')->pluck('id');

        $managerPermissions = DB::table('permissions')
            ->whereIn('name', [
                'dashboard.access',

                'projects.view',
                'projects.create',
                'projects.update',

                'tasks.view',
                'tasks.create',
                'tasks.assign',
                'tasks.close',

                'reports.view',
                'reports.generate',
            ])
            ->pluck('id');

        $memberPermissions = DB::table('permissions')
            ->whereIn('name', [
                'dashboard.access',

                'projects.view',

                'tasks.view',
                'tasks.update',
                'tasks.change_status',
            ])
            ->pluck('id');

        // Admin → ALL permissions
        DB::table('permission_role')->insertOrIgnore(
            $allPermissions->map(fn ($id) => [
                'role_id' => $roles['Admin'],
                'permission_id' => $id,
            ])->toArray()
        );

        // Manager
        DB::table('permission_role')->insertOrIgnore(
            $managerPermissions->map(fn ($id) => [
                'role_id' => $roles['Manager'],
                'permission_id' => $id,
            ])->toArray()
        );

        // Member
        DB::table('permission_role')->insertOrIgnore(
            $memberPermissions->map(fn ($id) => [
                'role_id' => $roles['Member'],
                'permission_id' => $id,
            ])->toArray()
        );
    }
}
