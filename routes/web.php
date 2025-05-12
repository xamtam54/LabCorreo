<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\SolicitudExportController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\GrupoController;

use App\Http\Middleware\RolMiddleware;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// solicitudes

Route::prefix('solicitudes')->name('solicitudes.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/index', [SolicitudController::class, 'index'])->name('index');                         // solicitudes.index
    Route::get('/create', [SolicitudController::class, 'create'])->name('create');                      // solicitudes.create
    Route::post('/store', [SolicitudController::class, 'store'])->name('store');                        // solicitudes.store
    Route::get('/edit/{solicitud}', [SolicitudController::class, 'edit'])->name('edit');                // solicitudes.edit
    Route::put('/update/{solicitud}', [SolicitudController::class, 'update'])->name('update');          // solicitudes.update
    Route::delete('/delete/{solicitud}', [SolicitudController::class, 'destroy'])->name('destroy');     // solicitudes.destroy

    Route::get('/dashboard', [SolicitudController::class, 'dashboard'])->name('dashboard');             // solicitudes.dashboard
    Route::get('/overview', [SolicitudController::class, 'overview'])->name('overview');

    // Exportaciones
    Route::get('/export/csv', [SolicitudExportController::class, 'exportCSV'])->name('export.csv');
    Route::get('/export/excel', [SolicitudExportController::class, 'exportExcel'])->name('export.excel');
});

// Rutas para grupos
Route::middleware(['auth', RolMiddleware::class . ':Administrador,Gestor_grupos'])->group(function () {
    // CRUD principal
    Route::get('grupos', [GrupoController::class, 'index'])->name('grupos.index');
    Route::get('grupos/create', [GrupoController::class, 'create'])->name('grupos.create');
    Route::post('grupos', [GrupoController::class, 'store'])->name('grupos.store');
    Route::get('grupos/{grupo}/edit', [GrupoController::class, 'edit'])->name('grupos.edit');
    Route::put('grupos/{grupo}', [GrupoController::class, 'update'])->name('grupos.update');
    Route::delete('grupos/{grupo}', [GrupoController::class, 'destroy'])->name('grupos.delete');

    // Funciones adicionales
    Route::post('grupos/{grupo}/bloquear/{usuario}', [GrupoController::class, 'bloquearUsuario'])->name('grupos.bloquear');
    Route::post('grupos/{grupo}/expulsar/{usuario}', [GrupoController::class, 'expulsarUsuario'])->name('grupos.expulsar');
    Route::get('grupos/{grupo}/agregar-usuario', [GrupoController::class, 'agregarUsuarioForm'])->name('grupos.agregar_usuario_form');
    Route::post('grupos/{grupo}/agregar-usuario', [GrupoController::class, 'agregarUsuario'])->name('grupos.agregar_usuario');
});


// Unirse a grupos (usuarios autenticados y verificados)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('grupos/unirse', [GrupoController::class, 'mostrarFormularioUnirse'])->name('grupos.unirse.form');
    Route::post('grupos/unirse', [GrupoController::class, 'unirsePorCodigo'])->name('grupos.unirse');
});

// Usuarios (Administradores o Gestores de grupos)
Route::middleware(['auth', RolMiddleware::class . ':Administrador'])->group(function () {
    Route::get('usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::get('usuarios/{usuario}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');
    Route::get('usuarios/{usuario}/delete', [UsuarioController::class, 'delete'])->name('usuarios.destroy');
    Route::get('usuarios/{usuario}/asignar-rol', [UsuarioController::class, 'formularioAsignarRol'])->name('usuarios.formularioAsignarRol');
    Route::post('usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');

});

Route::get('grupos/{grupo}/usuarios', [GrupoController::class, 'verUsuarios'])
    ->middleware(['auth', RolMiddleware::class . ':Administrador,Gestor_grupos,Miembro_grupo'])
    ->name('grupos.ver_usuarios');

Route::post('/grupos/{grupo}/hacer-admin/{usuario}', [GrupoController::class, 'hacerAdmin'])->name('grupos.hacer_admin');
Route::post('/grupos/{grupo}/denigrar/{usuario}', [GrupoController::class, 'denigrar'])->name('grupos.denigrar');

Route::prefix('grupos/{grupo}/solicitudes')->name('grupos.solicitudes.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [SolicitudController::class, 'index'])->name('index');                  // grupos.solicitudes.index
    Route::get('/crear', [SolicitudController::class, 'create'])->name('create');           // grupos.solicitudes.create
    Route::post('/guardar', [SolicitudController::class, 'store'])->name('store');          // grupos.solicitudes.store
    Route::get('/editar/{solicitud}', [SolicitudController::class, 'edit'])->name('edit');  // grupos.solicitudes.edit
    Route::put('/actualizar/{solicitud}', [SolicitudController::class, 'update'])->name('update'); // grupos.solicitudes.update
    Route::delete('/eliminar/{solicitud}', [SolicitudController::class, 'destroy'])->name('destroy'); // grupos.solicitudes.destroy

    Route::get('/resumen', [SolicitudController::class, 'overview'])->name('overview');     // grupos.solicitudes.overview
});


require __DIR__.'/auth.php';


