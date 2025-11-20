<x-app-layout>
    <div class="max-w-4xl mx-auto mt-10 p-8 bg-white rounded-2xl shadow-lg">
        <div class="flex items-center justify-between mb-8 border-b border-gray-200 pb-4">
            <h2 class="text-3xl font-extrabold text-gray-900">
                Solicitud #{{ $solicitud->numero_radicado }}
            </h2>
            <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $solicitud->estado->id == 1 ? 'bg-green-100 text-green-800' : ($solicitud->estado->id == 2 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                {{ $solicitud->estado->nombre ?? 'N/A' }}
            </span>
        </div>

        @php
            // Explota el radicado por guiones
            $radicadoParts = explode('-', $solicitud->numero_radicado);

            // Tipo de radicación: posición 4 (índice 3)
            $tipo = $radicadoParts[3] ?? '';

            // Dependencia: posición 5 (índice 4)
            $dependenciaCodigo = $radicadoParts[4] ?? '';

            // Traducir tipo a texto
            $tipoTexto = match($tipo) {
                'E' => 'Entrada',
                'S' => 'Salida',
                'I' => 'Interna',
                default => $tipo
            };

            // Traducir dependencia a texto
            $dependencias = [
                'SECGEN' => 'Secretaría General',
                'SECHAC' => 'Secretaría de Hacienda',
                'SECPLA' => 'Secretaría de Planeación',
                'SECOPS' => 'Secretaría de Obras Públicas',
                'SECAG'  => 'Secretaría de Agricultura',
                'DESP'   => 'Despacho del Alcalde',
                'COMSOC' => 'Comunicaciones Sociales',
            ];

            $dependenciaTexto = $dependencias[$dependenciaCodigo] ?? $dependenciaCodigo;
        @endphp

        {{-- Información General --}}
        <section class="space-y-6 mb-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Columna Izquierda --}}
                <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Tipo de Radicación</p>
                        <p class="font-semibold text-gray-900">{{ $tipoTexto }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Dependencia</p>
                        <p class="font-semibold text-gray-900">{{ $dependenciaTexto }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Fecha de Ingreso</p>
                        <p class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($solicitud->fecha_ingreso)->format('d/m/Y') }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Fecha de Vencimiento</p>
                        <p class="font-semibold text-gray-900">
                            {{ $solicitud->fecha_vencimiento ? \Carbon\Carbon::parse($solicitud->fecha_vencimiento)->format('d/m/Y') : 'No definido' }}
                        </p>
                    </div>
                </div>

                {{-- Columna Derecha --}}
                <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Tipo de Solicitud</p>
                        <p class="font-semibold text-gray-900">{{ $solicitud->tipoSolicitud->nombre ?? 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Medio de Recepción</p>
                        <p class="font-semibold text-gray-900">{{ $solicitud->medioRecepcion->nombre ?? 'N/A' }}</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Estado</p>
                        <p class="font-semibold {{ $solicitud->completada ? 'text-green-600' : 'text-amber-600' }}">
                            {{ $solicitud->completada ? 'Completada' : 'Pendiente' }}
                        </p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Firma Digital</p>
                        <p class="font-semibold text-gray-900">{{ $solicitud->firma_digital ? 'Sí' : 'No' }}</p>
                    </div>
                </div>
            </div>

            {{-- Información del Remitente --}}
            <div class="border-t pt-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Información del Remitente
                </h3>

                @if($solicitud->es_anonimo)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-yellow-800">Remitente Anónimo</p>
                                <p class="text-sm text-yellow-700 mt-1">No se registró información del remitente para esta solicitud.</p>
                            </div>
                        </div>
                    </div>
                @elseif($solicitud->remitente)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-blue-600 font-medium">Nombre Completo</p>
                                <p class="font-semibold text-gray-900">{{ $solicitud->remitente->nombre }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-blue-600 font-medium">Tipo de Remitente</p>
                                <p class="font-semibold text-gray-900">{{ $solicitud->tipoRemitente->nombre ?? 'N/A' }}</p>
                            </div>

                            @if($solicitud->remitente->numero_documento)
                            <div>
                                <p class="text-sm text-blue-600 font-medium">Documento</p>
                                <p class="font-semibold text-gray-900">
                                    {{ $solicitud->remitente->tipoDocumento->nombre ?? '' }}
                                    {{ $solicitud->remitente->numero_documento }}
                                </p>
                            </div>
                            @endif

                            @if($solicitud->remitente->telefono)
                            <div>
                                <p class="text-sm text-blue-600 font-medium">Teléfono</p>
                                <p class="font-semibold text-gray-900">{{ $solicitud->remitente->telefono }}</p>
                            </div>
                            @endif

                            @if($solicitud->remitente->correo)
                            <div class="md:col-span-2">
                                <p class="text-sm text-blue-600 font-medium">Correo Electrónico</p>
                                <p class="font-semibold text-gray-900">{{ $solicitud->remitente->correo }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                @else
                    <p class="text-gray-500 italic">Sin información del remitente</p>
                @endif
            </div>

            {{-- Asunto --}}
            @if($solicitud->asunto)
            <div class="border-t pt-6">
                <h3 class="text-xl font-bold text-gray-900 mb-3">Asunto</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-800">{{ $solicitud->asunto }}</p>
                </div>
            </div>
            @endif

            {{-- Contenido --}}
            @if($solicitud->contenido)
            <div class="border-t pt-6">
                <h3 class="text-xl font-bold text-gray-900 mb-3">Contenido</h3>
                <div class="whitespace-pre-wrap border border-gray-300 p-5 rounded-lg bg-gray-50 text-gray-800 shadow-inner max-h-96 overflow-y-auto">
                    {{ $solicitud->contenido }}
                </div>
            </div>
            @endif
        </section>

        {{-- Documentos Adjuntos --}}
        <section class="border-t pt-6">
            <h3 class="text-2xl font-bold text-gray-900 mb-5 flex items-center">
                <svg class="w-7 h-7 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Documentos Adjuntos
                @if($solicitud->documentos->count() > 0)
                    <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">
                        {{ $solicitud->documentos->count() }}
                    </span>
                @endif
            </h3>

            @if($solicitud->documentos->count() > 0)
                <div class="grid grid-cols-1 gap-4">
                    @foreach($solicitud->documentos as $index => $documento)
                        <div class="bg-white border-2 border-gray-200 hover:border-blue-300 rounded-lg p-5 shadow-sm hover:shadow-md transition-all duration-200">
                            <div class="flex items-center justify-between">
                                {{-- Información del documento --}}
                                <div class="flex items-center space-x-4 flex-1">
                                    <div class="bg-blue-100 p-3 rounded-lg">
                                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-semibold bg-gray-200 text-gray-700 px-2 py-1 rounded">
                                                #{{ $index + 1 }}
                                            </span>
                                            <a href="{{ route('grupos.solicitudes.documento.ver', ['grupo' => $solicitud->grupo_id, 'documento' => $documento->id]) }}"
                                               target="_blank"
                                               class="text-blue-700 hover:text-blue-900 hover:underline font-semibold text-lg truncate">
                                                {{ $documento->nombre_archivo }}
                                            </a>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1">
                                            Tamaño: {{ number_format($documento->tamano_mb, 2) }} MB
                                        </p>
                                    </div>
                                </div>

                                {{-- Acciones --}}
                                <div class="flex items-center space-x-3 ml-4">
                                    <a href="{{ route('grupos.solicitudes.documento.ver', ['grupo' => $solicitud->grupo_id, 'documento' => $documento->id]) }}"
                                       target="_blank"
                                       class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition text-sm font-medium">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Ver
                                    </a>

                                    <a href="{{ route('grupos.solicitudes.documento.descargar', ['grupo' => $solicitud->grupo_id, 'documento' => $documento->id]) }}"
                                       class="inline-flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition text-sm font-medium"
                                       download>
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        Descargar
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Resumen de documentos --}}
                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <strong>Total de documentos:</strong> {{ $solicitud->documentos->count() }}
                        <span class="mx-2">•</span>
                        <strong>Tamaño total:</strong> {{ number_format($solicitud->documentos->sum('tamano_mb'), 2) }} MB
                    </p>
                </div>
            @else
                <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="mt-4 text-gray-600 font-medium">No hay documentos adjuntos para esta solicitud</p>
                    <p class="text-sm text-gray-500 mt-1">Los documentos adjuntos aparecerán aquí cuando se agreguen</p>
                </div>
            @endif
        </section>

        {{-- Botones de acción --}}
        <div class="pt-8 flex items-center justify-between border-t">
            <x-gray-button text="Volver al listado" :href="route('grupos.solicitudes.index', $solicitud->grupo_id)" />

            <div class="space-x-2">
                <a href="{{ route('grupos.solicitudes.edit', ['grupo' => $grupo->id, 'solicitud' => $solicitud->id]) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar Solicitud
                </a>
            </div>
        </div>
    </div>
</x-app-layout>

