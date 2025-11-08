<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'species', 'level', 'exp', 'avatar_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Valor por defecto por si acaso
protected $attributes = ['xp' => 0];

// (Opcional para APIs) incluir "level" al serializar JSON
protected $appends = ['level'];

// Accesor de nivel
public function getLevelAttribute(): int
{
    return intdiv((int) ($this->xp ?? 0), 100) + 1;
}
}