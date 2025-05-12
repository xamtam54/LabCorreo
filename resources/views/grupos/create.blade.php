<x-app-layout>
    <div class="container mx-auto p-6 bg-white shadow-lg rounded-lg">
        <h1 class="text-3xl font-semibold mb-6 text-center">Crear Grupo</h1>

        <form method="POST" action="{{ route('grupos.store') }}">
            @csrf

            <div class="mb-4">
                <label for="nombre" class="block text-lg font-medium text-gray-700">Nombre del Grupo</label>
                <input type="text" class="form-control mt-2 p-3 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" id="nombre" name="nombre" required>
            </div>

            <div class="mb-4">
                <label for="descripcion" class="block text-lg font-medium text-gray-700">Descripción</label>
                <textarea class="form-control mt-2 p-3 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" id="descripcion" name="descripcion"></textarea>
            </div>

            <div class="mt-6 text-center">
                <button type="submit" class="w-full py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    Crear Grupo
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
