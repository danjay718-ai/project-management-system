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
            [
                'name' => 'dashboard.access',

            ],

            // User management
            [
                'name' => 'users.view',

            ],
            [
                'name' => 'users.create',

            ],
            [
                'name' => 'users.update',

            ],
            [
                'name' => 'users.delete',

            ],
            [
                'name' => 'users.assign_role',
            ],

            // Reports
            [
                'name' => 'reports.view',
            ],
            [
                'name' => 'reports.generate',
            ],

            // System settings
            [
                'name' => 'settings.manage',
            ],
        ];

        foreach ($permissions as &$permission) {
            $permission['created_at'] = $now;
            $permission['updated_at'] = $now;
        }

        DB::table('permissions')->insert($permissions);
    }
}
