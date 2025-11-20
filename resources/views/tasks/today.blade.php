@extends('layouts.app')

@section('content')
@php
    $user = auth()->user();
    [$greetTitle, $greetSubtitle] = app(\App\Services\GreetingService::class)->make($user, $character);
@endphp

<style>
    .avatar-sm {
        max-width: 96px;
    }
</style>

<div class="row justify-content-center">
    <div class="col-md-10">
        {{-- Header with compact avatar --}}
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start gap-3">
                    {{-- Avatar --}}
                    <div class="flex-shrink-0">
                        @if($character)
                            <x-avatar
                                :character="$character"
                                :size="96"
                                class="avatar-sm rounded-3 shadow-sm"
                            />
                        @else
                            <div class="avatar-sm d-flex align-items-center justify-content-center bg-light rounded-circle" style="width: 96px; height: 96px;">
                                <span style="font-size: 48px;">👤</span>
                            </div>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-grow-1 text-center text-md-start">
                        <h1 class="display-5 fw-bold mb-2">{{ $greetTitle }}</h1>
                        <p class="text-muted mb-3" style="font-size: 1.05rem;">{{ $greetSubtitle }}</p>
                        <div class="d-flex gap-2 justify-content-center justify-content-md-start flex-wrap align-items-center">
                            <span class="badge-soft-primary">{{ $tasks->count() }} pendientes hoy</span>
                            @if($character)
                                <span class="badge-soft-success">Nivel {{ $character->level }}</span>
                                <span class="badge-soft-info">{{ $character->xp }} XP</span>
                            @endif
                            <button class="btn btn-primary btn-sm ms-md-auto"
                                    data-bs-toggle="modal" data-bs-target="#modalCreateTask">
                                + Agregar tarea
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tasks list --}}
        @if($tasks->isEmpty())
            <div class="alert alert-info text-center" role="alert">
                <h5 class="alert-heading mb-3">¡Todo completado! 🔥</h5>
                <p class="mb-3">No tienes tareas pendientes para hoy.</p>
                <button class="btn btn-primary btn-lg"
                        data-bs-toggle="modal" data-bs-target="#modalCreateTask"
                        aria-label="Agregar nueva tarea para hoy">
                    + Agregar tarea
                </button>
            </div>
        @else
            <div class="list-group">
                @foreach($tasks as $t)
                    <div class="list-group-item">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-2">{{ $t->title }}</h5>
                                @if($t->description)
                                    <p class="mb-2 text-muted">{{ $t->description }}</p>
                                @endif
                                <div>
                                    @php
                                        $difficultyBadges = [
                                            1 => ['class' => 'badge-soft-success', 'text' => 'Fácil'],
                                            2 => ['class' => 'badge-soft-warning', 'text' => 'Media'],
                                            3 => ['class' => 'badge-soft-danger', 'text' => 'Difícil'],
                                        ];
                                        $badge = $difficultyBadges[$t->difficulty] ?? ['class' => 'badge bg-secondary', 'text' => 'Nivel ' . $t->difficulty];
                                    @endphp
                                    <span class="{{ $badge['class'] }}">{{ $badge['text'] }}</span>
                                    <span class="badge-soft-primary">{{ $t->points }} puntos</span>
                                    <span class="badge bg-secondary">Vence: {{ $t->due_date->format('d/m/Y') }}</span>
                                </div>
                            </div>
                            <div>
                                <form method="POST" action="{{ route('tasks.complete', $t) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">
                                        Completar
                                    </button>
                                </form>
                                <a href="{{ route('tasks.edit', $t) }}" class="btn btn-outline-secondary btn-sm">
                                    Editar
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-3">
                <p class="text-muted">
                    <strong>Total de tareas:</strong> {{ $tasks->count() }} |
                    <strong>Puntos posibles:</strong> {{ $tasks->sum('points') }} XP
                </p>
            </div>
        @endif

        {{-- Future tasks sections --}}
        @include('tasks._upcoming_sections')
    </div>
</div>

@include('tasks._create_modal')
@endsection