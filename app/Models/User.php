<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name','email','password'];
    protected $hidden = ['password','remember_token'];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /** Relación con Character */
    public function characters(): HasMany
    {
        return $this->hasMany(Character::class);
    }

    /** Relación con Task */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /** Relación con TaskCompletion */
    public function taskCompletions(): HasMany
    {
        return $this->hasMany(TaskCompletion::class);
    }

    /** Personaje principal (se crea si no existe) */
    public function getOrCreateMainCharacter(): Character
    {
        return $this->characters()->firstOrCreate([], [
            'name' => 'Héroe',
            'xp'   => 0,
            'avatar' => null,
        ]);
    }
}
