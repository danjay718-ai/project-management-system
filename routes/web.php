<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::livewire('/dashboard', 'pages::⚡dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Users
    Route::livewire('/user/list', 'pages::users.user-list')->name('user.list');

    // Projects
    Route::livewire('/project/list', 'pages::projects.project-list')->name('project.list');
    Route::livewire('/project/{project}', 'pages::projects.project-show')->name('project.show');
});

require __DIR__.'/auth.php';
