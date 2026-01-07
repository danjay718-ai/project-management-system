<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\User;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $manager = User::where('email', 'manager@example.com')->first();

        if (! $manager) {
            return;
        }

        Project::updateOrCreate(
            ['name' => 'Task Management System'],
            [
                'description' => 'Internal task tracking system',
                'owner_id' => $manager->id,
                'status' => 'active',
            ]
        );
    }
}
