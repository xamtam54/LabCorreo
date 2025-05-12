<x-app-layout>
    <div class="max-w-3xl mx-auto bg-white p-6 rounded-xl shadow">
        <h2 class="text-2xl font-semibold mb-4">Editar Solicitud</h2>

        <form action="{{ route('grupos.solicitudes.update', ['grupo' => $grupo, 'solicitud' => $solicitud->id]) }}" method="POST">
            @csrf
            @method('PUT')

            <x-text-input name="numero_radicado" label="Número de Radicado"
                          value="{{ old('numero_radicado', $solicitud->numero_radicado) }}" required />

            <x-select-input name="tipo_solicitud_id" label="Tipo de Solicitud"
                            :options="$tiposSolicitud" :selected="old('tipo_solicitud_id', $solicitud->tipo_solicitud_id)" required />

            <x-text-input name="remitente" label="Remitente"
                          value="{{ old('remitente', $solicitud->remitente) }}" required />

            <x-text-input name="asunto" label="Asunto"
                          value="{{ old('asunto', $solicitud->asunto) }}" />

            <x-select-input name="medio_recepcion_id" label="Medio de Recepción"
                            :options="$mediosRecepcion" :selected="old('medio_recepcion_id', $solicitud->medio_recepcion_id)" required />

            <x-text-input name="fecha_ingreso" label="Fecha de Ingreso"
                          type="date" value="{{ old('fecha_ingreso', $solicitud->fecha_ingreso) }}" required />

            <x-text-input name="fecha_vencimiento" label="Fecha de Vencimiento"
                          type="date" value="{{ old('fecha_vencimiento', $solicitud->fecha_vencimiento) }}" />

            <x-select-input name="estado_id" label="Estado"
                            :options="$estados" :selected="old('estado_id', $solicitud->estado_id)" required />

            <x-checkbox-input name="firma_digital" label="Firma Digital"
                              :checked="old('firma_digital', $solicitud->firma_digital)" />

            <button type="submit" class="mt-4 px-4 py-2 bg-purple-600 text-black rounded hover:bg-purple-700">
                Actualizar Solicitud
            </button>
        </form>
    </div>
</x-app-layout>
