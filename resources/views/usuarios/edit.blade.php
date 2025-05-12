<x-app-layout>
    <div class="container py-5 max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="mb-6 text-3xl font-semibold text-center">Editar Usuario</h1>

        <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="mb-4">
                        <label for="nombres" class="block text-lg font-medium text-gray-700">Nombre completo</label>
                        <input type="text" id="nombres" name="nombres" class="w-full mt-2 px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" required value="{{ old('nombres', $usuario->nombres) }}">
                    </div>
                </div>

                <div>
                    <div class="mb-4">
                        <label for="rol_id" class="block text-lg font-medium text-gray-700">Rol</label>
                        <select name="rol_id" id="rol_id" class="w-full mt-2 px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                            @foreach ($roles as $rol)
                                <option value="{{ $rol->id }}" {{ $usuario->rol_id == $rol->id ? 'selected' : '' }}>{{ $rol->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex justify-between mt-6">
                <button type="submit" class="bg-blue-500 text-gray-700 px-6 py-2 rounded-md hover:bg-blue-600 focus:outline-none">Actualizar</button>
                <a href="{{ route('usuarios.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-md hover:bg-gray-400 focus:outline-none">Cancelar</a>
            </div>
        </form>
    </div>
</x-app-layout>
