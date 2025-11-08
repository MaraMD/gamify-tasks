<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        // 1) primero elige la dificultad
        $difficulty = $this->faker->randomElement([1, 2, 3]);

        // 2) mapea puntos por dificultad
        $pointsMap = [1 => 5, 2 => 10, 3 => 20];
        $points = $pointsMap[$difficulty];

        // 3) fecha de vencimiento (entre hace 2 días y 3 días adelante)
        $due = Carbon::instance($this->faker->dateTimeBetween('-2 days', '+3 days'));

        // 4) algunas tareas pueden venir ya completadas
        $isCompleted = $this->faker->boolean(30);

        return [
            // si prefieres fijar al usuario 1, usa: 'user_id' => 1,
            'user_id'     => User::factory(),
            'title'       => $this->faker->sentence(3),
            'description' => $this->faker->optional()->paragraph(),
            'difficulty'  => $difficulty,
            'points'      => $points,
            'due_date'    => $due->format('Y-m-d'),
            'status'      => $isCompleted ? Task::STATUS_COMPLETED : Task::STATUS_PENDING,
            // OJO: nada de completed_at aquí; esa columna NO existe en tasks
        ];
    }
}
