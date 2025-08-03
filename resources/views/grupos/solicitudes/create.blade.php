<x-app-layout>
    <div class="p-8 bg-white rounded-2xl shadow-md max-w-3xl mx-auto mt-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">Nueva Solicitud</h2>

        <form action="{{ route('grupos.solicitudes.store', $grupo) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
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
                <input id="fecha_ingreso" type="date" name="fecha_ingreso"
                    max="{{ now()->format('Y-m-d') }}"
                    value="{{ now()->format('Y-m-d') }}"
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            {{-- Fecha de Vencimiento --}}
            <div class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Vencimiento</label>
                <input id="fecha_vencimiento" type="date" name="fecha_vencimiento"
                    value="{{ $fechaVencimiento ?? now()->format('Y-m-d') }}" readonly
                    class="w-full p-3 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed">
            </div>

            {{-- Estado --}}
            <div class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <input type="text" value="Nueva" class="w-full p-3 border border-gray-300 rounded-md bg-gray-100 text-gray-500" disabled>
                <input type="hidden" name="estado_id" value="{{ App\Models\EstadoSolicitud::where('nombre', 'Nueva')->first()->id }}">
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Medio de Recepción</label>
                <select name="medio_recepcion_id" class="w-full p-3 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach(App\Models\MedioRecepcion::all() as $medio)
                        <option value="{{ $medio->id }}">{{ $medio->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Firma digital y archivo --}}
            <div x-data="{ requiereAdjunto: @json(old('firma_digital', false)) }" class="max-w-md mx-auto mt-6">

                {{-- Para enviar el valor falso si no se marca --}}
                <input type="hidden" name="firma_digital" value="0">

                <label class="inline-flex items-center mb-3 cursor-pointer select-none">
                    <input
                        type="checkbox"
                        name="firma_digital"
                        x-model="requiereAdjunto"
                        value="1"
                        class="form-checkbox h-5 w-5 text-indigo-600 transition duration-150 ease-in-out"
                    >
                    <span class="ml-3 text-gray-800 font-medium">¿Requiere documento adjunto para completarlo?</span>
                </label>

                {{-- Campo para subir el archivo, visible solo si se marca --}}
                <div x-show="requiereAdjunto" x-transition.opacity class="mt-4">
                    <label for="archivo" class="block text-sm font-semibold text-gray-700 mb-2">Subir Documento</label>
                    <input
                        type="file"
                        id="archivo"
                        name="archivo"
                        x-ref="archivoInput"
                        @change="if ($refs.archivoInput.files[0]?.size > 10485760) { alert('El archivo no puede superar los 10 MB.'); $refs.archivoInput.value = '' }"
                        class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                    />
                    @error('archivo')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>


            {{-- Botón de envío --}}
            <div class="pt-4 text-right">
                <x-gray-button text="Cancelar" :href="route('grupos.solicitudes.index', $grupo)" />
                <x-blue-button type="submit" text="Guardar Solicitud" />
            </div>
        </form>
    </div>
</x-app-layout>


<script>
    window.addEventListener('DOMContentLoaded', () => {
    const fechaInput = document.getElementById('fecha_ingreso');
    if (fechaInput && fechaInput.value) {
        // Disparar el evento de cambio manualmente
        fechaInput.dispatchEvent(new Event('change'));
    }
    });

    const apiUrl = "{{ url('api/calcular-fecha') }}";

    document.getElementById('fecha_ingreso').addEventListener('change', async function() {
        const fechaInicial = this.value;
        if (!fechaInicial) return;

        try {
            const response = await fetch(apiUrl + '?fecha=' + fechaInicial);
            if (!response.ok) throw new Error('Error en la llamada a la API');

            const data = await response.json();
            document.getElementById('fecha_vencimiento').value = data.fecha_resultado;
        } catch (error) {
            console.error(error);
            alert('No se pudo calcular la fecha de vencimiento.');
        }
    });
</script>
