<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Gamify Tasks</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body{font-family:system-ui,Segoe UI,Roboto,Arial;margin:24px}
    nav a{margin-right:12px}
    .ok{color:#067;}
    .error{color:#a00;}
  </style>
</head>
<body>
  <nav>
    <a href="{{ route('character.show') }}">Mi Personaje</a>
    <a href="{{ route('tasks.today') }}">Tareas de Hoy</a>
    <a href="{{ route('tasks.completedWeek') }}">Completadas (semana)</a>
    <a href="{{ route('tasks.index') }}">Todas</a>
  </nav>
  <hr>
  @if(session('status')) <p class="ok">{{ session('status') }}</p> @endif
  @if($errors->any()) <p class="error">{{ $errors->first() }}</p> @endif

  @yield('content')
</body>
</html>
