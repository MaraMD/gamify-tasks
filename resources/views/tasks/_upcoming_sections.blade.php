@php
    $sections = [
        ['label' => 'Mañana', 'items' => $tomorrowTasks, 'xp' => $tomorrowXP],
        ['label' => 'Esta semana', 'items' => $weekTasks, 'xp' => $weekXP],
        ['label' => 'Próximas (≤30 días)', 'items' => $upcoming30Tasks, 'xp' => $upcoming30XP],
    ];
@endphp

@foreach ($sections as $sec)
    @if ($sec['items']->isNotEmpty())
        <div class="mt-5 mb-3">
            <h4 class="mb-1">{{ $sec['label'] }}</h4>
            <div class="d-flex gap-2 align-items-center">
                <span class="badge bg-light">{{ $sec['items']->count() }} tareas</span>
                <span class="badge-soft-info">{{ $sec['xp'] }} XP</span>
            </div>
        </div>
        @foreach ($sec['items'] as $task)
            @include('tasks._task_item', ['task' => $task])
        @endforeach
    @endif
@endforeach

@if ($futureTasks->isNotEmpty())
    <div class="mt-5 mb-3">
        <div class="d-flex align-items-center">
            <div>
                <h4 class="mb-1">Futuras</h4>
                <div class="d-flex gap-2">
                    <span class="badge bg-light">{{ $futureTasks->count() }} tareas</span>
                    <span class="badge-soft-info">{{ $futureXP }} XP</span>
                </div>
            </div>
            <button class="btn btn-sm btn-outline-primary ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#futureList" aria-expanded="false" aria-controls="futureList">
                Ver/Ocultar
            </button>
        </div>
    </div>
    <div id="futureList" class="collapse">
        @foreach ($futureTasks as $task)
            @include('tasks._task_item', ['task' => $task])
        @endforeach
    </div>
@endif
