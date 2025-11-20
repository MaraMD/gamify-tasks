@extends('layouts.app')

@section('content')
@php
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

// Normaliza la colección cualquiera que sea el nombre actual
$rows = $completions ?? $completed ?? $records ?? $items ?? collect();

// Mapeo de dificultad -> badge
$difficultyBadges = [
    1 => ['class' => 'badge-soft-success', 'text' => 'Fácil'],
    2 => ['class' => 'badge-soft-warning', 'text' => 'Media'],
    3 => ['class' => 'badge-soft-danger',  'text' => 'Difícil'],
];
@endphp

<div class="container py-4">
    <div class="mb-4">
        <h2 class="mb-2">Completadas esta semana</h2>
        <p class="text-muted">Historial de tareas completadas en los últimos 7 días</p>
    </div>

    @if ($rows->isEmpty())
        <div class="alert alert-info">Aún no tienes tareas completadas esta semana.</div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col">Fecha</th>
                        <th scope="col">Tarea</th>
                        <th scope="col">Dificultad</th>
                        <th scope="col" class="text-end">XP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $c)
                        @php
                            // Fecha de completado
                            $doneAt = isset($c->completed_at) ? Carbon::parse($c->completed_at) : null;
                            $doneTxt = $doneAt ? $doneAt->format('d/m/Y H:i') : '—';

                            // Tarea
                            $task = $c->task ?? null;
                            $title = $task?->title ?? ($c->title ?? '—');

                            // Dificultad y badge seguro
                            $diffVal = $task?->difficulty ?? $c->difficulty ?? null;
                            $badge = $difficultyBadges[$diffVal] ?? ['class' => 'bg-secondary', 'text' => 'N/A'];

                            // XP otorgado (o fallback a puntos de la tarea)
                            $xp = $c->points_awarded ?? $task?->points ?? 0;
                        @endphp
                        <tr>
                            <td>{{ $doneTxt }}</td>
                            <td>
                                <div class="fw-semibold">{{ $title }}</div>
                                @if(!empty($task?->description ?? $c->description))
                                    <div class="text-muted small">
                                        {{ Str::limit($task?->description ?? $c->description, 140) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="{{ $badge['class'] }}">{{ $badge['text'] }}</span>
                            </td>
                            <td class="text-end">
                                <span class="badge-soft-primary">{{ $xp }} XP</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    @php
                        $totalXp = $rows->sum('points_awarded') ?: $rows->sum(fn($r) => $r->task->points ?? 0);
                    @endphp
                    <tr>
                        <th colspan="3" class="text-end">Total semanal</th>
                        <th class="text-end">{{ $totalXp }} XP</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        </div>
    @endif

    <a href="{{ route('tasks.today') }}" class="btn btn-outline-secondary mt-3">Volver a Hoy</a>
</div>
@endsection
