<div class="card mb-3">
    <div class="card-body d-flex justify-content-between align-items-start">
        <div class="flex-grow-1">
            <h5 class="mb-2">{{ $task->title }}</h5>
            @if($task->description)
                <p class="text-muted small mb-2">{{ Illuminate\Support\Str::limit($task->description, 120) }}</p>
            @endif
            <div class="mt-2">
                @php
                    $diffBadge = match($task->difficulty) {
                        3 => ['class' => 'badge-soft-danger', 'text' => 'Difícil'],
                        2 => ['class' => 'badge-soft-warning', 'text' => 'Media'],
                        default => ['class' => 'badge-soft-success', 'text' => 'Fácil'],
                    };
                @endphp
                <span class="{{ $diffBadge['class'] }}">{{ $diffBadge['text'] }}</span>
                <span class="badge-soft-primary ms-1">{{ $task->points }} puntos</span>
                @if($task->due_date)
                    <span class="badge bg-secondary ms-1">Vence: {{ $task->due_date->format('d/m/Y') }}</span>
                @endif
            </div>
        </div>
        <div class="ms-3 d-flex gap-2 flex-shrink-0">
            <form method="POST" action="{{ route('tasks.complete', $task) }}">
                @csrf
                <button type="submit" class="btn btn-success btn-sm">Completar</button>
            </form>
            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-secondary btn-sm">Editar</a>
        </div>
    </div>
</div>
