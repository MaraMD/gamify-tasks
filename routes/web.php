<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\CharacterController;

Route::get('/', fn() => redirect()->route('tasks.today'));

Route::get('/character', [CharacterController::class,'show'])->name('character.show');
Route::post('/character', [CharacterController::class,'update'])->name('character.update');


Route::get('/tasks/today', [TaskController::class,'today'])->name('tasks.today');
Route::get('/tasks/completed-week', [TaskController::class,'completedWeek'])->name('tasks.completedWeek');

// ✅ Ruta para completar tarea
Route::post('/tasks/{task}/complete', [TaskController::class,'complete'])->name('tasks.complete');

// CRUD /tasks
Route::resource('tasks', TaskController::class)->only(['index','store','edit','update','destroy']);
