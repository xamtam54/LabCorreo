<x-app-layout>
    <div class="min-h-screen bg-gray-50 py-8 px-6">
        <div class="container mx-auto">
            {{-- Título --}}
            <div class="flex justify-start mb-4">
                <x-button-back href="{{ route('grupos.unirse') }}" />
            </div>

            <h1 class="text-4xl font-extrabold mb-8 text-center text-gray-800">
                Solicitudes del Grupo <span class="text-blue-700">{{ $grupo->nombre }}</span>
            </h1>

            {{-- Sección superior: botón + filtros + orden --}}
            <div class="bg-white rounded-xl shadow p-6 mb-6">
                <div class="flex flex-wrap items-end justify-between gap-6">

                    {{-- Botón de nueva solicitud --}}
                    <div>
                        <x-blue-button text="Nueva Solicitud"
                            onclick="window.location='{{ route('grupos.solicitudes.create', $grupo) }}'" />
                    </div>

                    {{-- Filtros --}}
                    <form method="GET" action="{{ route('grupos.solicitudes.index', $grupo) }}"
                        class="flex flex-wrap items-end gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Solicitud</label>
                            <select name="tipo" class="border rounded px-3 py-2 w-44">
                                <option value="">-- Todos --</option>
                                @foreach($tipos as $tipo)
                                    <option value="{{ $tipo->id }}" {{ request('tipo') == $tipo->id ? 'selected' : '' }}>
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                            <select name="estado" class="border rounded px-3 py-2 w-44">
                                <option value="">-- Todos --</option>
                                @foreach($estados as $estado)
                                    <option value="{{ $estado->id }}" {{ request('estado') == $estado->id ? 'selected' : '' }}>
                                        {{ $estado->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                            <input type="date" name="fecha" value="{{ request('fecha') }}"
                                class="border rounded px-3 py-2 w-44" />
                        </div>

                        <div class="flex gap-2 mt-4">
                            <button type="submit"
                                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                Filtrar
                            </button>
                            <a href="{{ route('grupos.solicitudes.index', $grupo) }}"
                                class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300">
                                Limpiar
                            </a>
                        </div>
                    </form>

                    {{-- Ordenar por --}}
                    <form method="GET" action="{{ route('grupos.solicitudes.index', $grupo) }}"
                        id="ordenForm" class="flex items-end">
                        <input type="hidden" name="tipo" value="{{ request('tipo') }}">
                        <input type="hidden" name="estado" value="{{ request('estado') }}">
                        <input type="hidden" name="fecha" value="{{ request('fecha') }}">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ordenar por</label>
                            <select name="orden" class="border rounded px-3 py-2 w-44"
                                onchange="document.getElementById('ordenForm').submit()">
                                <option value="">-- Fecha --</option>
                                <option value="recientes" {{ request('orden') == 'recientes' ? 'selected' : '' }}>Más recientes</option>
                                <option value="antiguos" {{ request('orden') == 'antiguos' ? 'selected' : '' }}>Más antiguos</option>
                                <option value="prioridad" {{ request('orden') == 'prioridad' ? 'selected' : '' }}>Prioridad</option>
                            </select>
                        </div>

                    </form>

                </div>
            </div>

            {{-- Tabla de solicitudes --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-2xl font-semibold mb-4 text-gray-800">Listado de Solicitudes</h2>

                <table class="w-full table-auto text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="p-3 text-left"># Radicado</th>
                            <th class="p-3 text-left">Asunto</th>
                            <th class="p-3 text-left">Tipo</th>
                            <th class="p-3 text-left">Estado</th>
                            <th class="p-3 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($solicitudes as $solicitud)
                            @php
                                $estadoNombre = $solicitud->estado->nombre ?? '';
                                $estadoDescripcion = $solicitud->estado->descripcion ?? '';

                                $estadoColor = match($estadoNombre) {
                                    'Nueva' => 'border-l-8 border-blue-400 bg-blue-100',
                                    'En Revisión' => 'border-l-8 border-orange-400 bg-orange-100',
                                    'Por Vencer' => 'border-l-8 border-red-600 bg-red-200',
                                    'Expirada' => 'border-l-8 border-gray-600 bg-gray-200',
                                    'Respondida' => 'border-l-8 border-yellow-400 bg-yellow-100',
                                    'Cerrada' => 'border-l-8 border-green-600 bg-green-200',
                                    default => 'border-l-8 border-gray-300 bg-white',
                                };
                            @endphp

                            <tr class="{{ $estadoColor }} hover:bg-opacity-80 transition-shadow shadow-sm hover:shadow-md">
                                <td class="p-4 font-semibold text-gray-900">{{ $solicitud->numero_radicado }}</td>
                                <td class="p-4 text-gray-800">{{ $solicitud->asunto }}</td>
                                <td class="p-4 text-gray-700 italic">{{ $solicitud->tipoSolicitud->nombre ?? 'No definido' }}</td>
                                <td class="p-4 font-semibold text-gray-900" title="{{ $estadoDescripcion }}">
                                    {{ $estadoNombre ?? 'Sin estado' }}
                                </td>
                                <td class="p-4 space-x-3">
                                    <x-button-edit :href="route('grupos.solicitudes.edit', [$grupo, $solicitud])" />

                                    <!-- Botón Ver -->
                                    <x-eye-button
                                        text="Ver"
                                        size="sm"
                                        onclick="window.location='{{ route('grupos.solicitudes.show', [$grupo, $solicitud]) }}'"
                                    />

                                    <form action="{{ route('grupos.solicitudes.destroy', [$grupo, $solicitud]) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <x-button-delete onclick="return confirm('¿Estás seguro de que deseas eliminar esta solicitud?')" />
                                    </form>

                                   @if (!$solicitud->completada)
                                        <form action="{{ route('grupos.solicitudes.completar', [$grupo, $solicitud]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="submit"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                                onclick="return confirm('¿Estás seguro de marcar esta solicitud como completada?')"
                                            >
                                                Completar
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('grupos.solicitudes.revertir', [$grupo, $solicitud]) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="submit"
                                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                                onclick="return confirm('¿Estás seguro de revertir la marca de completada?')"
                                            >
                                                Revertir
                                            </button>
                                        </form>
                                    @endif

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-6 text-center text-gray-500 italic">No hay solicitudes registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
