@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Panel de Correspondencia</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('correos.create') }}" class="bg-white p-5 rounded-2xl shadow-md hover:shadow-lg transition">
            <h2 class="text-xl font-semibold text-purple-600 mb-2">Nuevo Correo</h2>
            <p class="text-gray-500">Registrar una nueva correspondencia entrante o saliente.</p>
        </a>

        <a href="{{ route('correos.index') }}" class="bg-white p-5 rounded-2xl shadow-md hover:shadow-lg transition">
            <h2 class="text-xl font-semibold text-blue-600 mb-2">Ver Correspondencia</h2>
            <p class="text-gray-500">Consulta y gestiona los correos registrados.</p>
        </a>

        <a href="#" class="bg-white p-5 rounded-2xl shadow-md hover:shadow-lg transition">
            <h2 class="text-xl font-semibold text-green-600 mb-2">Reportes</h2>
            <p class="text-gray-500">Visualiza estadísticas y reportes del sistema.</p>
        </a>
    </div>
</div>
@endsection
