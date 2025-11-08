@extends('layouts.app')

@section('content')
<h1>Personaje</h1>

<ul>
  <li><strong>Nombre:</strong> {{ $character->name }}</li>
  <li><strong>Nivel:</strong> {{ $character->level }}</li>   {{-- <-- aquí pintamos el nivel --}}
  <li><strong>Experiencia:</strong> {{ $character->xp }} XP</li>
  @if($character->avatar)
    <li><strong>Avatar:</strong> {{ $character->avatar }}</li>
    <p><img src="{{ $character->avatar }}" alt="avatar" style="max-width:160px"></p>
  @endif
</ul>

{{-- 
<h3>Editar personaje</h3>
<form method="POST" action="{{ route('character.update') }}">
  @csrf
  <input name="name" value="{{ old('name',$character->name) }}" required placeholder="Nombre">
  <input name="avatar" value="{{ old('avatar',$character->avatar) }}" placeholder="URL del avatar (opcional)">
  <button type="submit">Guardar</button>
</form>
--}}
@endsection
