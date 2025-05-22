<x-app-layout>
    <div class="min-h-screen bg-gray-50 py-8 px-6">
        <div class="container mx-auto">
            <h1 class="text-4xl font-extrabold mb-8 text-center text-gray-800">Grupos</h1>
<div class="mb-6 flex justify-end">
    <x-blue-button text="Crear nuevo grupo"
        onclick="window.location='{{ route('grupos.create') }}'"
    />
</div>
            <div class="overflow-x-auto bg-white rounded-lg shadow-md border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-6 text-left text-sm font-semibold text-gray-700">Nombre</th>
                            <th class="py-3 px-6 text-left text-sm font-semibold text-gray-700">Descripción</th>
                            <th class="py-3 px-6 text-left text-sm font-semibold text-gray-700">Código</th>
                            <th class="py-3 px-6 text-left text-sm font-semibold text-gray-700">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($grupos as $grupo)
                            <tr class="hover:bg-indigo-50">
                                <td class="py-4 px-6 text-gray-800">{{ $grupo->nombre }}</td>
                                <td class="py-4 px-6 text-gray-700">{{ $grupo->descripcion }}</td>
                                <td class="py-4 px-6 text-gray-700 flex items-center space-x-2">
                                    <span id="codigo-{{ $grupo->id }}" class="font-mono">{{ $grupo->codigo }}</span>
                                    <x-button-copy onclick="copiarCodigo('{{ $grupo->id }}')" />
                                </td>
                                <td class="py-4">
                                    <div class="flex items-center space-x-3">
                                        <x-button-edit href="{{ route('grupos.edit', $grupo->id) }}" />
                                        <x-button-delete action="{{ route('grupos.delete', $grupo->id) }}" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

    <script>
        function copiarCodigo(id) {
            const codigo = document.getElementById('codigo-' + id).innerText;
            navigator.clipboard.writeText(codigo)
                .then(() => alert('Código copiado: ' + codigo))
                .catch(err => alert('Error al copiar: ' + err));
        }
    </script>
