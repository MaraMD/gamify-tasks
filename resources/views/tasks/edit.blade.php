@extends('layouts.app')

@section('content')
<h1>Editar tarea</h1>

<form method="POST" action="{{ route('tasks.update', $task) }}" style="max-width:640px">
  @csrf @method('PATCH')

  <label>Título</label><br>
  <input name="title" value="{{ old('title',$task->title) }}" required style="min-width:260px"><br><br>

  <label>Descripción</label><br>
  <input name="description" value="{{ old('description',$task->description) }}"><br><br>

  <label>Dificultad</label><br>
  <select name="difficulty" required>
    @foreach([1=>'Fácil',2=>'Media',3=>'Difícil'] as $val=>$txt)
      <option value="{{ $val }}" @selected(old('difficulty',$task->difficulty)==$val)>{{ $txt }}</option>
    @endforeach
  </select><br><br>

  <label>Puntos</label><br>
  <input type="number" name="points" value="{{ old('points',$task->points) }}" min="1" required><br><br>

  <label>Fecha</label><br>
  <input type="date" name="due_date" value="{{ old('due_date',$task->due_date->format('Y-m-d')) }}" required><br><br>

  <label>Status</label><br>
  <select name="status" required>
    <option value="0" @selected(old('status',$task->status)==0)>Pendiente</option>
    <option value="1" @selected(old('status',$task->status)==1)>Completada</option>
  </select><br><br>

  <button type="submit">Guardar cambios</button>
  <a href="{{ route('tasks.index') }}">Cancelar</a>
</form>
@endsection
