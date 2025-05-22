<x-app-layout>
<div class="min-h-screen bg-gray-50 py-8 px-6">
    <div class="container mx-auto">
        <h1 class="text-4xl font-extrabold mb-8 text-center text-gray-800">
            Solicitudes de Todos tus Grupos
        </h1>

        @if($solicitudes->isEmpty())
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6" role="alert">
                <p>No hay solicitudes registradas.</p>
            </div>
        @else
            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-2xl font-semibold mb-4 text-gray-800">Listado de Solicitudes por Prioridad</h2>

                <table class="w-full table-auto text-sm">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="p-3 text-left"># Radicado</th>
                            <th class="p-3 text-left">Asunto</th>
                            <th class="p-3 text-left">Tipo</th>
                            <th class="p-3 text-left">Estado</th>
                            <th class="p-3 text-left">Grupo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($solicitudes as $solicitud)
                            @php
                                $estadoNombre = $solicitud->estado->nombre ?? '';
                                $estadoColor = match($estadoNombre) {
                                    'Recibida' => 'border-l-8 border-blue-400 bg-blue-50',
                                    'En Revisión' => 'border-l-8 border-orange-400 bg-orange-50',
                                    'Por Vencer' => 'border-l-8 border-red-600 bg-red-100',
                                    'Respondida' => 'border-l-8 border-yellow-400 bg-yellow-50',
                                    'Cerrada' => 'border-l-8 border-green-600 bg-green-100',
                                    default => 'border-l-8 border-gray-300 bg-white',
                                };
                            @endphp

                            <tr onclick="window.location='{{ route('grupos.solicitudes.index', $solicitud->grupo_id) }}'"
                                class="{{ $estadoColor }} hover:bg-opacity-80 transition-shadow shadow-sm hover:shadow-md cursor-pointer">
                                <td class="p-4 font-semibold text-gray-900">{{ $solicitud->numero_radicado }}</td>
                                <td class="p-4 text-gray-800">{{ $solicitud->asunto }}</td>
                                <td class="p-4 text-gray-700 italic">{{ $solicitud->tipoSolicitud->nombre ?? 'No definido' }}</td>
                                <td class="p-4 font-semibold text-gray-900">{{ $solicitud->estado->nombre ?? 'Sin estado' }}</td>
                                <td class="p-4 text-gray-700">{{ $solicitud->grupo->nombre ?? 'Grupo desconocido' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
</x-app-layout>
