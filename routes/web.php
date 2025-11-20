<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\CharacterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Home route - redirect based on auth state
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('tasks.today')
        : redirect()->route('login');
});

// Auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected routes - require authentication
Route::middleware('auth')->group(function () {
    // Character routes
    Route::get('/character', [CharacterController::class, 'show'])->name('character.show');
    Route::patch('/character', [CharacterController::class, 'update'])->name('character.update');

    // Task routes
    Route::get('/tasks/today', [TaskController::class, 'today'])->name('tasks.today');
    Route::get('/tasks/completed-week', [TaskController::class, 'completedWeek'])->name('tasks.completedWeek');
    Route::post('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
    Route::resource('tasks', TaskController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);
});

// Admin routes - require authentication + admin privileges
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [App\Http\Controllers\Admin\UsersController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [App\Http\Controllers\Admin\UsersController::class, 'show'])->name('users.show');
    Route::get('/logs', [App\Http\Controllers\Admin\LogsController::class, 'index'])->name('logs.index');
});
