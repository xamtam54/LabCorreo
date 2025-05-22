<x-app-layout>
    <div class="p-8 bg-white rounded-2xl shadow-md max-w-3xl mx-auto mt-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">Editar Solicitud</h2>

        <form action="{{ route('grupos.solicitudes.update', ['grupo' => $grupo, 'solicitud' => $solicitud->id]) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Número de Radicado --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Número de Radicado</label>
                <input type="text" name="numero_radicado"
                    value="{{ old('numero_radicado', $solicitud->numero_radicado) }}"
                    class="w-full p-3 border border-gray-300 rounded-md bg-gray-100 text-gray-600" readonly>
            </div>

            {{-- Fecha de Ingreso --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Ingreso</label>
                <input type="date" name="fecha_ingreso"
                    value="{{ old('fecha_ingreso', $solicitud->fecha_ingreso) }}"
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>

            {{-- Fecha de Vencimiento --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Vencimiento</label>
                <input type="date" name="fecha_vencimiento"
                    value="{{ old('fecha_vencimiento', $solicitud->fecha_vencimiento) }}"
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            {{-- Estado --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="estado_id"
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required>
                    @foreach($estados as $id => $nombre)
                        <option value="{{ $id }}" {{ old('estado_id', $solicitud->estado_id) == $id ? 'selected' : '' }}>{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Tipo de Solicitud --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Solicitud</label>
                <select name="tipo_solicitud_id"
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required>
                    @foreach($tiposSolicitud as $id => $nombre)
                        <option value="{{ $id }}" {{ old('tipo_solicitud_id', $solicitud->tipo_solicitud_id) == $id ? 'selected' : '' }}>{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Remitente --}}
            <div x-data="{ remitente: '{{ old('remitente', $solicitud->remitente) }}' }">
                <label class="block text-sm font-medium text-gray-700 mb-0">Remitente</label>
                <input type="text" name="remitente" x-model="remitente" maxlength="100"
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
                <div class="text-xs text-gray-500 text-right mt-0" x-text="remitente.length + ' / 100'"></div>
            </div>

            {{-- Asunto --}}
            <div x-data="{ asunto: '{{ old('asunto', isset($solicitud) ? $solicitud->asunto : '') }}' }">
                <label class="block text-sm font-medium text-gray-700 mb-1">Asunto</label>
                <textarea name="asunto" x-model="asunto" maxlength="255"
                    rows="4"
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm resize-y"></textarea>
                <div class="text-xs text-gray-500 text-right" x-text="asunto.length + ' / 255'"></div>
            </div>


            {{-- Medio de Recepción --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Medio de Recepción</label>
                <select name="medio_recepcion_id"
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required>
                    @foreach($mediosRecepcion as $id => $nombre)
                        <option value="{{ $id }}" {{ old('medio_recepcion_id', $solicitud->medio_recepcion_id) == $id ? 'selected' : '' }}>{{ $nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Firma Digital --}}
            <div class="flex items-center">
                <label class="inline-flex items-center">
                    <input
                        type="checkbox"
                        name="firma_digital"
                        class="form-checkbox"
                        value="1"
                        {{ old('firma_digital', $solicitud->firma_digital) ? 'checked' : '' }}>
                    <span class="ml-2">¿Requiere Firma Digital?</span>
                </label>
            </div>

            {{-- Botón de envío --}}
            <div class="pt-4 text-right">
                <x-gray-button text="Cancelar" :href="route('grupos.solicitudes.index', $grupo)" />
                <x-blue-button type="submit" text="Actualizar Solicitud" />
            </div>
        </form>
    </div>
</x-app-layout>
