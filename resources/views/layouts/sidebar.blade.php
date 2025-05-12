<div class="h-full w-64 bg-white shadow-lg p-4 flex flex-col">
    <div class="text-purple-700 font-bold text-xl mb-6 text-center">Sistema de Correos</div>

    <nav class="flex flex-col gap-4 text-gray-700">
        <a href="{{ route('solicitudes.index') }}" class="hover:bg-purple-100 px-3 py-2 rounded transition">Dashboard</a>
        <a href="{{ route('grupos.unirse') }}" class="hover:bg-purple-100 px-3 py-2 rounded transition">Grupos</a>
        <a href="{{ route('solicitudes.overview') }}" class="hover:bg-purple-100 px-3 py-2 rounded transition">Reportes</a>
    </nav>

</div>
