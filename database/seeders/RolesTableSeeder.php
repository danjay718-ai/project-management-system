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
        $roles = [
            'Admin',
            'Manager',
            'Member',
        ];

        $now = now();

        foreach ($roles as $name) {
            $exists = DB::table('roles')
                ->where('name', $name)
                ->exists();

            if (! $exists) {
                DB::table('roles')->insert([
                    'name'       => $name,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
