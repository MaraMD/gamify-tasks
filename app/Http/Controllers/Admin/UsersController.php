<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SystemEvent;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    /**
     * Muestra listado paginado de usuarios con filtro de búsqueda
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $users = User::query()
            ->with('characters')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            // Agregar contadores de tareas con subselects para evitar N+1
            ->withCount([
                'tasks as tasks_total' => function ($query) {
                    $query->select(DB::raw('count(*)'));
                },
                'tasks as tasks_completed' => function ($query) {
                    $query->where('status', 1);
                },
            ])
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.users.index', compact('users', 'search'));
    }

    /**
     * Muestra detalle de un usuario específico
     */
    public function show(User $user): View
    {
        // Cargar relaciones necesarias
        $user->load('characters');

        // Obtener el personaje principal
        $character = $user->getOrCreateMainCharacter();

        // Contadores de tareas
        $tasks_total = $user->tasks()->count();
        $tasks_pending = $user->tasks()->where('status', 0)->count();
        $tasks_completed = $user->tasks()->where('status', 1)->count();

        // Últimos 10 eventos del sistema relacionados con este usuario
        $recent_events = SystemEvent::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Estadísticas de completado
        $completed_this_week = $user->taskCompletions()
            ->whereBetween('completed_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])
            ->count();

        $total_xp_earned = $user->taskCompletions()->sum('points_awarded');

        return view('admin.users.show', compact(
            'user',
            'character',
            'tasks_total',
            'tasks_pending',
            'tasks_completed',
            'recent_events',
            'completed_this_week',
            'total_xp_earned'
        ));
    }
}
