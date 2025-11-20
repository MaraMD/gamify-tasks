<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemEvent;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogsController extends Controller
{
    /**
     * Muestra listado paginado de eventos del sistema con filtros
     */
    public function index(Request $request): View
    {
        $query = SystemEvent::with('user');

        // Filtro por tipo de evento
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // Filtro por usuario
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        // Filtro por rango de fechas
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $events = $query->orderByDesc('created_at')->paginate(20);

        // Obtener tipos únicos para el filtro
        $event_types = SystemEvent::select('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type');

        // Obtener usuarios para el filtro
        $users = \App\Models\User::select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return view('admin.logs.index', compact('events', 'event_types', 'users'));
    }
}
