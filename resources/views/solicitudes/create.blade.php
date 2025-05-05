<x-app-layout>
    <div class="p-6 bg-white rounded-xl shadow max-w-3xl mx-auto">
        <h2 class="text-2xl font-semibold mb-6">Nueva Solicitud</h2>

        <form action="{{ route('solicitudes.store') }}" method="POST">
            @csrf

            {{-- Número de Radicado (generado automáticamente) --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Número de Radicado</label>
                <input type="text" name="numero_radicado" value="{{ 'SOL-' . now()->format('YmdHis') . '-' . rand(100, 999) }}" class="w-full mt-1 p-2 border rounded" readonly>
            </div>

            {{-- Fecha de Ingreso --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Fecha de Ingreso</label>
                <input type="date" name="fecha_ingreso" value="{{ now()->format('Y-m-d') }}" class="w-full mt-1 p-2 border rounded" readonly>
            </div>

            {{-- Fecha de Vencimiento (5 días después) --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Fecha de Vencimiento</label>
                <input type="date" name="fecha_vencimiento" value="{{ now()->addDays(15)->format('Y-m-d') }}" class="w-full mt-1 p-2 border rounded" readonly>
            </div>

            {{-- Estado (predeterminado: Pendiente) --}}
            {{-- Estado (editable con valor predeterminado) --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Estado</label>
                <select name="estado_id" class="w-full mt-1 p-2 border rounded">
                    @foreach(App\Models\EstadoSolicitud::all() as $estado)
                        <option value="{{ $estado->id }}"
                            {{ $estado->nombre === 'Pendiente' ? 'selected' : '' }}>
                            {{ $estado->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>


            {{-- Tipo de Solicitud --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Tipo de Solicitud</label>
                <select name="tipo_solicitud_id" class="w-full mt-1 p-2 border rounded">
                    @foreach(App\Models\TipoSolicitud::all() as $tipo)
                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Remitente --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Remitente</label>
                <input type="text" name="remitente" class="w-full mt-1 p-2 border rounded" required>
            </div>

            {{-- Asunto --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Asunto</label>
                <input type="text" name="asunto" class="w-full mt-1 p-2 border rounded">
            </div>

            {{-- Medio de Recepción --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Medio de Recepción</label>
                <select name="medio_recepcion_id" class="w-full mt-1 p-2 border rounded">
                    @foreach(App\Models\MedioRecepcion::all() as $medio)
                        <option value="{{ $medio->id }}">{{ $medio->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Firma Digital --}}
            <div class="mb-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="firma_digital" class="form-checkbox">
                    <span class="ml-2">¿Firma Digital?</span>
                </label>
            </div>

            {{-- Botón --}}
            <div class="text-right">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Guardar Solicitud
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
