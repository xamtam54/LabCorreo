<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Laravel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <style>
        /* Contenedor para posicionar el login */
        .login-container {
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1000;
        }

        .login-link {
            font-family: 'instrument-sans', sans-serif;
            font-weight: 500;
            text-decoration: none;
            color: #1a202c; /* color gris oscuro */
            padding: 0.5rem 1rem;
            border: 1px solid transparent;
            border-radius: 0.375rem;
            transition: all 0.3s ease;
        }

        .login-link:hover {
            background-color: #4c51bf; /* morado */
            color: white;
            border-color: #4c51bf;
        }
    </style>
</head>
<body>
    @if (Route::has('login'))
        <div class="login-container">
            @auth
                <a href="{{ url('/dashboard') }}" class="login-link">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="login-link">Login</a>
            @endauth
        </div>
    @endif

    <!-- Aquí puedes poner el contenido principal de la página -->

</body>
</html>
