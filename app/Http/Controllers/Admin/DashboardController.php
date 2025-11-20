<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Character;
use App\Models\Task;
use App\Models\TaskCompletion;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Muestra el dashboard de administración con KPIs y métricas
     */
    public function index(): View
    {
        // KPIs generales
        $users_total = User::count();
        $characters_total = Character::count();
        $tasks_total = Task::count();
        $tasks_pending = Task::where('status', Task::STATUS_PENDING)->count();
        $tasks_completed_total = Task::where('status', Task::STATUS_COMPLETED)->count();

        // KPIs de tareas de hoy
        $today = now()->toDateString();
        $tasks_today_pending = Task::where('status', Task::STATUS_PENDING)
            ->where('due_date', $today)
            ->count();

        $tasks_today_completed = TaskCompletion::whereDate('completed_at', $today)->count();

        // Métricas de XP
        $xp_total = Character::sum('xp');
        $xp_avg = Character::avg('xp');

        // Serie: Tareas completadas por día (últimos 7 días)
        $completed_by_day = TaskCompletion::select(
                DB::raw('DATE(completed_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('completed_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Asegurar que todos los días tengan un valor
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $last7Days[$date] = $completed_by_day[$date] ?? 0;
        }

        // Serie: Pendientes vs Completadas
        $pending_vs_completed = [
            'pending' => $tasks_pending,
            'completed' => $tasks_completed_total,
        ];

        // Top 5 usuarios por XP
        $top_users = User::select('users.id', 'users.name', 'users.email')
            ->join('characters', 'users.id', '=', 'characters.user_id')
            ->selectRaw('characters.xp, characters.name as character_name')
            ->selectRaw('(characters.xp DIV 100) + 1 as level')
            ->orderByDesc('characters.xp')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'users_total',
            'characters_total',
            'tasks_total',
            'tasks_pending',
            'tasks_completed_total',
            'tasks_today_pending',
            'tasks_today_completed',
            'xp_total',
            'xp_avg',
            'last7Days',
            'pending_vs_completed',
            'top_users'
        ));
    }
}
