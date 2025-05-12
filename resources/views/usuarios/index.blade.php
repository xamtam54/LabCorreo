<x-app-layout>
    <div class="container mx-auto py-6">
        <h1 class="text-3xl font-semibold text-center mb-6">Usuarios</h1>

        <a href="{{ route('usuarios.create') }}" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 mb-4 inline-block">Crear nuevo usuario</a>

        @if(session('success'))
            <div class="bg-green-500 text-white p-4 mb-4 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto bg-white p-4 rounded-lg shadow-md">
            <table class="min-w-full table-auto border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Nombre</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Correo</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Rol</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usuarios as $usuario)
                        <tr class="border-t border-gray-200">
                            <td class="px-6 py-4 text-sm text-gray-800">{{ $usuario->nombres }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $usuario->user->email }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $usuario->rol->nombre }}</td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('usuarios.edit', $usuario->id) }}" class="bg-yellow-500 text-black py-1 px-2 rounded hover:bg-yellow-600">Editar</a>
                                <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 text-black py-1 px-2 rounded hover:bg-red-600 ml-2" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
