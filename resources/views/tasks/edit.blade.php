@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="mb-4">
            <h2 class="mb-1">Editar Tarea</h2>
            <p class="text-muted mb-0">Modifica los detalles de tu tarea</p>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('tasks.update', $task) }}">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="title" class="form-label">Título</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror"
                               id="title" name="title" value="{{ old('title', $task->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3">{{ old('description', $task->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="difficulty" class="form-label">Dificultad</label>
                            <select class="form-select @error('difficulty') is-invalid @enderror"
                                    id="difficulty" name="difficulty" required>
                                @foreach([1 => 'Fácil', 2 => 'Media', 3 => 'Difícil'] as $val => $txt)
                                    <option value="{{ $val }}" @selected(old('difficulty', $task->difficulty) == $val)>
                                        {{ $txt }}
                                    </option>
                                @endforeach
                            </select>
                            @error('difficulty')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="points" class="form-label">Puntos</label>
                            <input type="number" class="form-control @error('points') is-invalid @enderror"
                                   id="points" name="points" value="{{ old('points', $task->points) }}"
                                   min="1" required>
                            @error('points')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="due_date" class="form-label">Fecha límite</label>
                            <input type="date" class="form-control @error('due_date') is-invalid @enderror"
                                   id="due_date" name="due_date"
                                   value="{{ old('due_date', $task->due_date->format('Y-m-d')) }}" required>
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Estado</label>
                        <select class="form-select @error('status') is-invalid @enderror"
                                id="status" name="status" required>
                            <option value="0" @selected(old('status', $task->status) == 0)>Pendiente</option>
                            <option value="1" @selected(old('status', $task->status) == 1)>Completada</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
