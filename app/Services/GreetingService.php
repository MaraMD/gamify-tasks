<?php

namespace App\Services;

use App\Models\User;
use App\Models\Character;
use Illuminate\Support\Arr;

class GreetingService
{
    public function make(User $user, ?Character $character = null): array
    {
        $name = $user->name ?? 'Héroe';
        $char = $character?->name ?: 'tu personaje';

        // Detect time-based greeting
        $hour = now()->timezone(config('app.timezone'))->format('H');
        $greet = match (true) {
            $hour < 12 => 'Buenos días',
            $hour < 19 => 'Buenas tardes',
            default => 'Buenas noches',
        };

        // Greeting templates
        $templates = [
            ':greet, :user. :character te espera.',
            '¡Hola, :user! :character está listo.',
            '¡Qué gusto verte, :user! :character ya calentó.',
            'A darle, :user — :character confía en ti.',
            'Listo para subir de nivel, :user. :character está contigo.',
            'Es hora, :user. :character ya está aquí.',
            '¡Hey, :user! :character quiere acción.',
        ];

        // Subtitle variations
        $subtitles = [
            'Tareas de hoy',
            'Hoy es un buen día para avanzar',
            'Un paso a la vez',
            'Tu racha comienza ahora',
            'Organiza, ejecuta y gana XP',
        ];

        // Select random template and subtitle
        $title = Arr::random($templates);
        $subtitle = Arr::random($subtitles);

        // Replace placeholders
        $title = str_replace(
            [':greet', ':user', ':character'],
            [$greet, $name, $char],
            $title
        );

        return [$title, $subtitle];
    }
}
