<x-app-layout>
    <div class="p-8 bg-white rounded-2xl shadow-md max-w-3xl mx-auto mt-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">Editar Solicitud</h2>

        <form action="{{ route('grupos.solicitudes.update', ['grupo' => $grupo, 'solicitud' => $solicitud->id]) }}"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Número de Radicado --}}
            <div>
                <label for="numero_radicado" class="block text-sm font-medium text-gray-700 mb-1">Número de Radicado</label>
                <input type="text" id="numero_radicado" name="numero_radicado"
                    value="{{ old('numero_radicado', $solicitud->numero_radicado) }}"
                    class="w-full p-3 border border-gray-300 rounded-md bg-gray-100 text-gray-600" readonly>
                <input type="hidden" name="numero_radicado" value="{{ $solicitud->numero_radicado }}">
            </div>

            {{-- Fecha de Ingreso --}}
            <div>
                <label for="fecha_ingreso" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Ingreso</label>
                <input type="date" id="fecha_ingreso" name="fecha_ingreso"
                    value="{{ old('fecha_ingreso', \Carbon\Carbon::parse($solicitud->fecha_ingreso)->format('Y-m-d')) }}"
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required>
                @error('fecha_ingreso')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Fecha de Vencimiento --}}
            <div>
                <label for="fecha_vencimiento" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Vencimiento</label>
                <input type="date" id="fecha_vencimiento" name="fecha_vencimiento"
                    value="{{ old('fecha_vencimiento', $solicitud->fecha_vencimiento ? \Carbon\Carbon::parse($solicitud->fecha_vencimiento)->format('Y-m-d') : '') }}"
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                @error('fecha_vencimiento')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Estado --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <input type="text" value="Nueva" class="w-full p-3 border border-gray-300 rounded-md bg-gray-100 text-gray-500" disabled>

                {{-- Campo oculto para enviar el estado_id de "Nueva" --}}
                <input type="hidden" name="estado_id" value="{{ App\Models\EstadoSolicitud::where('nombre', 'Nueva')->first()->id }}">
            </div>

            {{-- Estado

            <div>
                <label for="estado_id" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select id="estado_id" name="estado_id"
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required>
                    @foreach($estados as $id => $nombre)
                        <option value="{{ $id }}" {{ old('estado_id', $solicitud->estado_id) == $id ? 'selected' : '' }}>
                            {{ $nombre }}
                        </option>
                    @endforeach
                </select>
                @error('estado_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div> --}}

            {{-- Tipo de Solicitud --}}
            <div>
                <label for="tipo_solicitud_id" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Solicitud</label>
                <select id="tipo_solicitud_id" name="tipo_solicitud_id"
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required>
                    @foreach($tiposSolicitud as $id => $nombre)
                        <option value="{{ $id }}" {{ old('tipo_solicitud_id', $solicitud->tipo_solicitud_id) == $id ? 'selected' : '' }}>
                            {{ $nombre }}
                        </option>
                    @endforeach
                </select>
                @error('tipo_solicitud_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remitente --}}
            <div x-data="{ remitente: '{{ old('remitente', $solicitud->remitente) }}' }">
                <label for="remitente" class="block text-sm font-medium text-gray-700 mb-0">Remitente</label>
                <input type="text" id="remitente" name="remitente" x-model="remitente" maxlength="100"
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
                <div class="text-xs text-gray-500 text-right mt-0" x-text="remitente.length + ' / 100'"></div>
                @error('remitente')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Asunto --}}
            <div x-data="{ asunto: '{{ old('asunto', $solicitud->asunto) }}' }">
                <label for="asunto" class="block text-sm font-medium text-gray-700 mb-1">Asunto</label>
                <textarea id="asunto" name="asunto" x-model="asunto" maxlength="255"
                    rows="4"
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm resize-y"></textarea>
                <div class="text-xs text-gray-500 text-right" x-text="asunto.length + ' / 255'"></div>
                @error('asunto')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Contenido --}}
            <div x-data="{ contenido: '{{ old('contenido', isset($solicitud) ? $solicitud->contenido : '') }}' }" class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Contenido</label>
                <textarea name="contenido" x-model="contenido" maxlength="3000"
                    rows="6"
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm resize-y"
                    placeholder="Escribe el contenido detallado de la solicitud..."></textarea>
                <div class="text-xs text-gray-500 text-right mt-1" x-text="contenido.length + ' / 3000'"></div>
            </div>


            {{-- Medio de Recepción --}}
            <div>
                <label for="medio_recepcion_id" class="block text-sm font-medium text-gray-700 mb-1">Medio de Recepción</label>
                <select id="medio_recepcion_id" name="medio_recepcion_id"
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required>
                    @foreach($mediosRecepcion as $id => $nombre)
                        <option value="{{ $id }}" {{ old('medio_recepcion_id', $solicitud->medio_recepcion_id) == $id ? 'selected' : '' }}>
                            {{ $nombre }}
                        </option>
                    @endforeach
                </select>
                @error('medio_recepcion_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div x-data="{ requiereAdjunto: @json(old('firma_digital', $solicitud->firma_digital)) }" class="max-w-md mx-auto">
    <label class="inline-flex items-center mb-3 cursor-pointer select-none">
        <input
            type="checkbox"
            name="firma_digital"
            x-model="requiereAdjunto"
            value="1"
            class="form-checkbox h-5 w-5 text-indigo-600 transition duration-150 ease-in-out"
            {{ old('firma_digital', $solicitud->firma_digital) ? 'checked' : '' }}>
        <span class="ml-3 text-gray-800 font-medium">¿Requiere documento adjunto para completarlo?</span>
    </label>

    <div x-show="requiereAdjunto" x-transition.opacity class="mt-4">
        <label for="archivo" class="block text-sm font-semibold text-gray-700 mb-2">Subir Documento</label>
        <input
            type="file"
            id="archivo"
            name="archivo"
            class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
        >
        @error('archivo')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Mostrar el documento actual siempre que exista --}}
    @if($solicitud->documento)
        <div class="bg-white border border-gray-300 rounded-lg p-4 mt-6 shadow-sm hover:shadow-md transition-shadow duration-200 max-w-md">
            <a href="{{ route('grupos.solicitudes.documento.ver', ['grupo' => $solicitud->grupo_id, 'id' => $solicitud->documento->id]) }}"
               target="_blank"
               class="text-indigo-600 hover:text-indigo-900 hover:underline font-semibold truncate block max-w-full">
                {{ $solicitud->documento->nombre_archivo }}
            </a>
            <div class="flex items-center text-gray-500 text-sm mt-1 space-x-3 select-none">
                <span>{{ number_format(($solicitud->documento->tamano_mb ?? 0), 2) }} MB</span>
                <a href="{{ route('grupos.solicitudes.documento.descargar', ['grupo' => $solicitud->grupo_id, 'id' => $solicitud->documento->id]) }}"
                   class="text-green-600 hover:text-green-900 hover:underline font-semibold"
                   download>Descargar</a>
            </div>
        </div>
    @else
        <p class="text-gray-500 italic mt-4 text-center">No hay archivo adjunto para esta solicitud.</p>
    @endif
</div>

            {{-- Botón de envío --}}
            <div class="pt-4 text-right space-x-2">
                <x-gray-button text="Cancelar" :href="route('grupos.solicitudes.index', $grupo)" />
                <x-blue-button type="submit" text="Actualizar Solicitud" />
            </div>
        </form>
    </div>
</x-app-layout>
