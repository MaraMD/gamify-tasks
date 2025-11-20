@extends('admin.layout')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Usuarios</a></li>
    <li class="breadcrumb-item active">{{ $user->name }}</li>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary mb-3">
            ← Volver al listado
        </a>
    </div>
</div>

<!-- Header con Avatar y Datos Principales -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-2 text-center">
                        @if($character)
                            <x-avatar :character="$character" size="96" />
                        @else
                            <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center"
                                 style="width: 96px; height: 96px;">
                                <span class="text-white fs-1">?</span>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-10">
                        <h2 class="mb-2">
                            {{ $user->name }}
                            @if($user->is_admin)
                                <span class="badge bg-danger">Administrador</span>
                            @endif
                        </h2>
                        <p class="text-muted mb-2">{{ $user->email }}</p>

                        @if($character)
                            <div class="d-flex gap-3 align-items-center">
                                <div>
                                    <strong>Personaje:</strong> {{ $character->name }}
                                </div>
                                <div>
                                    <span class="badge bg-info fs-6">Nivel {{ $character->level }}</span>
                                </div>
                                <div>
                                    <strong>{{ number_format($character->xp) }} XP</strong>
                                </div>
                            </div>
                        @else
                            <p class="text-muted">Este usuario no tiene un personaje creado</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Métricas del Usuario -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body text-center">
                <h6 class="card-subtitle mb-2 text-muted">Tareas Totales</h6>
                <h2 class="card-title mb-0 text-primary">{{ number_format($tasks_total) }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-warning">
            <div class="card-body text-center">
                <h6 class="card-subtitle mb-2 text-muted">Pendientes</h6>
                <h2 class="card-title mb-0 text-warning">{{ number_format($tasks_pending) }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body text-center">
                <h6 class="card-subtitle mb-2 text-muted">Completadas</h6>
                <h2 class="card-title mb-0 text-success">{{ number_format($tasks_completed) }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-info">
            <div class="card-body text-center">
                <h6 class="card-subtitle mb-2 text-muted">Completadas esta Semana</h6>
                <h2 class="card-title mb-0 text-info">{{ number_format($completed_this_week) }}</h2>
            </div>
        </div>
    </div>
</div>

<!-- Estadísticas Adicionales -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">XP Total Ganado</h6>
                <h3 class="card-title mb-0">{{ number_format($total_xp_earned) }} XP</h3>
                <small class="text-muted">De todas las tareas completadas</small>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Tasa de Completado</h6>
                <h3 class="card-title mb-0">
                    @if($tasks_total > 0)
                        {{ number_format(($tasks_completed / $tasks_total) * 100, 1) }}%
                    @else
                        0%
                    @endif
                </h3>
                <small class="text-muted">{{ $tasks_completed }} de {{ $tasks_total }} tareas</small>
            </div>
        </div>
    </div>
</div>

<!-- Últimos Eventos del Sistema -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Últimos 10 Eventos del Sistema</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Fecha/Hora</th>
                                <th>Tipo</th>
                                <th>Entidad</th>
                                <th>Mensaje</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_events as $event)
                                <tr>
                                    <td class="text-nowrap">
                                        <small>{{ $event->created_at->format('d/m/Y H:i:s') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge
                                            @if(str_starts_with($event->type, 'auth.')) bg-primary
                                            @elseif(str_starts_with($event->type, 'task.created')) bg-success
                                            @elseif(str_starts_with($event->type, 'task.completed')) bg-info
                                            @elseif(str_starts_with($event->type, 'task.updated')) bg-warning
                                            @elseif(str_starts_with($event->type, 'task.deleted')) bg-danger
                                            @else bg-secondary
                                            @endif">
                                            {{ $event->type }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($event->entity_type)
                                            <small class="text-muted">{{ $event->entity_type }} #{{ $event->entity_id }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $event->message ?? '-' }}</td>
                                    <td><small class="text-muted">{{ $event->ip ?? '-' }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">
                                        No hay eventos registrados para este usuario
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
