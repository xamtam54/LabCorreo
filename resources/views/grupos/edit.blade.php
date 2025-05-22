<x-app-layout>
    <div class="min-h-screen bg-gray-50 py-8 px-6">
        <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md border border-gray-200 p-6">
            <h1 class="text-3xl font-extrabold mb-8 text-center text-gray-800">
                Editar Grupo: {{ $grupo->nombre }}
            </h1>

            <form method="POST" action="{{ route('grupos.update', $grupo->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label for="nombre" class="block text-sm font-semibold text-gray-700">Nombre del Grupo</label>
                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        value="{{ old('nombre', $grupo->nombre) }}"
                        required
                        class="mt-2 block w-full rounded-md border border-gray-300 px-4 py-3
                               text-gray-900 placeholder-gray-400
                               focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400 focus:outline-none"
                    >
                </div>

                <div class="mb-6">
                    <label for="descripcion" class="block text-sm font-semibold text-gray-700">Descripción</label>
                    <textarea
                        id="descripcion"
                        name="descripcion"
                        rows="4"
                        class="mt-2 block w-full rounded-md border border-gray-300 px-4 py-3
                               text-gray-900 placeholder-gray-400
                               focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400 focus:outline-none"
                    >{{ old('descripcion', $grupo->descripcion) }}</textarea>
                </div>

                <div class="flex justify-center space-x-4 mt-6">
                    <x-gray-button
                        text="Cancelar"
                        type="button"
                        onclick="window.location='{{ route('grupos.index') }}'"
                        class="w-full max-w-xs py-3"
                    />
                    <x-blue-button
                        text="Actualizar Grupo"
                        type="submit"
                        class="w-full max-w-xs py-3"
                    />
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
