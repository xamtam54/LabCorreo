<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::with('rol', 'user')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $roles = Rol::all();
        return view('usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'rol_id' => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        $user->assignRole($request->rol_id);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado');
    }

    public function edit(Usuario $usuario)
    {
        $roles = Rol::all();
        $usuario->load('grupos');
        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        $validated = $request->validate([
            'nombres' => 'required|string',
            'rol_id' => 'required|exists:roles,id',
        ]);

        $usuario->update([
            'nombres' => $validated['nombres'],
            'rol_id' => $validated['rol_id'],
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado');
    }

    public function destroy(Usuario $usuario)
    {
        $usuario->delete();
        $usuario->user->delete(); // también elimina de tabla users
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado');
    }
}
