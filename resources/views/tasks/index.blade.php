@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-11">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Todas mis Tareas</h2>
                <p class="text-muted mb-0">Gestiona todas tus tareas en un solo lugar</p>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreateTask">
                + Agregar tarea
            </button>
        </div>

        {{-- Tabla de tareas --}}
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                    <tr>
                        <th>Título</th>
                        <th>Dificultad</th>
                        <th>Puntos</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $t)
                        <tr>
                            <td>
                                <strong>{{ $t->title }}</strong>
                                @if($t->description)
                                    <br><small class="text-muted">{{ $t->description }}</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $difficultyBadges = [
                                        1 => ['class' => 'badge-soft-success', 'text' => 'Fácil'],
                                        2 => ['class' => 'badge-soft-warning', 'text' => 'Media'],
                                        3 => ['class' => 'badge-soft-danger', 'text' => 'Difícil'],
                                    ];
                                    $badge = $difficultyBadges[$t->difficulty] ?? ['class' => 'badge bg-secondary', 'text' => 'Nivel ' . $t->difficulty];
                                @endphp
                                <span class="{{ $badge['class'] }}">{{ $badge['text'] }}</span>
                            </td>
                            <td><span class="badge-soft-primary">{{ $t->points }} XP</span></td>
                            <td>{{ $t->due_date->format('d/m/Y') }}</td>
                            <td>
                                @if($t->status)
                                    <span class="badge-soft-success">Completada</span>
                                @else
                                    <span class="badge-soft-warning">Pendiente</span>
                                @endif
                            </td>
                            <td class="text-center" style="white-space: nowrap;">
                                <a href="{{ route('tasks.edit', $t) }}" class="btn btn-sm btn-outline-primary">
                                    Editar
                                </a>

                                <form method="POST" action="{{ route('tasks.destroy', $t) }}" class="d-inline"
                                      onsubmit="return confirm('¿Eliminar esta tarea?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        Eliminar
                                    </button>
                                </form>

                                @if($t->status == 0)
                                    <form method="POST" action="{{ route('tasks.complete', $t) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            Completar
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                No tienes tareas. ¡Crea una nueva arriba!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </div>

        {{-- Paginación --}}
        <div class="d-flex justify-content-center mt-3">
            {{ $tasks->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

@include('tasks._create_modal')
@endsection
