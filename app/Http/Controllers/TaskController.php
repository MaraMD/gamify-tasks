<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task;
use App\Models\TaskCompletion;
use App\Services\TaskScoringService;
use App\Listeners\SystemEventLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    private function currentUserId(): int
    {
        return auth()->id();
    }

    /** Autorización mínima por pertenencia */
    private function authorizeTask(Task $task): void
    {
        abort_if($task->user_id !== $this->currentUserId(), 403);
    }

    // ---------- Listados / Vistas ----------
    public function index()
    {
        $userId = $this->currentUserId();

        $tasks = Task::where('user_id', $userId)
            ->latest('due_date')
            ->paginate(10);

        return view('tasks.index', compact('tasks'));
    }

    public function today()
    {
        $userId = $this->currentUserId();
        $tz = config('app.timezone');
        $today = now()->timezone($tz)->toDateString();
        $tomorrow = now()->timezone($tz)->addDay()->toDateString();
        $endOfWeek = now()->timezone($tz)->endOfWeek();
        $in30 = now()->timezone($tz)->addDays(30)->toDateString();

        $tasks = Task::where('user_id', $userId)
            ->where('status', Task::STATUS_PENDING)
            ->where(function ($q) use ($today) {
                $q->whereNull('due_date')
                  ->orWhereDate('due_date', '<=', $today);
            })
            ->orderByRaw('due_date IS NULL, due_date')
            ->get();

        $pendingTodayCount = $tasks->count();

        $completedThisWeek = Task::where('user_id', $userId)
            ->where('status', Task::STATUS_COMPLETED)
            ->whereBetween('updated_at', [
                now()->timezone($tz)->startOfWeek(),
                now()->timezone($tz)->endOfWeek(),
            ])
            ->count();

        // Future tasks sections
        $base = Task::where('user_id', $userId)->where('status', Task::STATUS_PENDING);

        $tomorrowTasks = (clone $base)
            ->whereDate('due_date', $tomorrow)
            ->orderBy('due_date')
            ->get();
        $tomorrowXP = $tomorrowTasks->sum('points');

        $weekTasks = (clone $base)
            ->whereDate('due_date', '>', $tomorrow)
            ->whereDate('due_date', '<=', $endOfWeek->toDateString())
            ->orderBy('due_date')
            ->get();
        $weekXP = $weekTasks->sum('points');

        $upcoming30Tasks = (clone $base)
            ->whereDate('due_date', '>', $endOfWeek->toDateString())
            ->whereDate('due_date', '<=', $in30)
            ->orderBy('due_date')
            ->get();
        $upcoming30XP = $upcoming30Tasks->sum('points');

        $futureTasks = (clone $base)
            ->whereDate('due_date', '>', $in30)
            ->orderBy('due_date')
            ->get();
        $futureXP = $futureTasks->sum('points');

        $character = auth()->user()->getOrCreateMainCharacter();

        return view('tasks.today', compact(
            'tasks',
            'pendingTodayCount',
            'completedThisWeek',
            'character',
            'tomorrowTasks',
            'tomorrowXP',
            'weekTasks',
            'weekXP',
            'upcoming30Tasks',
            'upcoming30XP',
            'futureTasks',
            'futureXP'
        ));
    }

    public function completedWeek()
    {
        $userId = $this->currentUserId();
        $start = Carbon::now()->startOfWeek();
        $end   = Carbon::now()->endOfWeek();

        $completions = TaskCompletion::where('user_id', $userId)
            ->whereBetween('completed_at', [$start, $end])
            ->with('task')
            ->orderByDesc('completed_at')
            ->get();

        return view('tasks.completed_week', compact('completions'));
    }

    // ---------- CRUD ----------
    public function store(Request $request)
    {
        $user = auth()->user();
        $auto = (bool)$request->input('auto_score', true);

        $rules = [
            'title'       => ['required','string','max:150'],
            'description' => ['nullable','string'],
            'due_date'    => ['nullable','date'],
        ];

        if ($auto) {
            $rules['difficulty'] = ['nullable','integer','in:1,2,3'];
            $rules['points'] = ['nullable','integer','min:1'];
        } else {
            $rules['difficulty'] = ['required','integer','in:1,2,3'];
            $rules['points'] = ['required','integer','min:1'];
        }

        $data = $request->validate($rules);

        if ($auto) {
            $result = app(TaskScoringService::class)->evaluate($data['title'] ?? null, $data['description'] ?? null, $data['due_date'] ?? null);
            $data['difficulty'] = $result['difficulty'];
            $data['points'] = $result['points'];
        }

        $data['status'] = Task::STATUS_PENDING;

        $task = $user->tasks()->create($data);

        // Registrar evento de creación
        SystemEventLogger::log([
            'type' => 'task.created',
            'entity_type' => 'Task',
            'entity_id' => $task->id,
            'message' => "Tarea creada: {$task->title}",
        ]);

        $message = 'Tarea creada';
        if ($auto) {
            $diffText = ['', 'Fácil', 'Media', 'Difícil'][$data['difficulty']];
            $message .= " (auto: {$diffText}, {$data['points']} pts)";
        }

        return redirect()->route('tasks.today')->with('status', $message);
    }

    public function edit(Task $task)
    {
        $this->authorizeTask($task);
        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorizeTask($task);

        $data = $request->validate([
            'title'       => ['required','string','max:150'],
            'description' => ['nullable','string'],
            'difficulty'  => ['required','integer','in:1,2,3'],
            'points'      => ['required','integer','min:1'],
            'due_date'    => ['required','date'],
            'status'      => ['required','integer','in:0,1'],
        ]);

        $task->update($data);

        // Registrar evento de actualización
        SystemEventLogger::log([
            'type' => 'task.updated',
            'entity_type' => 'Task',
            'entity_id' => $task->id,
            'message' => "Tarea actualizada: {$task->title}",
        ]);

        return redirect()->route('tasks.index')->with('status', 'Tarea actualizada.');
    }

    public function destroy(Task $task)
    {
        $this->authorizeTask($task);

        $taskTitle = $task->title;
        $taskId = $task->id;

        $task->delete();

        // Registrar evento de eliminación
        SystemEventLogger::log([
            'type' => 'task.deleted',
            'entity_type' => 'Task',
            'entity_id' => $taskId,
            'message' => "Tarea eliminada: {$taskTitle}",
        ]);

        return back()->with('status', 'Tarea eliminada.');
    }

public function complete(Task $task)
{
    $this->authorizeTask($task);

    if ($task->status === Task::STATUS_COMPLETED) {
        return back()->withErrors('La tarea ya estaba completada.');
    }

    \Illuminate\Support\Facades\DB::transaction(function () use ($task) {
        // 1) Marcar completada
        $task->update(['status' => Task::STATUS_COMPLETED]);

        // 2) Registrar completion por RELACIÓN (mejor práctica)
        $task->completions()->create([
            'user_id'        => $task->user_id,   // task_id lo pone Eloquent
            'points_awarded' => $task->points,
            'completed_at'   => now(),
        ]);

        // 3) Sumar XP al personaje principal
        $user = $task->user; // belongsTo ya cargado
        $character = $user->getOrCreateMainCharacter();
        $character->increment('xp', $task->points);

        // 4) Registrar evento de completado
        SystemEventLogger::log([
            'type' => 'task.completed',
            'entity_type' => 'Task',
            'entity_id' => $task->id,
            'message' => "Tarea completada: {$task->title} (+{$task->points} XP)",
        ]);
    });

    return back()->with('status', '¡Tarea completada! XP otorgada.');
}
}
