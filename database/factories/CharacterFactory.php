<?php

namespace Database\Factories;

use App\Models\Character;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CharacterFactory extends Factory
{
    protected $model = Character::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->firstName(),
            'species' => $this->faker->randomElement(['Humano','Elfo','Mago','Robot']),
            'level' => 1,
            'exp' => 0,
            'avatar_url' => null,
        ];
    }
}