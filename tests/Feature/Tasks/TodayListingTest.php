<?php

use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['app.timezone' => 'America/Mexico_City']);
});

test('today listing shows only pending tasks with due date today or past or null', function () {
    $user = User::factory()->create();
    $today = now()->toDateString();
    $yesterday = now()->subDay()->toDateString();
    $tomorrow = now()->addDay()->toDateString();

    // Tareas que DEBEN aparecer
    $taskToday = Task::factory()->create([
        'user_id' => $user->id,
        'status' => Task::STATUS_PENDING,
        'due_date' => $today,
        'title' => 'Tarea de hoy'
    ]);

    $taskYesterday = Task::factory()->create([
        'user_id' => $user->id,
        'status' => Task::STATUS_PENDING,
        'due_date' => $yesterday,
        'title' => 'Tarea atrasada'
    ]);

    $taskNullDate = Task::factory()->create([
        'user_id' => $user->id,
        'status' => Task::STATUS_PENDING,
        'due_date' => null,
        'title' => 'Tarea sin fecha'
    ]);

    // Tareas que NO deben aparecer
    $taskFuture = Task::factory()->create([
        'user_id' => $user->id,
        'status' => Task::STATUS_PENDING,
        'due_date' => $tomorrow,
        'title' => 'Tarea futura'
    ]);

    $taskCompleted = Task::factory()->create([
        'user_id' => $user->id,
        'status' => Task::STATUS_COMPLETED,
        'due_date' => $today,
        'title' => 'Tarea completada'
    ]);

    $response = $this->actingAs($user)->get(route('tasks.today'));

    $response->assertOk();

    // Verificar que aparecen las tareas correctas
    $response->assertSee('Tarea de hoy');
    $response->assertSee('Tarea atrasada');
    $response->assertSee('Tarea sin fecha');

    // Verificar que NO aparecen las incorrectas
    $response->assertDontSee('Tarea futura');
    $response->assertDontSee('Tarea completada');
});

test('today listing excludes tasks from other users', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $today = now()->toDateString();

    Task::factory()->create([
        'user_id' => $user1->id,
        'status' => Task::STATUS_PENDING,
        'due_date' => $today,
        'title' => 'Tarea de usuario 1'
    ]);

    Task::factory()->create([
        'user_id' => $user2->id,
        'status' => Task::STATUS_PENDING,
        'due_date' => $today,
        'title' => 'Tarea de usuario 2'
    ]);

    $response = $this->actingAs($user1)->get(route('tasks.today'));

    $response->assertOk();
    $response->assertSee('Tarea de usuario 1');
    $response->assertDontSee('Tarea de usuario 2');
});
