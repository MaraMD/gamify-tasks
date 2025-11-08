<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task;
use App\Models\TaskCompletion;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /** Helper para usar el demo user cuando no hay login */
    private function currentUserId(): int
    {
        return auth()->id() ?? 1;
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

        $tasks = Task::where('user_id', $userId)
            ->whereDate('due_date', Carbon::today())
            ->where('status', Task::STATUS_PENDING)
            ->orderBy('difficulty', 'desc')
            ->get();

        return view('tasks.today', compact('tasks'));
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
        $user = auth()->user() ?? User::findOrFail(1);

        $data = $request->validate([
            'title'       => ['required','string','max:150'],
            'description' => ['nullable','string'],
            'difficulty'  => ['required','integer','in:1,2,3'],
            'points'      => ['required','integer','min:1'],
            'due_date'    => ['required','date'],
        ]);

        $user->tasks()->create($data);
        return back()->with('status', 'Tarea creada.');
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
        return redirect()->route('tasks.index')->with('status', 'Tarea actualizada.');
    }

    public function destroy(Task $task)
    {
        $this->authorizeTask($task);
        $task->delete();
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
    });

    return back()->with('status', '¡Tarea completada! XP otorgada.');
}
}
