@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body text-center py-5">
                <h1 class="mb-3">Nueva Tarea</h1>
                <p class="text-muted mb-4">Usa el botón para crear una tarea rápidamente.</p>
                <button class="btn btn-primary btn-lg"
                        data-bs-toggle="modal" data-bs-target="#modalCreateTask">
                    + Agregar tarea
                </button>
                <div class="mt-3">
                    <a href="{{ route('tasks.index') }}" class="btn btn-link">Ver todas las tareas</a>
                </div>
            </div>
        </div>
    </div>
</div>

@include('tasks._create_modal')
@endsection