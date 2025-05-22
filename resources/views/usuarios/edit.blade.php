<x-app-layout>
    <div class="min-h-screen bg-gray-50 py-8 px-6">
        <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-md border border-gray-200">
            <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Editar Usuario</h1>

            <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nombres" class="block text-sm font-semibold text-gray-700 mb-2">Nombre completo</label>
                        <input type="text" id="nombres" name="nombres"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                               required value="{{ old('nombres', $usuario->nombres) }}">
                    </div>

                    <div>
                        <label for="rol_id" class="block text-sm font-semibold text-gray-700 mb-2">Rol</label>
                        <select name="rol_id" id="rol_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                required>
                            @foreach ($roles as $rol)
                                <option value="{{ $rol->id }}" {{ $usuario->rol_id == $rol->id ? 'selected' : '' }}>
                                    {{ $rol->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end mt-8 space-x-4">
                    <x-gray-button text="Cancelar" :href="route('usuarios.index')" />
                    <x-blue-button text="Actualizar" type="submit" />
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
