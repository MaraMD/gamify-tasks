<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light dark">
    <title>Gamificación - Tareas</title>

    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Theme -->
    <link href="{{ asset('theme/theme.css') }}" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ auth()->check() ? route('tasks.today') : route('login') }}">Gamificación</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('tasks.today') }}">Tareas de Hoy</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('tasks.completedWeek') }}">Completadas (semana)</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('character.show') }}">Mi Personaje</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('tasks.index') }}">Todas las Tareas</a>
                        </li>
                        @if(auth()->user()->is_admin ?? false)
                            <li class="nav-item">
                                <a class="nav-link text-warning fw-bold" href="{{ route('admin.dashboard') }}">
                                    Panel Admin
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>

                <ul class="navbar-nav ms-auto">
                    <li class="nav-item me-2">
                        <button id="themeToggle" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2"
                                type="button" aria-pressed="false" aria-label="Cambiar tema" title="Cambiar tema">
                            <span class="theme-icon" aria-hidden="true">
                                <svg class="icon-moon" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M21 12.79A9 9 0 0 1 11.21 3 7 7 0 1 0 21 12.79z"/>
                                </svg>
                                <svg class="icon-sun d-none" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 1 1-8 0 4 4 0 0 1 8 0z"/>
                                </svg>
                            </span>
                            <span class="d-none d-md-inline">Tema</span>
                        </button>
                    </li>
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Iniciar Sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Registro</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Salir</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-4">
        @include('partials.alerts')

        @yield('content')
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Theme Toggle Script -->
    <script>
    (function(){
        const root = document.documentElement;
        const storageKey = 'theme';

        // 1) Establecer tema al cargar
        const saved = localStorage.getItem(storageKey);
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const theme = saved || (prefersDark ? 'dark' : 'light');
        if(theme === 'dark') root.setAttribute('data-theme','dark');
        else root.removeAttribute('data-theme');

        // 2) Toggle button
        const btn = document.getElementById('themeToggle');
        if(btn){
            const syncIcon = () => {
                const isDark = root.hasAttribute('data-theme');
                btn.setAttribute('aria-pressed', String(isDark));
                btn.querySelector('.icon-moon')?.classList.toggle('d-none', isDark);
                btn.querySelector('.icon-sun')?.classList.toggle('d-none', !isDark);
            };
            syncIcon();
            btn.addEventListener('click', () => {
                const isDark = root.hasAttribute('data-theme');
                if(isDark){
                    root.removeAttribute('data-theme');
                    localStorage.setItem(storageKey,'light');
                } else {
                    root.setAttribute('data-theme','dark');
                    localStorage.setItem(storageKey,'dark');
                }
                syncIcon();
            });
        }

        // 3) Si el usuario no ha elegido, y cambia preferencia del SO
        if(!saved){
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e=>{
                if(localStorage.getItem(storageKey)) return; // respetar elección si ya existe
                if(e.matches) root.setAttribute('data-theme','dark');
                else root.removeAttribute('data-theme');
            });
        }
    })();
    </script>
</body>
</html>
