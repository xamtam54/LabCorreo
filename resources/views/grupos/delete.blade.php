<x-app-layout>
    <div class="container">
        <h1>Eliminar Grupo</h1>

        <p>¿Estás seguro de que deseas eliminar el grupo "{{ $grupo->nombre }}"?</p>

        <form method="POST" action="{{ route('grupos.destroy', $grupo->id) }}">
            @csrf
            @method('DELETE')

            <button type="submit" class="btn btn-danger">Eliminar</button>
            <a href="{{ route('grupos.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</x-app-layout>
