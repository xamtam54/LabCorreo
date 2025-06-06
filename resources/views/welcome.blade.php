<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>SIGES - Laravel</title>

    @vite(['resources/css/app.css', 'resources/js/app.js']) {{-- Asegúrate de tener esto en tu layout --}}
</head>
<body class="bg-gray-900 text-white min-h-screen flex flex-col relative font-sans">

    {{-- Botón de Login / Dashboard arriba a la derecha --}}
    @if (Route::has('login'))
        <div class="absolute top-4 right-4 z-10">
            @auth
                <a href="{{ url('/solicitudes/overview') }}"
                   class="px-4 py-2 rounded-md bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-md transition">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="px-4 py-2 rounded-md bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-md transition">
                    Login
                </a>
            @endauth
        </div>
    @endif

    {{-- Contenido central --}}
    <main class="flex-1 flex items-center justify-center text-center px-4">
        <div>
            <h1 class="text-6xl md:text-7xl font-bold text-white mb-4">SIGES</h1>
            <p class="text-lg md:text-xl text-gray-400 font-light">Sistema Integral de Gestión de Solicitudes</p>
        </div>
    </main>

    {{-- Footer opcional --}}
    <footer class="text-center py-4 text-sm text-gray-500">
        © {{ date('Y') }} - Desarrollado con Laravel
    </footer>

</body>
</html>
