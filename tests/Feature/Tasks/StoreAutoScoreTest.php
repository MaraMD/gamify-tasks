<?php

use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['app.timezone' => 'America/Mexico_City']);
});

test('can create task with auto score and redirects to tasks.today', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('tasks.store'), [
        'title' => 'Tarea con auto-scoring',
        'description' => 'Esta es una descripción larga para probar el auto-scoring del sistema',
        'due_date' => now()->addDays(2)->toDateString(),
        'auto_score' => true,
    ]);

    $response->assertRedirect(route('tasks.today'));

    // Verificar que la tarea se creó
    $this->assertDatabaseHas('tasks', [
        'user_id' => $user->id,
        'title' => 'Tarea con auto-scoring',
        'status' => Task::STATUS_PENDING,
    ]);

    // Verificar que tiene difficulty y points en rangos válidos
    $task = Task::where('title', 'Tarea con auto-scoring')->first();
    expect($task->difficulty)->toBeGreaterThanOrEqual(1);
    expect($task->difficulty)->toBeLessThanOrEqual(3);
    expect($task->points)->toBeGreaterThanOrEqual(5);
    expect($task->points)->toBeLessThanOrEqual(100);
    expect($task->status)->toBe(Task::STATUS_PENDING);
});

test('task creation fails with validation error when title is missing', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('tasks.store'), [
        'description' => 'Descripción sin título',
        'due_date' => now()->addDays(2)->toDateString(),
        'auto_score' => true,
    ]);

    $response->assertSessionHasErrors(['title']);

    // Verificar que NO se creó la tarea
    $this->assertDatabaseMissing('tasks', [
        'user_id' => $user->id,
        'description' => 'Descripción sin título',
    ]);
});

test('task creation fails with validation error when title exceeds max length', function () {
    $user = User::factory()->create();

    $longTitle = str_repeat('a', 151); // Más de 150 caracteres

    $response = $this->actingAs($user)->post(route('tasks.store'), [
        'title' => $longTitle,
        'description' => 'Descripción válida',
        'due_date' => now()->addDays(2)->toDateString(),
        'auto_score' => true,
    ]);

    $response->assertSessionHasErrors(['title']);

    // Verificar que NO se creó la tarea
    $this->assertDatabaseMissing('tasks', [
        'user_id' => $user->id,
        'title' => $longTitle,
    ]);
});

test('auto scored task has status pending by default', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('tasks.store'), [
        'title' => 'Verificar status',
        'description' => 'Test de status',
        'due_date' => now()->toDateString(),
        'auto_score' => true,
    ]);

    $task = Task::where('title', 'Verificar status')->first();

    expect($task->status)->toBe(Task::STATUS_PENDING);
});
