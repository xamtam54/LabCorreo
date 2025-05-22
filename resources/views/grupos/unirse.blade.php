<x-app-layout >
    <div class="min-h-screen flex flex-col mx-auto p-6 bg-gray-50">
        <!-- Panel desplegable para Unirse a un grupo -->
        <div x-data="{ open: false }" class="bg-white rounded shadow p-4 mb-6 flex-shrink-0">
            <button @click="open = !open" class="w-full flex justify-between items-center text-xl font-bold focus:outline-none">
                <span>Unirse a un grupo</span>
                <svg :class="{'rotate-180': open}" class="h-6 w-6 transform transition-transform duration-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="open" x-transition class="mt-4">
                @if (session('info'))
                    <div class="bg-blue-100 text-blue-800 p-2 rounded mb-4">
                        {{ session('info') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-red-100 text-red-800 p-2 rounded mb-4">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('grupos.unirse') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="codigo" class="block text-sm font-medium text-gray-700">Código del grupo</label>
                        <input type="text" name="codigo" id="codigo" class="w-full border border-gray-300 rounded p-2 mt-1 focus:ring-2 focus:ring-blue-500 focus:outline-none" required>
                    </div>
                    <x-blue-button type="submit" text="Unirse"/>
                </form>
            </div>
        </div>

        <!-- Grupos existentes -->
        <h3 class="text-2xl font-semibold mb-6 flex-shrink-0 border-b pb-2">Grupos a los que ya perteneces</h3>

        <div class="flex-grow overflow-auto">
            @if ($grupos->isEmpty())
                <p class="text-gray-600 italic">Aún no perteneces a ningún grupo.</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($grupos as $grupo)
                        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow p-6 flex flex-col justify-between">
                            <h4 class="text-xl font-semibold text-gray-900 mb-4 truncate" title="{{ $grupo->nombre }}">
                                {{ $grupo->nombre }}
                            </h4>

                            {{-- Sección de código con botón copiar --}}
                            <div class="flex items-center gap-x-2 mb-8">
                                <span id="codigo-{{ $grupo->id }}" class="font-mono text-sm text-gray-700">{{ $grupo->codigo }}</span>
                                <x-button-copy onclick="copiarCodigo('{{ $grupo->id }}')" />
                            </div>

                        <div class="mt-auto flex flex-wrap gap-3">
                            <x-gray-button href="{{ route('grupos.ver_usuarios', $grupo) }}" text="Ver usuarios" />
                            <x-blue-button href="{{ route('grupos.solicitudes.index', $grupo) }}" text="Ver solicitudes" />
                            <x-blue-button href="{{ route('solicitudes.overview', ['grupo_id' => $grupo->id]) }}" text="Ver reporte" />
                        </div>

                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div id="toast"
            class="hidden fixed bottom-5 right-5 bg-green-200 text-black px-5 py-3 rounded-lg shadow-lg text-sm z-50
                    flex items-center gap-2 font-medium drop-shadow-lg"
        ></div>

    </div>
</x-app-layout>


<script>
    function copiarCodigo(id) {
        const codigo = document.getElementById('codigo-' + id).innerText;
        navigator.clipboard.writeText(codigo)
            .then(() => {
                const toast = document.getElementById('toast');
                toast.textContent = 'Código copiado: ' + codigo;
                toast.classList.remove('hidden');
                setTimeout(() => toast.classList.add('hidden'), 3000);
            });
    }
</script>
