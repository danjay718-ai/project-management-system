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
            ['name' => 'dashboard.access'],

            ['name' => 'users.view'],
            ['name' => 'users.create'],
            ['name' => 'users.update'],
            ['name' => 'users.delete'],
            ['name' => 'users.assign_role'],

            ['name' => 'reports.view'],
            ['name' => 'reports.generate'],

            ['name' => 'settings.manage'],
        ];

        foreach ($permissions as &$permission) {
            $permission['created_at'] = $now;
            $permission['updated_at'] = $now;
        }

        DB::table('permissions')->upsert(
            $permissions,
            ['name'],              // unique key
            ['updated_at']         // columns to update if exists
        );
    }
}
