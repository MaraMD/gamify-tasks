<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Character;
use App\Models\Task;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
public function run(): void
{
    $user = User::factory()->create([
        'id' => 1,
        'name' => 'Demo',
        'email' => 'demo@example.com',
        'password' => bcrypt('password'),
    ]);

    // Personaje inicial
    $user->characters()->create(['name' => 'Héroe', 'xp' => 120]);

    // Tareas random
    Task::factory()->count(12)->create(['user_id' => $user->id]);

    // Tareas de ejemplo del enunciado
    Task::factory()->create([
        'user_id'    => $user->id,
        'title'      => 'Hacer cena: chilaquiles',
        'description'=> 'Con salsa verde',
        'difficulty' => 1,
        'points'     => 5,
        'due_date'   => now()->toDateString(),
        'status'     => Task::STATUS_PENDING,
    ]);

    Task::factory()->create([
        'user_id'    => $user->id,
        'title'      => 'Proyecto de la materia de web',
        'description'=> 'Módulo de autenticación',
        'difficulty' => 3,
        'points'     => 20,
        'due_date'   => now()->toDateString(),
        'status'     => Task::STATUS_PENDING,
    ]);

    // Genera TaskCompletion para las que ya están completadas
    Task::where('user_id', $user->id)
        ->where('status', Task::STATUS_COMPLETED)
        ->get()
        ->each(function (Task $t) use ($user) {
            $t->completions()->create([
                'user_id'        => $t->user_id,
                'points_awarded' => $t->points,
                'completed_at'   => now()->subDay(),
            ]);
            // suma XP al personaje
            $user->getOrCreateMainCharacter()->increment('xp', $t->points);
        });
}
}