<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\User;

class ProjectUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch all projects
        $projects = Project::all();

        // Fetch users that can be members (exclude Admin)
        $users = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Manager', 'Member']);
        })->get();

        foreach ($projects as $project) {

            // 1. Attach owner automatically (if exists)
            if ($project->owner_id) {
                $project->users()->syncWithoutDetaching([$project->owner_id]);
            }

            // 2. Remove owner from random candidates (avoid duplicates)
            $eligibleUsers = $users->where('id', '!=', $project->owner_id);

            // 3. Guard: no eligible users
            if ($eligibleUsers->isEmpty()) {
                continue;
            }

            // 4. Safe random count (never exceeds available users)
            $memberCount = rand(1, min(3, $eligibleUsers->count()));

            // 5. Pick random members safely
            $memberIds = $eligibleUsers
                ->random($memberCount)
                ->pluck('id')
                ->toArray();

            // 6. Attach members without removing existing ones
            $project->users()->syncWithoutDetaching($memberIds);
        }
    }
}
