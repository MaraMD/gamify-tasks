<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'species', 'level', 'exp', 'avatar_url',
        'skin_hex', 'boxer_hex', 'hair_style', 'hair_hex', 'eyes_hex',
        'body_file', 'eyes_file', 'hair_file', 'top_file', 'bottom_file', 'acc_file',
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

/**
 * Get the public asset URL for a specific avatar layer.
 *
 * Returns the asset URL for the specified layer, using the character's
 * saved file or falling back to the first available file in the layer folder.
 *
 * @param string $layer Layer name (body, eyes, hair, top, bottom, acc)
 * @return string|null Asset URL or null if no file found
 */
public function avatarLayerPath(string $layer): ?string
{
    $field = $layer . '_file';
    $rel = $this->$field ?? null;

    if (!$rel) {
        $rel = \App\Support\Avatar::firstIn($layer);
    }

    return $rel
        ? asset(trim(config('avatar.base_path'), '/') . '/' . ltrim($rel, '/'))
        : null;
}
}