<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'dashboard.access',

            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'users.assign_role',

            'reports.view',
            'reports.generate',

            'settings.manage',

            'projects.view',
            'projects.create',
            'projects.update',
            'projects.archive',

            'tasks.view',
            'tasks.create',
            'tasks.update',
            'tasks.assign',
            'tasks.change_status',
            'tasks.close',
        ];

        $now = now();

        foreach ($permissions as $name) {
            $exists = DB::table('permissions')
                ->where('name', $name)
                ->exists();

            if (! $exists) {
                DB::table('permissions')->insert([
                    'name'       => $name,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}

