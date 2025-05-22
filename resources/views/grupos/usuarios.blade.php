<x-app-layout>
    <div class="min-h-screen flex flex-col mx-auto p-6 bg-gray-50">
        <!-- Contenedor principal -->

        <div class="flex justify-start mb-4">
            <x-button-back href="{{ route('grupos.unirse') }}" />
        </div>
        <div class="flex-grow bg-white p-6 rounded-lg shadow-lg overflow-x-auto">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">
                Usuarios del grupo: <span class="text-blue-600">{{ $grupo->nombre }}</span>
            </h2>

            <table class="w-full table-auto text-center border border-gray-300 rounded-lg">
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
                                <td class="px-4 py-3 text-center">
                                    <div x-data="{ open: false }" class="relative inline-block text-left">
                                        <button
                                            @click="open = !open"
                                            @keydown.escape="open = false"
                                            type="button"
                                            class="inline-flex justify-center w-28 rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700
                                                hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-indigo-500"
                                            aria-expanded="true"
                                            aria-haspopup="true"
                                        >
                                            Acciones
                                            <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>

                                        <div
                                            x-show="open"
                                            @click.away="open = false"
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            class="origin-top-right absolute right-0 mt-2 w-44 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                                            role="menu"
                                            aria-orientation="vertical"
                                            aria-labelledby="menu-button"
                                            tabindex="-1"
                                        >
                                            <div class="py-1" role="none">
                                                @if (!$usuario->pivot->es_administrador)
                                                    <form action="{{ route('grupos.bloquear', [$grupo, $usuario]) }}" method="POST" role="menuitem" tabindex="-1">
                                                        @csrf
                                                        <button
                                                            type="submit"
                                                            class="block px-4 py-2 text-sm text-black hover:bg-blue-50 w-full text-left"
                                                            @click="open = false"
                                                        >
                                                            Bloquear
                                                        </button>
                                                    </form>

                                                    <form action="{{ route('grupos.expulsar', [$grupo, $usuario]) }}" method="POST" role="menuitem" tabindex="-1">
                                                        @csrf
                                                        <button
                                                            type="submit"
                                                            class="block px-4 py-2 text-sm text-black hover:bg-blue-50 w-full text-left"
                                                            @click="open = false"
                                                        >
                                                            Expulsar
                                                        </button>
                                                    </form>

                                                    <form action="{{ route('grupos.hacer_admin', [$grupo, $usuario]) }}" method="POST" role="menuitem" tabindex="-1">
                                                        @csrf
                                                        <button
                                                            type="submit"
                                                            class="block px-4 py-2 text-sm text-black hover:bg-blue-50 w-full text-left"
                                                            @click="open = false"
                                                        >
                                                            Hacer Admin
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('grupos.denigrar', [$grupo, $usuario]) }}" method="POST" role="menuitem" tabindex="-1">
                                                        @csrf
                                                        <button
                                                            type="submit"
                                                            class="block px-4 py-2 text-sm text-black hover:bg-blue-50 w-full text-left disabled:opacity-50 disabled:cursor-not-allowed"
                                                            @click="open = false"
                                                            @if($grupo->creador_id == $usuario->id) disabled @endif
                                                        >
                                                            Degradar
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
