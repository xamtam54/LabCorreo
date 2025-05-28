<x-app-layout>
    <div class="max-w-3xl mx-auto mt-10 p-8 bg-white rounded-2xl shadow-lg">
        <h2 class="text-3xl font-extrabold text-gray-900 mb-10 border-b border-gray-200 pb-4">
            Detalle de Solicitud #{{ $solicitud->numero_radicado }}
        </h2>

        <section class="space-y-5 mb-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-700">
                <div>
                    <p><span class="font-semibold text-gray-800">Fecha de Ingreso:</span> {{ $solicitud->fecha_ingreso }}</p>
                    <p><span class="font-semibold text-gray-800">Fecha de Vencimiento:</span> {{ $solicitud->fecha_vencimiento ?? 'No definido' }}</p>
                    <p><span class="font-semibold text-gray-800">Estado:</span> {{ $solicitud->estado->nombre ?? 'N/A' }}</p>
                    <p><span class="font-semibold text-gray-800">Tipo de Solicitud:</span> {{ $solicitud->tipoSolicitud->nombre ?? 'N/A' }}</p>
                </div>
                <div>
                    <p><span class="font-semibold text-gray-800">Remitente:</span> {{ $solicitud->remitente }}</p>
                    <p><span class="font-semibold text-gray-800">Asunto:</span> {{ $solicitud->asunto ?? 'Sin asunto' }}</p>
                    <p><span class="font-semibold text-gray-800">Completada:</span> {{ $solicitud->completada ? 'Sí' : 'No' }}</p>
                    <p><span class="font-semibold text-gray-800">Medio de Recepción:</span> {{ $solicitud->medioRecepcion->nombre ?? 'N/A' }}</p>
                    <p><span class="font-semibold text-gray-800">Firma Digital:</span> {{ $solicitud->firma_digital ? 'Sí' : 'No' }}</p>
                </div>
            </div>

            <div>
                <p class="font-semibold text-gray-800 mb-2">Contenido:</p>
                <div class="whitespace-pre-wrap border border-gray-300 p-4 rounded-lg bg-gray-50 text-gray-700 shadow-inner">
                    {{ $solicitud->contenido ?? 'Sin contenido' }}
                </div>
            </div>
        </section>

        <section>
            <h3 class="text-2xl font-semibold text-gray-900 mb-5 border-b border-gray-200 pb-3">Documento Comprobatorio Adjunto</h3>

            @if($solicitud->documento)
                <div class="max-w-md bg-white border border-gray-300 rounded-lg p-5 shadow-sm hover:shadow-md transition-shadow duration-200">
                    <a href="{{ route('grupos.solicitudes.documento.ver', ['grupo' => $solicitud->grupo_id, 'id' => $solicitud->documento->id]) }}"
                       target="_blank"
                       class="text-indigo-600 hover:text-indigo-900 hover:underline font-semibold">
                        {{ $solicitud->documento->nombre_archivo }}
                    </a>
                    <span class="text-gray-400 text-sm mx-2 select-none">•</span>
                    <small class="text-gray-500 text-sm">{{ number_format(($solicitud->documento->tamano_mb ?? 0), 2) }} MB</small>
                    <span class="text-gray-400 text-sm mx-2 select-none">•</span>
                    <a href="{{ route('grupos.solicitudes.documento.descargar', ['grupo' => $solicitud->grupo_id, 'id' => $solicitud->documento->id]) }}"
                       class="text-green-600 hover:text-green-900 hover:underline ml-3 font-semibold"
                       download>Descargar</a>
                </div>
            @else
                <p class="text-gray-500 italic">No hay archivo adjunto para esta solicitud.</p>
            @endif
        </section>

        <div class="pt-8 text-right">
            <x-gray-button text="Volver al listado" :href="route('grupos.solicitudes.index', $solicitud->grupo_id)" />
        </div>
    </div>
</x-app-layout>
