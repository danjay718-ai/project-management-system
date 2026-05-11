<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Models\TaskStatus;

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

        // Look up status IDs from the task_statuses table
        $notStarted = TaskStatus::where('name', 'not_started')->first();
        $inProgress = TaskStatus::where('name', 'in_progress')->first();

        if (! $notStarted || ! $inProgress) {
            return;
        }

        $tasks = [
            [
                'title' => 'Design database schema',
                'description' => 'Create project and task migrations',
                'task_status_id' => $notStarted->id,
            ],
            [
                'title' => 'Implement RBAC policies',
                'description' => 'Task and project authorization',
                'task_status_id' => $inProgress->id,
            ],
        ];

        foreach ($tasks as $task) {
            Task::updateOrCreate(
                [
                    'project_id' => $project->id,
                    'title' => $task['title'], // unique per project
                ],
                [
                    'description' => $task['description'],
                    'task_status_id' => $task['task_status_id'],
                    'assigned_to'  => $member->id,
                    'created_by'   => $manager->id,
                ]
            );
        }
    }

}
