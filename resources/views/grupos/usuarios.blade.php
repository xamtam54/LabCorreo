<x-app-layout>
    <div class="max-w-6xl mx-auto bg-white p-8 rounded-2xl shadow-lg">
        <h2 class="text-2xl font-semibold text-center text-gray-800 mb-6">
            Usuarios del grupo: <span class="text-blue-600">{{ $grupo->nombre }}</span>
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full text-center border border-gray-300 rounded-lg">
                <thead class="bg-gray-100 text-gray-800">
                    <tr>
                        <th class="px-4 py-3 border-b">Nombre</th>
                        <th class="px-4 py-3 border-b">Correo</th>
                        <th class="px-4 py-3 border-b">Administrador</th>
                        <th class="px-4 py-3 border-b">Bloqueado</th>
                        @if ($esAdmin)
                            <th class="px-4 py-3 border-b">Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @foreach ($usuarios as $usuario)
                        <tr class="hover:bg-gray-50 border-t">
                            <td class="px-4 py-3">{{ $usuario->user->name }}</td>
                            <td class="px-4 py-3">{{ $usuario->user->email }}</td>
                            <td class="px-4 py-3">
                                <span class="font-semibold {{ $usuario->pivot->es_administrador ? 'text-green-700' : 'text-gray-600' }}">
                                    {{ $usuario->pivot->es_administrador ? 'Sí' : 'No' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-semibold {{ $usuario->pivot->bloqueado ? 'text-red-600' : 'text-gray-600' }}">
                                    {{ $usuario->pivot->bloqueado ? 'Sí' : 'No' }}
                                </span>
                            </td>
                                @if ($esAdmin)
                                    <td class="px-4 py-3 space-y-2 flex flex-col items-center justify-center">
                                        {{-- Si NO es administrador, puede ser bloqueado, expulsado o promovido --}}
                                        @if (!$usuario->pivot->es_administrador)
                                            <form action="{{ route('grupos.bloquear', [$grupo, $usuario]) }}" method="POST">
                                                @csrf
                                                <button class="bg-yellow-400 hover:bg-yellow-500 text-black px-4 py-1 rounded text-sm w-28">Bloquear</button>
                                            </form>

                                            <form action="{{ route('grupos.expulsar', [$grupo, $usuario]) }}" method="POST">
                                                @csrf
                                                <button class="bg-red-500 hover:bg-red-600 text-black px-4 py-1 rounded text-sm w-28">Expulsar</button>
                                            </form>

                                            <form action="{{ route('grupos.hacer_admin', [$grupo, $usuario]) }}" method="POST">
                                                @csrf
                                                <button class="bg-green-500 hover:bg-green-600 text-black px-4 py-1 rounded text-sm w-28">Hacer Admin</button>
                                            </form>
                                        @else
                                            {{-- Si ES administrador, permitir denigrarlo excepto si es el creador --}}
                                            <form action="{{ route('grupos.denigrar', [$grupo, $usuario]) }}" method="POST">
                                                @csrf
                                                <button class="bg-red-500 hover:bg-red-600 text-black px-4 py-1 rounded text-sm w-28"
                                                    @if($grupo->creador_id == $usuario->id) disabled @endif>
                                                    Degradar
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                @endif


                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
