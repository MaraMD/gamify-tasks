@extends('layouts.plain')
@section('content')
<h1>Tareas pendientes de hoy</h1>
@if($tasks->isEmpty())
    <p>Nada pendiente. 🔥</p>
@else
    <ul>
        @foreach($tasks as $t)
            <li>
                <strong>{{ $t->title }}</strong>
                (dif: {{ $t->difficulty }}, pts: {{ $t->points }})
                — vence: {{ $t->due_date->format('Y-m-d') }}
            </li>
        @endforeach
    </ul>
@endif
@endsection