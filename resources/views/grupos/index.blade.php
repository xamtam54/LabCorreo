<x-app-layout>
    <div class="container mx-auto px-6 py-6">
        <h1 class="text-3xl font-semibold mb-6 text-center">Grupos</h1>

        <div class="mb-4 text-right">
            <a href="{{ route('grupos.create') }}"
               class="inline-block px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                Crear nuevo grupo
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-3 px-6 text-left text-sm font-medium text-gray-700">Nombre</th>
                        <th class="py-3 px-6 text-left text-sm font-medium text-gray-700">Descripción</th>
                        <th class="py-3 px-6 text-left text-sm font-medium text-gray-700">Codigo</th>
                        <th class="py-3 px-6 text-left text-sm font-medium text-gray-700">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($grupos as $grupo)
                        <tr>
                            <td class="py-3 px-6 text-gray-800">{{ $grupo->nombre }}</td>
                            <td class="py-3 px-6 text-gray-800">{{ $grupo->descripcion }}</td>
                            <td class="py-3 px-6 text-gray-800 flex items-center space-x-2">
                                <span id="codigo-{{ $grupo->id }}">{{ $grupo->codigo }}</span>
                                <button onclick="copiarCodigo('{{ $grupo->id }}')"
                                        class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 text-sm">
                                    Copiar
                                </button>
                            </td>
                                                        <td class="py-3 px-6">
                                <a href="{{ route('grupos.edit', $grupo->id) }}"
                                   class="inline-block px-3 py-1 bg-yellow-500 text-black rounded hover:bg-yellow-600 mr-2">
                                    Editar
                                </a>
                                <a href="{{ route('grupos.delete', $grupo->id) }}"
                                   class="inline-block px-3 py-1 bg-red-500 text-black rounded hover:bg-red-600"
                                   onclick="return confirm('¿Estás seguro de eliminar este grupo?')">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

<script>
    function copiarCodigo(id) {
        const codigo = document.getElementById('codigo-' + id).innerText;
        navigator.clipboard.writeText(codigo).then(function() {
            alert('Código copiado: ' + codigo);
        }, function(err) {
            alert('Error al copiar: ', err);
        });
    }
</script>
