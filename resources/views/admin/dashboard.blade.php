@extends('admin.layout')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <h1 class="mb-4">Dashboard de Administración</h1>
    </div>
</div>

<!-- KPIs Principales -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Usuarios Totales</h6>
                <h2 class="card-title mb-0">{{ number_format($users_total) }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-info">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Personajes</h6>
                <h2 class="card-title mb-0">{{ number_format($characters_total) }}</h2>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Tareas Totales</h6>
                <h2 class="card-title mb-0">{{ number_format($tasks_total) }}</h2>
                <small class="text-muted">{{ number_format($tasks_completed_total) }} completadas</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-warning">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">XP Total del Sistema</h6>
                <h2 class="card-title mb-0">{{ number_format($xp_total) }}</h2>
                <small class="text-muted">Promedio: {{ number_format($xp_avg, 1) }}</small>
            </div>
        </div>
    </div>
</div>

<!-- KPIs de Hoy -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Tareas Pendientes</h6>
                <h3 class="card-title mb-0 text-warning">{{ number_format($tasks_pending) }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Pendientes Hoy</h6>
                <h3 class="card-title mb-0 text-danger">{{ number_format($tasks_today_pending) }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">Completadas Hoy</h6>
                <h3 class="card-title mb-0 text-success">{{ number_format($tasks_today_completed) }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos -->
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Tareas Completadas (Últimos 7 Días)</h5>
            </div>
            <div class="card-body">
                <canvas id="completedByDayChart" height="80"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Estado de Tareas</h5>
            </div>
            <div class="card-body">
                <canvas id="pendingVsCompletedChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Top 5 Usuarios -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Top 5 Usuarios por XP</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Posición</th>
                                <th>Usuario</th>
                                <th>Personaje</th>
                                <th>Nivel</th>
                                <th>XP</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($top_users as $index => $user)
                                <tr>
                                    <td>
                                        @if($index === 0)
                                            <span class="badge bg-warning text-dark">🥇 {{ $index + 1 }}</span>
                                        @elseif($index === 1)
                                            <span class="badge bg-secondary">🥈 {{ $index + 1 }}</span>
                                        @elseif($index === 2)
                                            <span class="badge bg-warning" style="background-color: #CD7F32 !important;">🥉 {{ $index + 1 }}</span>
                                        @else
                                            <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $user->name }}</div>
                                        <small class="text-muted">{{ $user->email }}</small>
                                    </td>
                                    <td>{{ $user->character_name }}</td>
                                    <td><span class="badge bg-info">Nivel {{ $user->level }}</span></td>
                                    <td><strong>{{ number_format($user->xp) }} XP</strong></td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No hay usuarios registrados</td>
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

@section('scripts')
<script>
// Gráfico de líneas: Tareas completadas por día
const ctx1 = document.getElementById('completedByDayChart').getContext('2d');
const completedByDayChart = new Chart(ctx1, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_keys($last7Days)) !!},
        datasets: [{
            label: 'Tareas Completadas',
            data: {!! json_encode(array_values($last7Days)) !!},
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Gráfico de dona: Pendientes vs Completadas
const ctx2 = document.getElementById('pendingVsCompletedChart').getContext('2d');
const pendingVsCompletedChart = new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: ['Pendientes', 'Completadas'],
        datasets: [{
            data: [{{ $pending_vs_completed['pending'] }}, {{ $pending_vs_completed['completed'] }}],
            backgroundColor: [
                'rgba(255, 193, 7, 0.8)',
                'rgba(40, 167, 69, 0.8)'
            ],
            borderColor: [
                'rgb(255, 193, 7)',
                'rgb(40, 167, 69)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endsection
