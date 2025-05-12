<x-app-layout>

    <div class="flex justify-end p-6">
        <a href="{{ route('grupos.solicitudes.create', $grupo) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            Nueva Solicitud
        </a>
    </div>

    {{-- Filtros --}}
    <form method="GET" action="{{ route('grupos.solicitudes.index', $grupo) }}" class="flex flex-wrap gap-4 mb-6">
        <select name="tipo" class="border rounded px-3 py-2">
            <option value="">-- Tipo de Solicitud --</option>
            @foreach($tipos as $tipo)
                <option value="{{ $tipo->id }}" {{ request('tipo') == $tipo->id ? 'selected' : '' }}>
                    {{ $tipo->nombre }}
                </option>
            @endforeach
        </select>

        <select name="estado" class="border rounded px-3 py-2">
            <option value="">-- Estado --</option>
            @foreach($estados as $estado)
                <option value="{{ $estado->id }}" {{ request('estado') == $estado->id ? 'selected' : '' }}>
                    {{ $estado->nombre }}
                </option>
            @endforeach
        </select>

        <input type="date" name="fecha" value="{{ request('fecha') }}" class="border rounded px-3 py-2" />

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Filtrar</button>
        <a href="{{ route('grupos.solicitudes.index', $grupo) }}" class="text-gray-600 hover:underline">Limpiar</a>
    </form>

    {{-- Ordenamiento --}}
    <form method="GET" action="{{ route('grupos.solicitudes.index', $grupo) }}" id="ordenForm" class="mb-6">
        <input type="hidden" name="tipo" value="{{ request('tipo') }}">
        <input type="hidden" name="estado" value="{{ request('estado') }}">
        <input type="hidden" name="fecha" value="{{ request('fecha') }}">

        <select name="orden" class="border rounded px-3 py-2" onchange="document.getElementById('ordenForm').submit()">
            <option value="">-- Ordenar por fecha --</option>
            <option value="recientes" {{ request('orden') == 'recientes' ? 'selected' : '' }}>Más recientes</option>
            <option value="antiguos" {{ request('orden') == 'antiguos' ? 'selected' : '' }}>Más antiguos</option>
        </select>
    </form>

    <div class="p-6 bg-white rounded-xl shadow">
        <h2 class="text-2xl font-semibold mb-4">Listado de Solicitudes</h2>

        <table class="w-full table-auto border rounded-md text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 text-left">Número de Radicado</th>
                    <th class="p-2 text-left">Asunto</th>
                    <th class="p-2 text-left">Tipo</th>
                    <th class="p-2 text-left">Estado</th>
                    <th class="p-2 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($solicitudes as $solicitud)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-2">{{ $solicitud->numero_radicado }}</td>
                        <td class="p-2">{{ $solicitud->asunto }}</td>
                        <td class="p-2">{{ $solicitud->tipoSolicitud->nombre ?? 'No definido' }}</td>
                        <td class="p-2">{{ $solicitud->estado->nombre ?? 'Sin estado' }}</td>
                        <td class="p-2 space-x-2">
                            <a href="{{ route('grupos.solicitudes.edit', [$grupo, $solicitud]) }}" class="text-indigo-600 hover:underline">Editar</a>
                            <form action="{{ route('grupos.solicitudes.destroy', [$grupo, $solicitud]) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline" onclick="return confirm('¿Estás seguro de que deseas eliminar esta solicitud?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-gray-500">No hay solicitudes registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
