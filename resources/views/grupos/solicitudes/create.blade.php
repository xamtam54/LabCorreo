<x-app-layout>
    <div class="p-8 bg-white rounded-2xl shadow-md max-w-3xl mx-auto mt-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">Nueva Solicitud</h2>

        <form action="{{ route('grupos.solicitudes.store', $grupo) }}" method="POST" class="space-y-6">
            @csrf

            {{-- Número de Radicado --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Número de Radicado</label>
                <input type="text" name="numero_radicado" value="{{ 'SOL-' . now()->format('YmdHis') . '-' . rand(100, 999) }}"
                    class="w-full p-3 border border-gray-300 rounded-md bg-gray-100 text-gray-600" readonly>
            </div>

            {{-- Fecha de Ingreso --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Ingreso</label>
                <input type="date" name="fecha_ingreso"
                    max="{{ now()->format('Y-m-d') }}"
                    value="{{ now()->format('Y-m-d') }}"
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
            </div>


            {{-- Fecha de Vencimiento --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Vencimiento</label>
                <input type="date" name="fecha_vencimiento" value="{{ now()->addDays(15)->format('Y-m-d') }}"
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            {{-- Estado --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="estado_id" class="w-full p-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach(App\Models\EstadoSolicitud::all() as $estado)
                        <option value="{{ $estado->id }}" {{ $estado->nombre === 'Pendiente' ? 'selected' : '' }}>
                            {{ $estado->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tipo de Solicitud --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Solicitud</label>
                <select name="tipo_solicitud_id" class="w-full p-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach(App\Models\TipoSolicitud::all() as $tipo)
                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                    @endforeach
                </select>
            </div>

                        {{-- Remitente --}}
            <div x-data="{ remitente: '' }">
                <label class="block text-sm font-medium text-gray-700 mb-1">Remitente</label>
                <input type="text" name="remitente" x-model="remitente" maxlength="100"
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
                <div class="text-xs text-gray-500 text-right" x-text="remitente.length + ' / 100'"></div>
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
                <select name="medio_recepcion_id" class="w-full p-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach(App\Models\MedioRecepcion::all() as $medio)
                        <option value="{{ $medio->id }}">{{ $medio->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Firma Digital --}}
            <div class="flex items-center">
                <label class="inline-flex items-center">
                    <input
                        type="checkbox"
                        name="firma_digital"
                        value="1"
                        class="form-checkbox"
                        {{ old('firma_digital') ? 'checked' : '' }}>
                    <span class="ml-2">¿Requiere Firma Digital?</span>
                </label>
            </div>


            {{-- Botón de envío --}}
            <div class="pt-4 text-right">
                <x-gray-button text="Cancelar" :href="route('grupos.solicitudes.index', $grupo)" />
                <x-blue-button type="submit" text="Guardar Solicitud" />
            </div>
        </form>
    </div>
</x-app-layout>
