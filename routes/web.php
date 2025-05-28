<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\SolicitudExportController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\DocumentoController;

use App\Http\Middleware\RolMiddleware;


Route::get('/', function () {
    return view('login');
})->name('inicio');  // o cualquier nombre que desees



// Perfil - requiere autenticación
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Exportaciones de solicitudes - auth + verified
Route::prefix('solicitudes')->name('solicitudes.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/overview', [SolicitudController::class, 'overview'])->name('overview');
    Route::get('/dashboard', [SolicitudController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/solicitudes-prioridad', [SolicitudController::class, 'soloPrioridad'])->name('soloPrioridad');

    Route::get('/export/csv', [SolicitudExportController::class, 'exportCSV'])->name('export.csv');
    Route::get('/export/excel', [SolicitudExportController::class, 'exportExcel'])->name('export.excel');

});

// Rutas para Grupos con middleware de roles
Route::middleware(['auth', RolMiddleware::class . ':Administrador,Gestor_grupos'])->prefix('grupos')->name('grupos.')->group(function () {

    // CRUD grupos
    Route::get('/', [GrupoController::class, 'index'])->name('index');
    Route::get('create', [GrupoController::class, 'create'])->name('create');
    Route::post('/', [GrupoController::class, 'store'])->name('store');
    Route::get('{grupo}/edit', [GrupoController::class, 'edit'])->name('edit');
    Route::put('{grupo}', [GrupoController::class, 'update'])->name('update');
    Route::delete('{grupo}', [GrupoController::class, 'destroy'])->name('delete');

    // Acciones sobre usuarios en grupos
    Route::post('{grupo}/bloquear/{usuario}', [GrupoController::class, 'bloquearUsuario'])->name('bloquear');
    Route::post('{grupo}/expulsar/{usuario}', [GrupoController::class, 'expulsarUsuario'])->name('expulsar');

    // Agregar usuario
    Route::get('{grupo}/agregar-usuario', [GrupoController::class, 'agregarUsuarioForm'])->name('agregar_usuario_form');
    Route::post('{grupo}/agregar-usuario', [GrupoController::class, 'agregarUsuario'])->name('agregar_usuario');
});

// Rutas para unirse a grupos - usuarios autenticados y verificados
Route::middleware(['auth', 'verified'])->prefix('grupos')->name('grupos.')->group(function () {
    Route::get('unirse', [GrupoController::class, 'mostrarFormularioUnirse'])->name('unirse.form');
    Route::post('unirse', [GrupoController::class, 'unirsePorCodigo'])->name('unirse');
});

// Usuarios (solo Administrador)
Route::middleware(['auth', RolMiddleware::class . ':Administrador'])->prefix('usuarios')->name('usuarios.')->group(function () {
    Route::get('/', [UsuarioController::class, 'index'])->name('index');
    Route::get('create', [UsuarioController::class, 'create'])->name('create');
    Route::post('/', [UsuarioController::class, 'store'])->name('store');

    Route::get('{usuario}/edit', [UsuarioController::class, 'edit'])->name('edit');
    Route::put('{usuario}/update', [UsuarioController::class, 'update'])->name('update'); // Nota: normalmente update es PATCH o PUT, no GET
    Route::delete('{usuario}/destroy', [UsuarioController::class, 'destroy'])->name('destroy'); // Nota: eliminar por GET no es recomendable
    Route::get('{usuario}/asignar-rol', [UsuarioController::class, 'formularioAsignarRol'])->name('formularioAsignarRol');
});

// Ver usuarios de un grupo con distintos roles permitidos
Route::get('grupos/{grupo}/usuarios', [GrupoController::class, 'verUsuarios'])
    ->middleware(['auth', RolMiddleware::class . ':Administrador,Gestor_grupos,Miembro_grupo'])
    ->name('grupos.ver_usuarios');

// Asignar y denigrar admin en grupo (sin middleware explícito, quizás añadir)
Route::post('grupos/{grupo}/hacer-admin/{usuario}', [GrupoController::class, 'hacerAdmin'])->name('grupos.hacer_admin');
Route::post('grupos/{grupo}/denigrar/{usuario}', [GrupoController::class, 'denigrar'])->name('grupos.denigrar');

// Rutas para solicitudes dentro de grupos (auth + verified)
Route::prefix('grupos/{grupo}/solicitudes')->name('grupos.solicitudes.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [SolicitudController::class, 'index'])->name('index');
    Route::get('/crear', [SolicitudController::class, 'create'])->name('create');
    Route::post('/guardar', [SolicitudController::class, 'store'])->name('store');
    Route::get('/editar/{solicitud}', [SolicitudController::class, 'edit'])->name('edit');
    Route::put('/actualizar/{solicitud}', [SolicitudController::class, 'update'])->name('update');
    Route::delete('/eliminar/{solicitud}', [SolicitudController::class, 'destroy'])->name('destroy');

    Route::patch('/{solicitud}/completar', [SolicitudController::class, 'completar'])->name('completar');
    Route::patch('/{solicitud}/revertir', [SolicitudController::class, 'revertir'])->name('revertir');

    Route::get('/{solicitud}', [SolicitudController::class, 'show'])->name('show');           // Mostrar detalle (la que quieres)
    Route::get('/documento/{id}/descargar', [DocumentoController::class, 'descargar'])->name('documento.descargar');
    Route::get('/documento/{id}/ver', [DocumentoController::class, 'ver'])->name('documento.ver');


});





require __DIR__.'/auth.php';


