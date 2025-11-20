<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemEvent extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'entity_type',
        'entity_id',
        'message',
        'ip',
        'user_agent',
    ];

    /**
     * Relación con el usuario que generó el evento
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con la tarea (si entity_type es Task)
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'entity_id')
            ->where('entity_type', 'Task');
    }
}
