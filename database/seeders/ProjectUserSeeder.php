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

        // Fetch users that can be members (exclude Admin optional)
        $users = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Manager', 'Member']);
        })->get();

        foreach ($projects as $project) {

            // Attach owner automatically as member (very realistic)
            if ($project->owner_id) {
                $project->users()->syncWithoutDetaching([$project->owner_id]);
            }

            // Random members (1–3 users)
            $memberIds = $users->random(rand(1, 3))->pluck('id')->toArray();

            $project->users()->syncWithoutDetaching($memberIds);
        }
    }
}
