<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskCompletion extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['task_id', 'user_id', 'points_awarded', 'completed_at'];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function task(): BelongsTo { return $this->belongsTo(Task::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}

