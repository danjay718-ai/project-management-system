<?php

use App\Models\User;
use App\Models\Project;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);


// New test to verify non-admin cannot delete a project
it('prevents non-admin from deleting a project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $result = $user->can('delete', $project);

    expect($result)->toBeFalse();
});

// New test to verify admin can delete any project
it('allows admin to delete any project', function () {
    $admin = User::factory()->admin()->create();

    $project = Project::factory()->create();

    expect($admin->can('delete', $project))->toBeTrue();
});

it('prevents non-owner from updating a project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $result = $user->can('update', $project);

    expect($result)->toBeFalse();
});

it('allows owner to update their project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $user->id]);

    $result = $user->can('update', $project);

    expect($result)->toBeTrue();
});
