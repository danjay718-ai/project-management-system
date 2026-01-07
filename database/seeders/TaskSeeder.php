<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::first();
        $member  = User::where('email', 'member@example.com')->first();
        $manager = User::where('email', 'manager@example.com')->first();

        if (! $project || ! $member || ! $manager) {
            return;
        }

        Task::create([
            'project_id' => $project->id,
            'title' => 'Design database schema',
            'description' => 'Create project and task migrations',
            'status' => 'pending',
            'assigned_to' => $member->id,
            'created_by' => $manager->id,
        ]);

        Task::create([
            'project_id' => $project->id,
            'title' => 'Implement RBAC policies',
            'description' => 'Task and project authorization',
            'status' => 'in_progress',
            'assigned_to' => $member->id,
            'created_by' => $manager->id,
        ]);
    }
}
