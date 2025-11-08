<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    public const STATUS_PENDING   = 0;
    public const STATUS_COMPLETED = 1;

    // Seguridad: no exponer user_id a mass assignment
    protected $fillable = [
        'title', 'description', 'difficulty', 'points', 'due_date', 'status',
    ];

    protected $casts = [
        'difficulty' => 'integer',
        'points'     => 'integer',
        'due_date'   => 'date',
        'status'     => 'integer',
    ];

    protected $attributes = [
        'status' => self::STATUS_PENDING,
    ];

    /** Relaciones */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function completions(): HasMany
    {
        return $this->hasMany(TaskCompletion::class);
    }

    /** Helpers */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function markAsCompleted(): void
    {
        $this->update(['status' => self::STATUS_COMPLETED]);
    }

    /** Scopes opcionales */
    public function scopeForUser($q, int $userId)
    {
        return $q->where('user_id', $userId);
    }

    public function scopePendingToday($q)
    {
        return $q->whereDate('due_date', now()->toDateString())
                 ->where('status', self::STATUS_PENDING)
                 ->orderBy('difficulty', 'desc');
    }
}
