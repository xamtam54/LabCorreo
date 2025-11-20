<x-app-layout>
    <div class="p-8 bg-white rounded-2xl shadow-md max-w-4xl mx-auto mt-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">Editar Solicitud #{{ $solicitud->numero_radicado }}</h2>

        <form action="{{ route('grupos.solicitudes.update', ['grupo' => $grupo, 'solicitud' => $solicitud->id]) }}"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Información de Radicación (Solo lectura) --}}
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Radicación</label>
                        <p class="text-gray-900 font-semibold">
                            @if($solicitud->tipo_radicacion === 'E') Entrada
                            @elseif($solicitud->tipo_radicacion === 'S') Salida
                            @elseif($solicitud->tipo_radicacion === 'I') Interna
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dependencia</label>
                        <p class="text-gray-900 font-semibold">{{ $solicitud->dependencia }}</p>
                    </div>
                </div>
            </div>

            {{-- Número de Radicado --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Número de Radicado</label>
                <input type="hidden" name="numero_radicado" value="{{ $solicitud->numero_radicado }}">
                <p class="text-gray-900 font-semibold text-lg">{{ $solicitud->numero_radicado }}</p>
            </div>

            {{-- Fecha de Ingreso --}}
            <div>
                <label for="fecha_ingreso" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Ingreso</label>
                <input type="date" id="fecha_ingreso" name="fecha_ingreso"
                    value="{{ old('fecha_ingreso', \Carbon\Carbon::parse($solicitud->fecha_ingreso)->format('Y-m-d')) }}"
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                @error('fecha_ingreso')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Fecha de Vencimiento --}}
            <div>
                <label for="fecha_vencimiento" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Vencimiento</label>
                <input type="date" id="fecha_vencimiento" name="fecha_vencimiento"
                    value="{{ old('fecha_vencimiento', $solicitud->fecha_vencimiento ? \Carbon\Carbon::parse($solicitud->fecha_vencimiento)->format('Y-m-d') : now()->format('Y-m-d')) }}"
                    readonly
                    class="w-full p-3 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed">
                @error('fecha_vencimiento')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <input type="hidden" name="estado_id" value="{{ $solicitud->estado_id }}">

            {{-- Tipo de Solicitud --}}
            <div>
                <label for="tipo_solicitud_id" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Solicitud</label>
                <select id="tipo_solicitud_id" name="tipo_solicitud_id"
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
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

            {{-- ================= REMITENTE ================= --}}
            <div x-data="{
                tipo: '{{ old('tipo_remitente_id', $solicitud->tipo_remitente_id) }}',
                busqueda: '{{ old('rem_nombre', $solicitud->remitente ? $solicitud->remitente->nombre : '') }}',
                remitenteSeleccionado: @js(old('remitente_id') ?
                    \App\Models\Remitente::find(old('remitente_id')) :
                    ($solicitud->remitente_id && !$solicitud->es_anonimo ? $solicitud->remitente : null)
                ),
                mostrandoSugerencias: false,
                todosRemitentes: {{ $remitentes->whereNotIn('tipo_remitente_id', [2])->values()->toJson() }},

                get remitentesEncontrados() {
                    if (!this.busqueda.trim() || this.tipo == '2') return [];
                    const busq = this.busqueda.toLowerCase();
                    return this.todosRemitentes.filter(r =>
                        r.nombre?.toLowerCase().includes(busq) ||
                        r.numero_documento?.includes(busq) ||
                        r.correo?.toLowerCase().includes(busq)
                    ).slice(0, 5);
                },

                seleccionarRemitente(remitente) {
                    this.remitenteSeleccionado = remitente;
                    this.busqueda = remitente.nombre;
                    this.mostrandoSugerencias = false;
                },

                limpiarSeleccion() {
                    this.remitenteSeleccionado = null;
                    this.busqueda = '';
                },

                esNuevoRemitente() {
                    return this.busqueda.trim() &&
                        !this.remitenteSeleccionado &&
                        this.remitentesEncontrados.length === 0;
                },

                mostrarFormulario() {
                    return this.tipo === '2' || this.remitenteSeleccionado || this.esNuevoRemitente();
                }
            }"
            x-init="
                // Inicializar búsqueda con el nombre del remitente seleccionado
                if (remitenteSeleccionado) {
                    busqueda = remitenteSeleccionado.nombre;
                }
            "
            class="space-y-4 border-t pt-6">

                <label for="tipo_remitente_id" class="block text-sm font-semibold mb-1">
                    Tipo de Remitente *
                </label>
                <select id="tipo_remitente_id"
                        name="tipo_remitente_id"
                        x-model="tipo"
                        @change="limpiarSeleccion()"
                        required
                        class="w-full p-3 border rounded mb-1">
                    <option value="">Seleccione...</option>
                    @foreach ($tipos_remitente as $t)
                        <option value="{{ $t->id }}"
                                {{ old('tipo_remitente_id', $solicitud->tipo_remitente_id) == $t->id ? 'selected' : '' }}>
                            {{ $t->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('tipo_remitente_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror

                {{-- Remitente Actual (solo mostrar si hay remitente y no se ha limpiado la selección) --}}
                <div x-show="remitenteSeleccionado && tipo !== '2'"
                    class="p-4 bg-blue-50 border border-blue-200 rounded-lg mb-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-800 mb-2">Remitente seleccionado:</p>
                            <p class="font-semibold text-blue-900" x-text="remitenteSeleccionado?.nombre"></p>
                            <div class="text-sm text-blue-700">
                                <span x-show="remitenteSeleccionado?.numero_documento">
                                    Doc: <span x-text="remitenteSeleccionado?.numero_documento"></span>
                                </span>
                                <span x-show="remitenteSeleccionado?.correo">
                                    • <span x-text="remitenteSeleccionado?.correo"></span>
                                </span>
                            </div>
                        </div>
                        <button type="button"
                                @click="limpiarSeleccion()"
                                class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Cambiar remitente
                        </button>
                    </div>
                    {{-- Hidden input para enviar el ID del remitente seleccionado --}}
                    <input type="hidden" name="remitente_id" :value="remitenteSeleccionado?.id">
                </div>

                {{-- BÚSQUEDA INTELIGENTE (solo si NO es anónimo y NO hay remitente seleccionado) --}}
                <div x-show="tipo !== '' && tipo !== '2' && !remitenteSeleccionado" class="space-y-3">
                    <div class="relative" @click.away="mostrandoSugerencias = false">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Buscar o Crear Remitente *
                        </label>
                        <div class="relative">
                            <input type="text"
                                x-model="busqueda"
                                @focus="mostrandoSugerencias = true"
                                @input="mostrandoSugerencias = true"
                                placeholder="Escribe el nombre, documento o correo del remitente..."
                                class="w-full p-3 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>

                        {{-- Sugerencias --}}
                        <div x-show="mostrandoSugerencias && busqueda.length >= 2 && remitentesEncontrados.length > 0"
                            x-transition
                            class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto">
                            <template x-for="remitente in remitentesEncontrados" :key="remitente.id">
                                <button type="button"
                                        @click="seleccionarRemitente(remitente)"
                                        class="w-full px-4 py-3 text-left hover:bg-blue-50 border-b border-gray-100 last:border-b-0 transition">
                                    <div class="font-medium text-gray-900" x-text="remitente.nombre"></div>
                                    <div class="text-xs text-gray-500 mt-1 space-y-0.5">
                                        <div x-show="remitente.numero_documento">
                                            Doc: <span x-text="remitente.numero_documento"></span>
                                        </div>
                                        <div x-show="remitente.correo">
                                            Email: <span x-text="remitente.correo"></span>
                                        </div>
                                    </div>
                                </button>
                            </template>
                        </div>

                        {{-- Mensaje: crear nuevo --}}
                        <div x-show="esNuevoRemitente() && busqueda.length >= 3"
                            x-transition
                            class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                <span class="text-sm font-medium text-blue-800">
                                    No se encontró el remitente. Se creará uno nuevo.
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FORMULARIO DE DATOS DEL REMITENTE --}}
                <div x-show="mostrarFormulario()"
                    x-transition
                    class="p-4 border-2 rounded-lg space-y-3"
                    :class="{
                        'border-yellow-300 bg-yellow-50': tipo === '2',
                        'border-green-300 bg-green-50': remitenteSeleccionado,
                        'border-blue-300 bg-blue-50': esNuevoRemitente()
                    }">

                    <h3 class="font-semibold mb-3 flex items-center"
                        :class="{
                            'text-yellow-900': tipo === '2',
                            'text-green-900': remitenteSeleccionado,
                            'text-blue-900': esNuevoRemitente()
                        }">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span x-text="tipo === '2' ? 'Remitente Anónimo' :
                                    (remitenteSeleccionado ? 'Datos del Remitente Seleccionado' : 'Datos del Nuevo Remitente')">
                        </span>
                    </h3>

                    {{-- Nombre --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo *</label>
                        <input type="text"
                            name="rem_nombre"
                            :value="tipo === '2' ? 'Anónimo' :
                                    (remitenteSeleccionado ? remitenteSeleccionado.nombre : busqueda)"
                            :readonly="tipo === '2' || remitenteSeleccionado"
                            required
                            class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            :class="{'bg-gray-100': tipo === '2' || remitenteSeleccionado}">
                    </div>

                    {{-- Mostrar solo si NO es anónimo --}}
                    <template x-if="tipo !== '2'">
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Documento *</label>
                                <select name="rem_tipo_documento_id"
                                        :disabled="remitenteSeleccionado"
                                        :required="tipo !== '2'"
                                        class="w-full p-2.5 border border-gray-300 rounded-lg"
                                        :class="{'bg-gray-100': remitenteSeleccionado}">
                                    <option value="">Seleccione...</option>
                                    @foreach(App\Models\TipoDocumentoIdentificacion::all() as $tipoDoc)
                                        <option value="{{ $tipoDoc->id }}"
                                                x-bind:selected="remitenteSeleccionado &&
                                                                remitenteSeleccionado.tipo_documento_identificacion_id == {{ $tipoDoc->id }}">
                                            {{ $tipoDoc->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Número de Documento *</label>
                                    <input type="text"
                                        name="rem_numero_documento"
                                        :value="remitenteSeleccionado ? remitenteSeleccionado.numero_documento : ''"
                                        :readonly="remitenteSeleccionado"
                                        :required="tipo !== '2'"
                                        class="w-full p-2.5 border border-gray-300 rounded-lg"
                                        :class="{'bg-gray-100': remitenteSeleccionado}">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                                    <input type="text"
                                        name="rem_telefono"
                                        :value="remitenteSeleccionado ? remitenteSeleccionado.telefono : ''"
                                        :readonly="remitenteSeleccionado"
                                        class="w-full p-2.5 border border-gray-300 rounded-lg"
                                        :class="{'bg-gray-100': remitenteSeleccionado}">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                                <input type="email"
                                    name="rem_correo"
                                    :value="remitenteSeleccionado ? remitenteSeleccionado.correo : ''"
                                    :readonly="remitenteSeleccionado"
                                    class="w-full p-2.5 border border-gray-300 rounded-lg"
                                    :class="{'bg-gray-100': remitenteSeleccionado}">
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            {{-- =============== FIN REMITENTE =============== --}}

            {{-- Asunto --}}
            <div x-data="{ asunto: @js(old('asunto', $solicitud->asunto)) }">
                <label class="block text-sm font-medium text-gray-700 mb-1">Asunto</label>
                <textarea name="asunto" x-model="asunto" maxlength="255"
                          rows="4"
                          class="w-full p-3 border border-gray-300 rounded-md text-sm resize-y"></textarea>
                <div class="text-xs text-gray-500 text-right" x-text="asunto.length + ' / 255'"></div>
            </div>

            {{-- Contenido --}}
            <div x-data="{ contenido: @js(old('contenido', $solicitud->contenido ?? '')) }" class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Contenido</label>
                <textarea name="contenido" x-model="contenido" maxlength="3000"
                          rows="6"
                          class="w-full p-3 border border-gray-300 rounded-md text-sm resize-y"
                          placeholder="Escribe el contenido detallado de la solicitud..."></textarea>
                <div class="text-xs text-gray-500 text-right mt-1" x-text="contenido.length + ' / 3000'"></div>
            </div>

            {{-- Medio de Recepción --}}
            <div>
                <label for="medio_recepcion_id" class="block text-sm font-medium text-gray-700 mb-1">Medio de Recepción</label>
                <select id="medio_recepcion_id" name="medio_recepcion_id"
                    class="w-full p-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
                    @foreach($mediosRecepcion as $id => $nombre)
                        <option value="{{ $id }}" {{ old('medio_recepcion_id', $solicitud->medio_recepcion_id) == $id ? 'selected' : '' }}>
                            {{ $nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Gestión de Documentos --}}
            <div x-data="{
                    requiereAdjunto: {{ $solicitud->firma_digital ? 'true' : 'false' }},
                    archivosExistentes: @js($solicitud->documentos->map(function($doc) {
                        return [
                            'id' => $doc->id,
                            'nombre' => $doc->nombre_archivo,
                            'tamano' => number_format($doc->tamano_mb, 2) . ' MB',
                            'eliminar' => false
                        ];
                    })->values()->toArray()),
                    archivosNuevos: [],
                    agregarArchivos(event) {
                        const nuevosArchivos = Array.from(event.target.files);
                        nuevosArchivos.forEach(file => {
                            this.archivosNuevos.push({
                                file: file,
                                nombre: file.name,
                                tamano: (file.size / 1024 / 1024).toFixed(2) + ' MB',
                                id: Date.now() + Math.random()
                            });
                        });
                        event.target.value = '';
                    },
                    marcarParaEliminar(id) {
                        const doc = this.archivosExistentes.find(d => d.id === id);
                        if (doc) doc.eliminar = !doc.eliminar;
                    },
                    eliminarNuevo(id) {
                        this.archivosNuevos = this.archivosNuevos.filter(a => a.id !== id);
                    },
                    getTotalSize() {
                        const total = this.archivosNuevos.reduce((sum, a) => sum + a.file.size, 0);
                        return (total / 1024 / 1024).toFixed(2);
                    }
                }"
                class="border-t pt-6">

                <input type="hidden" name="firma_digital" value="0">

                <label class="inline-flex items-center mb-4 cursor-pointer select-none">
                    <input type="checkbox" name="firma_digital" x-model="requiereAdjunto" value="1"
                           class="h-5 w-5 text-blue-600">
                    <span class="ml-3 text-gray-800 font-medium">¿Requiere documentos adjuntos?</span>
                </label>

                <div x-show="requiereAdjunto" x-transition class="space-y-4">

                    {{-- Documentos Existentes --}}
                    <div x-show="archivosExistentes.length > 0" class="space-y-3">
                        <h4 class="font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Documentos Actuales
                        </h4>

                        <template x-for="doc in archivosExistentes" :key="doc.id">
                            <div class="flex items-center justify-between p-3 rounded-lg border transition"
                                 :class="doc.eliminar ? 'bg-red-50 border-red-300' : 'bg-gray-50 border-gray-200'">
                                <div class="flex items-center space-x-3 flex-1">
                                    <svg class="w-8 h-8" :class="doc.eliminar ? 'text-red-400' : 'text-blue-500'"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium" :class="doc.eliminar ? 'text-red-600 line-through' : 'text-gray-900'"
                                           x-text="doc.nombre"></p>
                                        <p class="text-xs" :class="doc.eliminar ? 'text-red-500' : 'text-gray-500'"
                                           x-text="doc.tamano"></p>
                                    </div>
                                </div>
                                <button type="button"
                                        @click="marcarParaEliminar(doc.id)"
                                        class="px-3 py-1 rounded text-sm font-medium transition"
                                        :class="doc.eliminar ? 'bg-gray-200 text-gray-700 hover:bg-gray-300' : 'bg-red-100 text-red-700 hover:bg-red-200'">
                                    <span x-text="doc.eliminar ? 'Restaurar' : 'Eliminar'"></span>
                                </button>
                                <input type="hidden"
                                       :name="doc.eliminar ? 'documentos_eliminar[]' : ''"
                                       :value="doc.eliminar ? doc.id : ''">
                            </div>
                        </template>
                    </div>

                    {{-- Agregar Nuevos Documentos --}}
                    <div class="border-t pt-4">
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Agregar Nuevos Documentos
                        </h4>

                        <div class="flex items-center gap-3 mb-4">
                            <label for="nuevos-archivos" class="cursor-pointer inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Seleccionar Archivos
                            </label>
                            <input type="file"
                                   id="nuevos-archivos"
                                   @change="agregarArchivos($event)"
                                   multiple
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                   class="hidden">
                            <span class="text-sm text-gray-500">Máximo 10MB por archivo</span>
                        </div>

                        <div x-show="archivosNuevos.length > 0" class="space-y-2">
                            <template x-for="archivo in archivosNuevos" :key="archivo.id">
                                <div class="flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex items-center space-x-3 flex-1">
                                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900" x-text="archivo.nombre"></p>
                                            <p class="text-xs text-gray-500" x-text="archivo.tamano"></p>
                                        </div>
                                    </div>
                                    <button type="button"
                                            @click="eliminarNuevo(archivo.id)"
                                            class="text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>

                        {{-- Campos hidden para nuevos archivos --}}
                        <template x-for="(archivo, index) in archivosNuevos" :key="archivo.id">
                            <input type="file"
                                   :name="'archivos[' + index + ']'"
                                   class="hidden"
                                   x-ref="hiddenFile"
                                   x-init="
                                       const dataTransfer = new DataTransfer();
                                       dataTransfer.items.add(archivo.file);
                                       $el.files = dataTransfer.files;
                                   ">
                        </template>
                    </div>
                </div>
            </div>

            {{-- Botones --}}
            <div class="pt-6 flex items-center justify-between border-t">
                <x-gray-button text="Cancelar" :href="route('grupos.solicitudes.index', $grupo)" />
                <x-blue-button type="submit" text="Actualizar Solicitud" />
            </div>
        </form>
    </div>
</x-app-layout>

{{-- Script para calcular fecha de vencimiento --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const fechaInput = document.getElementById('fecha_ingreso');
    if (fechaInput && fechaInput.value) {
        fechaInput.dispatchEvent(new Event('change'));
    }

    fechaInput.addEventListener('change', async () => {
        const fechaInicial = fechaInput.value;
        if (!fechaInicial) return;

        try {
            const response = await fetch("{{ url('api/calcular-fecha') }}?fecha=" + fechaInicial);
            if (!response.ok) throw new Error('Error en la llamada a la API');

            const data = await response.json();
            document.getElementById('fecha_vencimiento').value = data.fecha_resultado;
        } catch (error) {
            console.error(error);
            alert('No se pudo calcular la fecha de vencimiento.');
        }
    });
});
</script>
