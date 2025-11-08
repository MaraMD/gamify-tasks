@extends('layouts.app')

@section('content')
<h1>Todas mis tareas</h1>

{{-- Crear nueva tarea --}}
<form method="POST" action="{{ route('tasks.store') }}" style="margin:16px 0; padding:12px; border:1px solid #ccc;">
  @csrf
  <strong>Nueva tarea</strong><br>
  <input name="title" placeholder="Título" required style="min-width:260px">
  <input name="description" placeholder="Descripción opc.">
  <select name="difficulty" required>
    <option value="1">Fácil (≈5)</option>
    <option value="2">Media (≈10)</option>
    <option value="3">Difícil (≈20)</option>
  </select>
  <input type="number" name="points" value="5" min="1" required>
  <input type="date" name="due_date" value="{{ now()->toDateString() }}" required>
  <button type="submit">Agregar</button>
</form>

<table border="1" cellpadding="6" cellspacing="0">
  <thead>
    <tr>
      <th>Título</th>
      <th>Dificultad</th>
      <th>Puntos</th>
      <th>Fecha</th>
      <th>Status</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    @forelse($tasks as $t)
      <tr>
        <td>{{ $t->title }}</td>
        <td>{{ $t->difficulty }}</td>
        <td>{{ $t->points }}</td>
        <td>{{ $t->due_date->format('Y-m-d') }}</td>
        <td>{{ $t->status ? 'Completada' : 'Pendiente' }}</td>
        <td style="white-space:nowrap">
          <a href="{{ route('tasks.edit', $t) }}">Editar</a>

          <form method="POST" action="{{ route('tasks.destroy', $t) }}" style="display:inline"
                onsubmit="return confirm('¿Eliminar tarea?');">
            @csrf @method('DELETE')
            <button type="submit">Eliminar</button>
          </form>

          @if($t->status == 0)
          <form method="POST" action="{{ route('tasks.complete', $t) }}" style="display:inline">
            @csrf
            <button type="submit">Completar</button>
          </form>
          @endif
        </td>
      </tr>
    @empty
      <tr><td colspan="6">Sin tareas.</td></tr>
    @endforelse
  </tbody>
</table>

<div style="margin-top:12px;">
  {{ $tasks->links() }}
</div>
@endsection
