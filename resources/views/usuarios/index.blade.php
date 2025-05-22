<x-app-layout>
    <div class="min-h-screen bg-gray-50 py-8 px-6">
        <div class="container mx-auto">
            <h1 class="text-4xl font-extrabold mb-8 text-center text-gray-800">Gestión de Usuarios</h1>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded mb-6 shadow">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-6 flex justify-end">
                <x-blue-button text="Crear nuevo usuario"
                    onclick="window.location='{{ route('usuarios.create') }}'"
                />
            </div>

            <div class="overflow-x-auto bg-white rounded-lg shadow-md border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 px-6 text-left text-sm font-semibold text-gray-700">Nombre</th>
                            <th class="py-3 px-6 text-left text-sm font-semibold text-gray-700">Correo</th>
                            <th class="py-3 px-6 text-left text-sm font-semibold text-gray-700">Rol</th>
                            <th class="py-3 px-6 text-left text-sm font-semibold text-gray-700">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($usuarios as $usuario)
                            <tr class="hover:bg-indigo-50">
                                <td class="py-4 px-6 text-gray-800">{{ $usuario->nombres }}</td>
                                <td class="py-4 px-6 text-gray-700">{{ $usuario->user->email }}</td>
                                <td class="py-4 px-6 text-gray-700">{{ $usuario->rol->nombre }}</td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center space-x-3">
                                        <x-button-edit href="{{ route('usuarios.edit', $usuario->id) }}" />

                                        <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST"
                                              onsubmit="return confirm('¿Estás seguro de eliminar este usuario?')"
                                              class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <x-button-delete type="submit" text="Eliminar" />
                                        </form>
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
