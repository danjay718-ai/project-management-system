<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $roles = [
            ['name' => 'Admin'],
            ['name' => 'Manager'],
            ['name' => 'Member'],
        ];

        foreach ($roles as &$role) {
            $role['created_at'] = $now;
            $role['updated_at'] = $now;
        }

        DB::table('roles')->upsert(
            $roles,
            ['name'],
            ['updated_at']
        );
    }
}
