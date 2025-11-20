@extends('admin.layout')

@section('breadcrumb')
    <li class="breadcrumb-item active">Logs del Sistema</li>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1>Logs del Sistema</h1>
    </div>
</div>

<!-- Filtros -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Filtros de Búsqueda</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.logs.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="type" class="form-label">Tipo de Evento</label>
                        <select class="form-select" id="type" name="type">
                            <option value="">Todos</option>
                            @foreach($event_types as $type)
                                <option value="{{ $type }}"
                                    {{ request('type') === $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="user_id" class="form-label">Usuario</label>
                        <select class="form-select" id="user_id" name="user_id">
                            <option value="">Todos</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="date_from" class="form-label">Desde</label>
                        <input type="date"
                               class="form-control"
                               id="date_from"
                               name="date_from"
                               value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-2">
                        <label for="date_to" class="form-label">Hasta</label>
                        <input type="date"
                               class="form-control"
                               id="date_to"
                               name="date_to"
                               value="{{ request('date_to') }}">
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Logs -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Eventos del Sistema</h5>
                <span class="badge bg-secondary">{{ $events->total() }} eventos</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 140px;">Fecha/Hora</th>
                                <th style="width: 150px;">Tipo</th>
                                <th style="width: 180px;">Usuario</th>
                                <th style="width: 120px;">Entidad</th>
                                <th>Mensaje</th>
                                <th style="width: 130px;">IP</th>
                                <th style="width: 200px;">User Agent</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($events as $event)
                                <tr>
                                    <td class="text-nowrap">
                                        <small>{{ $event->created_at->format('d/m/Y H:i:s') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge
                                            @if(str_starts_with($event->type, 'auth.login')) bg-success
                                            @elseif(str_starts_with($event->type, 'auth.logout')) bg-secondary
                                            @elseif(str_starts_with($event->type, 'task.created')) bg-primary
                                            @elseif(str_starts_with($event->type, 'task.completed')) bg-info
                                            @elseif(str_starts_with($event->type, 'task.updated')) bg-warning
                                            @elseif(str_starts_with($event->type, 'task.deleted')) bg-danger
                                            @else bg-dark
                                            @endif">
                                            {{ $event->type }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($event->user)
                                            <div>{{ $event->user->name }}</div>
                                            <small class="text-muted">{{ $event->user->email }}</small>
                                        @else
                                            <span class="text-muted">Sistema</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($event->entity_type && $event->entity_id)
                                            <small class="text-muted">
                                                {{ $event->entity_type }}<br>#{{ $event->entity_id }}
                                            </small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $event->message ?? '-' }}</td>
                                    <td><small class="text-muted">{{ $event->ip ?? '-' }}</small></td>
                                    <td>
                                        <small class="text-muted text-truncate d-inline-block" style="max-width: 200px;" title="{{ $event->user_agent }}">
                                            {{ $event->user_agent ?? '-' }}
                                        </small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No se encontraron eventos con los filtros aplicados
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($events->hasPages())
                <div class="card-footer">
                    {{ $events->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
