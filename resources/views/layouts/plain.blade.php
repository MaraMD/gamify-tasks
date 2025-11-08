<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gamificación</title>
</head>
<body>
<nav>
    <a href="{{ route('tasks.today') }}">Tareas de hoy</a> |
    <a href="{{ route('tasks.completedWeek') }}">Completadas (semana)</a> |
    <a href="{{ route('character.show') }}">Mi personaje</a> |
    <a href="{{ route('tasks.index') }}">CRUD</a>
</nav>
<hr>
    @if(session('ok'))
        <p style="color:green;">{{ session('ok') }}</p>
    @endif
    @yield('content')
</body>
</html>