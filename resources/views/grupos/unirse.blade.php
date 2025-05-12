<x-app-layout>
    <div class="max-w-md mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-xl font-bold mb-4">Unirse a un grupo</h2>

        @if (session('info'))
            <div class="bg-blue-100 text-blue-800 p-2 rounded mb-4">
                {{ session('info') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 text-red-800 p-2 rounded mb-4">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('grupos.unirse') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="codigo" class="block text-sm font-medium">Código del grupo</label>
                <input type="text" name="codigo" id="codigo" class="w-full border border-gray-300 p-2 rounded mt-1" required>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Unirse
            </button>
        </form>

        <hr class="my-6">
        <h3 class="text-lg font-semibold mb-3">Grupos a los que ya perteneces</h3>

        @if ($grupos->isEmpty())
            <p class="text-gray-600">Aún no perteneces a ningún grupo.</p>
        @else
            <ul class="list-disc list-inside text-gray-800">
                @foreach ($grupos as $grupo)
                    <div class="mt-4 border p-3 rounded">
                        <h3 class="font-semibold">{{ $grupo->nombre }}</h3>
                        <div class="mt-2 space-x-2">
                            <a href="{{ route('grupos.ver_usuarios', $grupo) }}"
                            class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-900 inline-block">
                                Ver usuarios
                            </a>

                            <a href="{{ route('grupos.solicitudes.index', $grupo) }}"
                            class="bg-gray-800 text-white px-4 py-2 rounded hover:bg-blue-700 inline-block">
                                Ver solicitudes
                            </a>
                        </div>
                    </div>
                @endforeach
            </ul>
        @endif


    </div>
</x-app-layout>
