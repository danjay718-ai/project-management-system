<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $permissions = [
            // Dashboard
            ['name' => 'dashboard.access'],

            // Users
            ['name' => 'users.view'],
            ['name' => 'users.create'],
            ['name' => 'users.update'],
            ['name' => 'users.delete'],
            ['name' => 'users.assign_role'],

            // Reports
            ['name' => 'reports.view'],
            ['name' => 'reports.generate'],

            // Settings
            ['name' => 'settings.manage'],

            // Projects
            ['name' => 'projects.view'],
            ['name' => 'projects.create'],
            ['name' => 'projects.update'],
            ['name' => 'projects.archive'],

            // Tasks
            ['name' => 'tasks.view'],
            ['name' => 'tasks.create'],
            ['name' => 'tasks.update'],
            ['name' => 'tasks.assign'],
            ['name' => 'tasks.change_status'],
            ['name' => 'tasks.close'],
        ];

        foreach ($permissions as &$permission) {
            $permission['created_at'] = $now;
            $permission['updated_at'] = $now;
        }

        DB::table('permissions')->upsert(
            $permissions,
            ['name'],
            ['updated_at']
        );
    }
}

