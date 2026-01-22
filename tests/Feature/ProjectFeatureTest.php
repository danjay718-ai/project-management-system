<?php

use App\Models\User;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows admin to delete a project', function () {
    // 1️⃣ Given: an admin user and a project
    $admin = User::factory()->admin()->create();
    $project = Project::factory()->create();

    // 2️⃣ When: admin sends DELETE request
    $response = $this->actingAs($admin)
        ->delete(route('projects.destroy', $project));

    // 3️⃣ Then: request succeeds
    $response->assertRedirect();

    // 4️⃣ And: project is deleted in database
    expect(Project::find($project->id))->toBeNull();
});
