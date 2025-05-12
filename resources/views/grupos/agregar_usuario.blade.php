<x-app-layout>
    <div class="container">
        <h2>Agregar usuario al grupo: {{ $grupo->nombre }}</h2>

        <form method="POST" action="{{ route('grupos.agregar_usuario', $grupo->id) }}">
            @csrf

            <div class="mb-3">
                <label for="usuario_id" class="form-label">Seleccionar Usuario</label>
                <select name="usuario_id" class="form-control" required>
                    <option value="">-- Selecciona un usuario --</option>
                    @foreach ($usuarios as $usuario)
                        <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="es_administrador" value="1" id="es_admin">
                <label class="form-check-label" for="es_admin">
                    Es administrador del grupo
                </label>
            </div>

            <button type="submit" class="btn btn-primary">Agregar</button>
        </form>
    </div>
</x-app-layout>
