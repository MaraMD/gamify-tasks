@extends('admin.layout')

@section('breadcrumb')
    <li class="breadcrumb-item active">Usuarios</li>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1>Gestión de Usuarios</h1>
    </div>
</div>

<!-- Filtros de Búsqueda -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                    <div class="col-md-10">
                        <label for="search" class="form-label">Buscar por nombre o email</label>
                        <input type="text"
                               class="form-control"
                               id="search"
                               name="search"
                               value="{{ $search }}"
                               placeholder="Ejemplo: Juan, admin@example.com">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Buscar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Usuarios -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Listado de Usuarios</h5>
                <span class="badge bg-secondary">{{ $users->total() }} usuarios</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Personaje</th>
                                <th>Nivel</th>
                                <th>XP</th>
                                <th>Tareas</th>
                                <th>Completadas</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>
                                        {{ $user->name }}
                                        @if($user->is_admin)
                                            <span class="badge bg-danger ms-1">Admin</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->characters->isNotEmpty())
                                            {{ $user->characters->first()->name }}
                                        @else
                                            <span class="text-muted">Sin personaje</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->characters->isNotEmpty())
                                            <span class="badge bg-info">
                                                Nivel {{ $user->characters->first()->level }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->characters->isNotEmpty())
                                            <strong>{{ number_format($user->characters->first()->xp) }}</strong>
                                        @else
                                            <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $user->tasks_total ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $user->tasks_completed ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $user->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            Ver Detalle
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        No se encontraron usuarios
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($users->hasPages())
                <div class="card-footer">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
