<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class GrupoController extends Controller
{
    public function index()
    {
        // Obtener todos los grupos con sus usuarios y el creador
        $grupos = Grupo::with('usuarios', 'creador')->get();
        return view('grupos.index', compact('grupos'));
    }

    public function create()
    {
        return view('grupos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|unique:grupos,nombre',
        ]);

        try {
            $grupo = Grupo::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'contrasena' => null, // Hash::make($request->password)
                'creador_id' => Auth::id(),
            ]);

            $grupo->usuarios()->attach(Auth::id(), [
                'es_administrador' => true,
                'bloqueado' => false,
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Hubo un problema al crear el grupo.']);
        }

        return redirect()->route('grupos.index')->with('success', 'Grupo creado correctamente');
    }

    public function edit(Grupo $grupo)
    {
        return view('grupos.edit', compact('grupo'));
    }

    public function update(Request $request, Grupo $grupo)
    {
        // Validación de los datos
        $request->validate([
            'nombre' => 'required|string',
            'descripcion' => 'nullable|string',
        ]);

        // Actualizar los datos del grupo
        $grupo->nombre = $request->input('nombre');
        $grupo->descripcion = $request->input('descripcion');

        $grupo->save();

        return redirect()->route('grupos.index')->with('success', 'Grupo actualizado');
    }

    public function destroy(Grupo $grupo)
    {
        $grupo->delete();
        return redirect()->route('grupos.index')->with('success', 'Grupo eliminado');
    }

    public function verUsuarios(Grupo $grupo)
    {
        $usuarios = $grupo->usuarios()
                        ->withPivot('es_administrador', 'bloqueado')
                        ->with('user')
                        ->get();

        $usuarioActual = auth::user();

        $esAdmin = $grupo->usuarios()
                        ->where('usuario_id', $usuarioActual->id)
                        ->wherePivot('es_administrador', true)
                        ->exists();

        return view('grupos.usuarios', compact('grupo', 'usuarios', 'esAdmin'));
    }


    public function bloquearUsuario(Grupo $grupo, Usuario $usuario)
    {
        $grupo->usuarios()->updateExistingPivot($usuario->id, ['bloqueado' => true]);
        return back()->with('success', 'El usuario fue bloqueado');
    }

    public function expulsarUsuario(Grupo $grupo, Usuario $usuario)
    {
        $grupo->usuarios()->detach($usuario->id);
        return back()->with('success', 'El usuario fue expulsado');
    }
    public function hacerAdmin(Grupo $grupo, Usuario $usuario)
    {
        $grupo->usuarios()->updateExistingPivot($usuario->id, ['es_administrador' => true]);
        return back()->with('success', 'El usuario ahora es administrador.');
    }
    public function denigrar(Grupo $grupo, Usuario $usuario)
    {
        // Asegurarse de que el creador no pueda ser denigrado
        if ($grupo->creador_id === $usuario->id) {
            return back()->with('error', 'El creador del grupo no puede ser denigrado.');
        }

        // Actualizar la relación en la tabla intermedia
        $grupo->usuarios()->updateExistingPivot($usuario->id, ['es_administrador' => false]);

        return back()->with('success', 'El usuario ya no es administrador.');
    }

    public function agregarUsuarioForm(Grupo $grupo)
    {
        $usuarios = Usuario::all(); // o puedes filtrar según lo que necesites
        return view('grupos.agregar_usuario', compact('grupo', 'usuarios'));
    }

    public function agregarUsuario(Request $request, Grupo $grupo)
    {
        $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'es_administrador' => 'boolean'
        ]);

        $grupo->usuarios()->attach($request->usuario_id, [
            'es_administrador' => $request->es_administrador ?? false,
            'bloqueado' => false
        ]);

        return redirect()->route('grupos.index')->with('success', 'Usuario agregado al grupo');
    }

    public function unirsePorCodigo(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string'
        ]);

        $grupo = Grupo::where('codigo', $request->codigo)->first();

        if (!$grupo) {
            return back()->withErrors(['codigo' => 'Código inválido.']);
        }

        $usuario = Auth::user();

        if ($grupo->usuarios()->where('usuario_id', $usuario->id)->exists()) {
            return back()->with('info', 'Ya estás en este grupo.');
        }

        $grupo->usuarios()->attach($usuario->id, [
            'es_administrador' => false,
            'bloqueado' => false
        ]);
        return back()->with('success', 'Te has unido al grupo exitosamente.');
    }
    public function mostrarFormularioUnirse()
    {
        $usuario = auth::user()->Usuario;
        $grupos = $usuario ? $usuario->grupos : collect();
        return view('grupos.unirse', compact('grupos'));
    }

}

