<x-app-layout>
    <div class="min-h-screen bg-gray-50 py-8 px-6">
        <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-md border border-gray-200">
            <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">Crear Usuario</h1>

            <form action="{{ route('usuarios.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nombre completo</label>
                        <input type="text" id="name" name="name"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                               required value="{{ old('name') }}">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Correo electrónico</label>
                        <input type="email" id="email" name="email"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                               required value="{{ old('email') }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Contraseña</label>
                        <input type="password" id="password" name="password"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                               required>
                    </div>

                    <div>
                        <label for="rol_id" class="block text-sm font-semibold text-gray-700 mb-2">Rol</label>
                        <select name="rol_id" id="rol_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                required>
                            @foreach ($roles as $rol)
                                <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end mt-8 space-x-4">
                    <x-gray-button text="Cancelar" :href="route('usuarios.index')" />
                    <x-blue-button text="Crear" type="submit" />
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
