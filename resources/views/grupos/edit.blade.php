<x-app-layout>
    <div class="container mx-auto p-6 bg-white shadow-lg rounded-lg">
        <h1 class="text-3xl font-semibold mb-6 text-center">Editar Grupo: {{ $grupo->nombre }}</h1>

        <form method="POST" action="{{ route('grupos.update', $grupo->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="nombre" class="block text-lg font-medium text-gray-700">Nombre del Grupo</label>
                <input type="text" class="mt-2 p-3 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" id="nombre" name="nombre" value="{{ old('nombre', $grupo->nombre) }}" required>
            </div>

            <div class="mb-4">
                <label for="descripcion" class="block text-lg font-medium text-gray-700">Descripción</label>
                <textarea class="mt-2 p-3 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" id="descripcion" name="descripcion">{{ old('descripcion', $grupo->descripcion) }}</textarea>
            </div>

            <div class="mt-6 text-center">
                <button type="submit" class="w-full py-3 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    Actualizar Grupo
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
