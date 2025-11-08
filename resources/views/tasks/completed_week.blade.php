@extends('layouts.app')

@section('content')
<h1>Tareas completadas esta semana</h1>

@php($totalXp = $completions->sum('points_awarded'))

@if($completions->isEmpty())
  <p>Aún sin victorias. 💪</p>
@else
  <p><strong>Total XP ganado:</strong> {{ $totalXp }}</p>
  <table border="1" cellpadding="6" cellspacing="0">
    <thead>
      <tr>
        <th>Fecha</th>
        <th>Título</th>
        <th>Dificultad</th>
        <th>Puntos</th>
      </tr>
    </thead>
    <tbody>
      @foreach($completions as $c)
        <tr>
          <td>{{ $c->completed_at?->format('Y-m-d H:i') }}</td>
          <td>{{ $c->task?->title ?? '—' }}</td>
          <td>{{ $c->task?->difficulty ?? '—' }}</td>
          <td>{{ $c->points_awarded }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endif
@endsection
