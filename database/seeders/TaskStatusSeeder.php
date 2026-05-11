<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaskStatus;

class TaskStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'not_started', 'label' => 'Not Started', 'color' => 'slate',   'position' => 0],
            ['name' => 'in_progress', 'label' => 'In Progress', 'color' => 'blue',    'position' => 1],
            ['name' => 'review',      'label' => 'Review',      'color' => 'amber',   'position' => 2],
            ['name' => 'completed',   'label' => 'Completed',   'color' => 'emerald', 'position' => 3],
        ];

        foreach ($statuses as $status) {
            TaskStatus::updateOrCreate(
                ['name' => $status['name']],
                $status
            );
        }
    }
}
