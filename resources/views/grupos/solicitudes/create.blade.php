<x-app-layout>
    <div class="p-8 bg-white rounded-2xl shadow-md max-w-3xl mx-auto mt-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-8">Nueva Solicitud</h2>

        <form action="{{ route('grupos.solicitudes.store', $grupo) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Tipo de Radicación --}}
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Radicación</label>
                <select name="tipo_radicacion" class="w-full p-3 border border-gray-300 rounded-md bg-white">
                    <option value="E" {{ old('tipo_radicacion') == 'E' ? 'selected' : '' }}>Entrada</option>
                    <option value="S" {{ old('tipo_radicacion') == 'S' ? 'selected' : '' }}>Salida</option>
                    <option value="I" {{ old('tipo_radicacion') == 'I' ? 'selected' : '' }}>Interna</option>
                </select>
            </div>

            {{-- Dependencia --}}
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Dependencia</label>
                <select name="dependencia" class="w-full p-3 border border-gray-300 rounded-md bg-white">
                    <option value="SECGEN" {{ old('dependencia') == 'SECGEN' ? 'selected' : '' }}>Secretaría General</option>
                    <option value="SECHAC" {{ old('dependencia') == 'SECHAC' ? 'selected' : '' }}>Secretaría de Hacienda</option>
                    <option value="SECPLA" {{ old('dependencia') == 'SECPLA' ? 'selected' : '' }}>Secretaría de Planeación</option>
                    <option value="SECOPS" {{ old('dependencia') == 'SECOPS' ? 'selected' : '' }}>Secretaría de Obras Públicas</option>
                    <option value="SECAG" {{ old('dependencia') == 'SECAG' ? 'selected' : '' }}>Secretaría de Agricultura</option>
                    <option value="DESP"   {{ old('dependencia') == 'DESP' ? 'selected' : '' }}>Despacho del Alcalde</option>
                    <option value="COMSOC" {{ old('dependencia') == 'COMSOC' ? 'selected' : '' }}>Comunicaciones Sociales</option>
                </select>
            </div>

            {{-- Fecha de ingreso --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Ingreso</label>
                <input id="fecha_ingreso" type="date" name="fecha_ingreso"
                       max="{{ now()->format('Y-m-d') }}"
                       value="{{ old('fecha_ingreso', now()->format('Y-m-d')) }}"
                       class="w-full p-3 border border-gray-300 rounded-md">
            </div>

            {{-- Fecha vencimiento (calculada) --}}
            <input type="hidden" id="fecha_vencimiento" name="fecha_vencimiento" value="{{ old('fecha_vencimiento', now()->format('Y-m-d')) }}">

            {{-- Estado inicial --}}
            <input type="hidden" name="estado_id"
                   value="{{ App\Models\EstadoSolicitud::where('nombre','Nueva')->first()->id }}">

            {{-- Tipo de solicitud --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Solicitud</label>
                <select name="tipo_solicitud_id" class="w-full p-3 border border-gray-300 rounded-md">
                    @foreach(App\Models\TipoSolicitud::all() as $tipo)
                        <option value="{{ $tipo->id }}" {{ old('tipo_solicitud_id') == $tipo->id ? 'selected' : '' }}>
                            {{ $tipo->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- ================= REMITENTE ================= --}}
            <div x-data="{
                    tipo: '{{ old('tipo_remitente_id','') }}',
                    busqueda: '',
                    remitenteSeleccionado: null,
                    mostrandoSugerencias: false,
                    todosRemitentes: {{ $remitentes->whereNotIn('tipo_remitente_id', [2])->values()->toJson() }},

                    // Campos del formulario
                    formNombre: '{{ old('rem_nombre', '') }}',
                    formTipoDoc: '{{ old('rem_tipo_documento_id', '') }}',
                    formNumDoc: '{{ old('rem_numero_documento', '') }}',
                    formTelefono: '{{ old('rem_telefono', '') }}',
                    formCorreo: '{{ old('rem_correo', '') }}',

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

                        // Llenar el formulario con los datos del remitente seleccionado
                        this.formNombre = remitente.nombre;
                        this.formTipoDoc = remitente.tipo_documento_identificacion_id || '';
                        this.formNumDoc = remitente.numero_documento || '';
                        this.formTelefono = remitente.telefono || '';
                        this.formCorreo = remitente.correo || '';
                    },

                    limpiarSeleccion() {
                        this.remitenteSeleccionado = null;
                        this.busqueda = '';
                        this.formNombre = '';
                        this.formTipoDoc = '';
                        this.formNumDoc = '';
                        this.formTelefono = '';
                        this.formCorreo = '';
                    },

                    esNuevoRemitente() {
                        return this.busqueda.trim() && !this.remitenteSeleccionado && this.remitentesEncontrados.length === 0;
                    },

                    mostrarFormulario() {
                        return this.tipo === '2' || this.remitenteSeleccionado || this.esNuevoRemitente();
                    },

                    actualizarNombreDesdeRemitenteSeleccionado() {
                        if (this.tipo === '2') {
                            this.formNombre = 'Anónimo';
                        } else if (this.remitenteSeleccionado) {
                            this.formNombre = this.remitenteSeleccionado.nombre;
                        } else if (this.busqueda.trim()) {
                            // Para nuevos remitentes, usar lo que escribió en búsqueda
                            this.formNombre = this.busqueda;
                        }
                    }
                }"
                x-init="
                    // Si hay datos viejos (old), inicializar el formulario
                    if ('{{ old('rem_nombre') }}') {
                        formNombre = '{{ old('rem_nombre') }}';
                    }

                    // Watch para sincronizar nombre cuando cambia la búsqueda
                    $watch('busqueda', value => {
                        if (!remitenteSeleccionado && value.trim()) {
                            formNombre = value;
                        }
                    });
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
                        <option value="{{ $t->id }}" {{ old('tipo_remitente_id') == $t->id ? 'selected' : '' }}>
                            {{ $t->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('tipo_remitente_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror

                {{-- BÚSQUEDA (solo si NO es anónimo) --}}
                <div x-show="tipo !== '' && tipo !== '2'" class="space-y-3">
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
                                class="w-full p-3 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{'bg-green-50 border-green-500': remitenteSeleccionado}">

                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <template x-if="remitenteSeleccionado">
                                    <button type="button" @click="limpiarSeleccion()" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </template>
                                <template x-if="!remitenteSeleccionado">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </template>
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
                                        <div x-show="remitente.numero_documento">Doc: <span x-text="remitente.numero_documento"></span></div>
                                        <div x-show="remitente.correo">Email: <span x-text="remitente.correo"></span></div>
                                    </div>
                                </button>
                            </template>
                        </div>

                        {{-- Mensaje: remitente seleccionado --}}
                        <div x-show="remitenteSeleccionado"
                            x-transition
                            class="mt-2 p-3 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span class="text-sm font-medium text-green-800">Remitente existente seleccionado</span>
                            </div>
                            <input type="hidden" name="remitente_id" :value="remitenteSeleccionado?.id">
                        </div>

                        {{-- Mensaje: crear nuevo --}}
                        <div x-show="esNuevoRemitente() && busqueda.length >= 3"
                            x-transition
                            class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center mb-2">
                                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                <span class="text-sm font-medium text-blue-800">No se encontró el remitente. Se creará uno nuevo.</span>
                            </div>
                        </div>

                        {{-- Mensaje: búsqueda vacía --}}
                        <div x-show="busqueda.length > 0 && busqueda.length < 3" class="mt-2 text-xs text-gray-500">
                            Escribe al menos 3 caracteres para buscar
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span x-text="tipo === '2' ? 'Remitente Anónimo' : (remitenteSeleccionado ? 'Datos del Remitente Seleccionado' : 'Datos del Nuevo Remitente')"></span>
                    </h3>

                    {{-- Nombre --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo *</label>
                        <input type="text"
                            name="rem_nombre"
                            x-model="formNombre"
                            x-init="if (tipo === '2') formNombre = 'Anónimo'"
                            :readonly="tipo === '2' || remitenteSeleccionado"
                            required
                            class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            :class="{'bg-gray-100': tipo === '2' || remitenteSeleccionado}">
                        @error('rem_nombre') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Mostrar solo si NO es anónimo --}}
                    <template x-if="tipo !== '2'">
                        <div class="space-y-3">
                            {{-- Tipo de Documento --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Documento *</label>
                                <select name="rem_tipo_documento_id"
                                        x-model="formTipoDoc"
                                        :disabled="remitenteSeleccionado"
                                        :required="tipo !== '2'"
                                        class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        :class="{'bg-gray-100': remitenteSeleccionado}">
                                    <option value="">Seleccione...</option>
                                    @foreach(App\Models\TipoDocumentoIdentificacion::all() as $tipoDoc)
                                        <option value="{{ $tipoDoc->id }}">
                                            {{ $tipoDoc->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('rem_tipo_documento_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Número de Documento y Teléfono --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Número de Documento *</label>
                                    <input type="text"
                                        name="rem_numero_documento"
                                        x-model="formNumDoc"
                                        :readonly="remitenteSeleccionado"
                                        :required="tipo !== '2'"
                                        class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        :class="{'bg-gray-100': remitenteSeleccionado}">
                                    @error('rem_numero_documento') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                                    <input type="text"
                                        name="rem_telefono"
                                        x-model="formTelefono"
                                        :readonly="remitenteSeleccionado"
                                        class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                        :class="{'bg-gray-100': remitenteSeleccionado}">
                                </div>
                            </div>

                            {{-- Correo --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                                <input type="email"
                                    name="rem_correo"
                                    x-model="formCorreo"
                                    :readonly="remitenteSeleccionado"
                                    class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    :class="{'bg-gray-100': remitenteSeleccionado}"
                                    placeholder="ejemplo@correo.com">
                            </div>
                        </div>
                    </template>
                </div>

            </div>
            {{-- =============== FIN REMITENTE =============== --}}

            {{-- Asunto --}}
            <div x-data="{ asunto: '{{ old('asunto','') }}' }">
                <label class="block text-sm font-medium text-gray-700 mb-1">Asunto</label>
                <textarea name="asunto" x-model="asunto" maxlength="255"
                          rows="4"
                          class="w-full p-3 border border-gray-300 rounded-md text-sm resize-y"></textarea>
                <div class="text-xs text-gray-500 text-right" x-text="asunto.length + ' / 255'"></div>
            </div>

            {{-- Contenido --}}
            <div x-data="{ contenido: '{{ old('contenido','') }}' }" class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Contenido</label>
                <textarea name="contenido" x-model="contenido" maxlength="3000"
                          rows="6"
                          class="w-full p-3 border border-gray-300 rounded-md text-sm resize-y"
                          placeholder="Escribe el contenido detallado de la solicitud..."></textarea>
                <div class="text-xs text-gray-500 text-right mt-1" x-text="contenido.length + ' / 3000'"></div>
            </div>

            {{-- Medio de Recepción --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Medio de Recepción</label>
                <select name="medio_recepcion_id" class="w-full p-3 border border-gray-300 rounded-md">
                    @foreach(App\Models\MedioRecepcion::all() as $medio)
                        <option value="{{ $medio->id }}" {{ old('medio_recepcion_id') == $medio->id ? 'selected' : '' }}>
                            {{ $medio->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Firma Digital / Múltiples Archivos --}}
            <div x-data="{
                    requiereAdjunto: @json(old('firma_digital', false)),
                    archivos: [],
                    agregarArchivos(event) {
                        const nuevosArchivos = Array.from(event.target.files);
                        nuevosArchivos.forEach(file => {
                            this.archivos.push({
                                file: file,
                                nombre: file.name,
                                tamano: (file.size / 1024 / 1024).toFixed(2) + ' MB',
                                id: Date.now() + Math.random()
                            });
                        });
                        event.target.value = '';
                    },
                    eliminarArchivo(id) {
                        this.archivos = this.archivos.filter(a => a.id !== id);
                    },
                    getTotalSize() {
                        const total = this.archivos.reduce((sum, a) => sum + a.file.size, 0);
                        return (total / 1024 / 1024).toFixed(2);
                    }
                }"
                class="max-w-full mx-auto mt-6 border-t pt-6">

                <input type="hidden" name="firma_digital" value="0">

                <label class="inline-flex items-center mb-3 cursor-pointer select-none">
                    <input type="checkbox" name="firma_digital" x-model="requiereAdjunto" value="1"
                           class="h-5 w-5 text-blue-600">
                    <span class="ml-3 text-gray-800 font-medium">¿Requiere documentos adjuntos?</span>
                </label>

                <div x-show="requiereAdjunto" x-transition.opacity class="mt-4 space-y-4">

                    {{-- Botón para agregar archivos --}}
                    <div class="flex items-center gap-3">
                        <label for="archivos-input" class="cursor-pointer inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Agregar Archivos
                        </label>
                        <input type="file"
                               id="archivos-input"
                               @change="agregarArchivos($event)"
                               multiple
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                               class="hidden">
                        <span class="text-sm text-gray-500">Máximo 10MB por archivo</span>
                    </div>

                    {{-- Lista de archivos seleccionados --}}
                    <div x-show="archivos.length > 0" class="space-y-2">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-gray-700">Archivos seleccionados (<span x-text="archivos.length"></span>)</h4>
                            <span class="text-sm text-gray-600">Total: <span x-text="getTotalSize()"></span> MB</span>
                        </div>

                        <template x-for="archivo in archivos" :key="archivo.id">
                            <div class="flex items-center justify-between p-3 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition">
                                <div class="flex items-center space-x-3 flex-1">
                                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate" x-text="archivo.nombre"></p>
                                        <p class="text-xs text-gray-500" x-text="archivo.tamano"></p>
                                    </div>
                                </div>
                                <button type="button"
                                        @click="eliminarArchivo(archivo.id)"
                                        class="ml-4 text-red-500 hover:text-red-700 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    {{-- Mensaje cuando no hay archivos --}}
                    <div x-show="archivos.length === 0" class="text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">No hay archivos seleccionados</p>
                        <p class="text-xs text-gray-500 mt-1">Haz clic en "Agregar Archivos" para comenzar</p>
                    </div>

                    {{-- Advertencia de tamaño --}}
                    <div x-show="parseFloat(getTotalSize()) > 50" class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex">
                            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <p class="text-sm text-yellow-800">El tamaño total de los archivos es considerable. La carga puede tomar algunos minutos.</p>
                        </div>
                    </div>
                </div>

                {{-- Campo oculto que contendrá los archivos para enviar --}}
                <template x-for="(archivo, index) in archivos" :key="archivo.id">
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

            {{-- Botones --}}
            <div class="pt-4 text-right border-t">
                <x-gray-button text="Cancelar" :href="route('grupos.solicitudes.index', $grupo)" />
                <x-blue-button type="submit" text="Guardar Solicitud" />
            </div>

        </form>
    </div>
</x-app-layout>

<script>
const apiUrl = "{{ url('api/calcular-fecha') }}";

async function actualizarFechaVencimiento(fecha) {
    if (!fecha) return;
    try {
        const response = await fetch(apiUrl + '?fecha=' + fecha);
        if (!response.ok) throw new Error('Error API');
        const data = await response.json();
        document.getElementById('fecha_vencimiento').value = data.fecha_resultado;
    } catch (e) {
        console.error(e);
    }
}

// Ejecutar al cargar la página con el valor por defecto
document.addEventListener('DOMContentLoaded', () => {
    const fechaIngreso = document.getElementById('fecha_ingreso').value;
    actualizarFechaVencimiento(fechaIngreso);
});

// Ejecutar cuando el usuario cambie la fecha
document.getElementById('fecha_ingreso')?.addEventListener('change', function () {
    actualizarFechaVencimiento(this.value);
});
</script>

