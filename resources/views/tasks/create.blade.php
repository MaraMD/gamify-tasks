@extends('layouts.plain')
@section('content')
<h1>Nueva tarea</h1>
<form method="POST" action="{{ route('tasks.store') }}">
    @csrf
    <label>Título <input name="title" value="{{ old('title') }}" required></label><br>
    <label>Descripción <textarea name="description">{{ old('description') }}</textarea></label><br>
    <label>Dificultad <input type="number" name="difficulty" min="1" max="5" value="{{ old('difficulty',1) }}"></label><br>
    <label>Puntos <input type="number" name="points" min="1" value="{{ old('points',10) }}"></label><br>
    <label>Fecha límite <input type="date" name="due_date" value="{{ old('due_date', now()->toDateString()) }}" required></label><br>
    <button type="submit">Guardar</button>
</form>
@endsection