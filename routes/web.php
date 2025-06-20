<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\SolicitudExportController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\HomeController;

use App\Http\Middleware\RolMiddleware;
use App\Http\Middleware\VerificarMiembroGrupo;
use App\Http\Middleware\AutorizarAdministradorGrupo;
/*
Route::get('/', function () {
    return view('login');
})->name('inicio');
*/

Route::match(['get', 'post'], '/', [HomeController::class, 'index']);


// ============================
// RUTAS DE PERFIL - requiere autenticación
// ============================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ============================
// RUTAS DE SOLICITUDES GENERALES - requiere auth + verified
// ============================
Route::prefix('solicitudes')->name('solicitudes.')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/overview', [SolicitudController::class, 'overview'])->name('overview');
    Route::get('/dashboard', [SolicitudController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/solicitudes-prioridad', [SolicitudController::class, 'soloPrioridad'])->name('soloPrioridad');

    // Exportaciones
    Route::get('/export/csv', [SolicitudExportController::class, 'exportCSV'])->name('export.csv');
    Route::get('/export/excel', [SolicitudExportController::class, 'exportExcel'])->name('export.excel');

});

// ============================
// RUTAS DE GRUPOS (Gestión completa) - requiere roles: Administrador o Gestor_grupos
// ============================
Route::middleware(['auth', RolMiddleware::class . ':Administrador,Gestor_grupos'])->prefix('grupos')->name('grupos.')->group(function () {
    // CRUD grupos
    Route::get('/', [GrupoController::class, 'index'])->name('index');
    Route::get('create', [GrupoController::class, 'create'])->name('create');
    Route::post('/', [GrupoController::class, 'store'])->name('store');
    Route::get('{grupo}/edit', [GrupoController::class, 'edit'])->name('edit');
    Route::put('{grupo}', [GrupoController::class, 'update'])->name('update');
    Route::delete('{grupo}', [GrupoController::class, 'destroy'])->name('delete');

    // Gestión de usuarios en grupos
    Route::post('{grupo}/bloquear/{usuario}', [GrupoController::class, 'bloquearUsuario'])->name('bloquear');
    Route::post('{grupo}/expulsar/{usuario}', [GrupoController::class, 'expulsarUsuario'])->name('expulsar');

    // Agregar usuario
    Route::get('{grupo}/agregar-usuario', [GrupoController::class, 'agregarUsuarioForm'])->name('agregar_usuario_form');
    Route::post('{grupo}/agregar-usuario', [GrupoController::class, 'agregarUsuario'])->name('agregar_usuario');
});

// ============================
// RUTAS DE UNIÓN A GRUPOS - requiere auth + verified
// ============================
Route::middleware(['auth', 'verified'])->prefix('grupos')->name('grupos.')->group(function () {
    Route::get('unirse', [GrupoController::class, 'mostrarFormularioUnirse'])->name('unirse.form');
    Route::post('unirse', [GrupoController::class, 'unirsePorCodigo'])->name('unirse');
});

// ============================
// RUTAS DE USUARIOS - solo Administrador
// ============================
Route::middleware(['auth', RolMiddleware::class . ':Administrador'])->prefix('usuarios')->name('usuarios.')->group(function () {
    Route::get('/', [UsuarioController::class, 'index'])->name('index');
    Route::get('create', [UsuarioController::class, 'create'])->name('create');
    Route::post('/', [UsuarioController::class, 'store'])->name('store');

    Route::get('{usuario}/edit', [UsuarioController::class, 'edit'])->name('edit');
    Route::put('{usuario}/update', [UsuarioController::class, 'update'])->name('update');
    Route::delete('{usuario}/destroy', [UsuarioController::class, 'destroy'])->name('destroy');

    Route::get('{usuario}/asignar-rol', [UsuarioController::class, 'formularioAsignarRol'])->name('formularioAsignarRol');
});



// ============================
// RUTAS DE ASIGNACIÓN DE ROLES EN GRUPOS - requiere ser admin del grupo
// ============================
Route::post('grupos/{grupo}/hacer-admin/{usuario}', [GrupoController::class, 'hacerAdmin'])
    ->middleware(['auth', AutorizarAdministradorGrupo::class])
    ->name('grupos.hacer_admin');

Route::post('grupos/{grupo}/denigrar/{usuario}', [GrupoController::class, 'denigrar'])
    ->middleware(['auth', AutorizarAdministradorGrupo::class])
    ->name('grupos.denigrar');

// ============================
// VER USUARIOS DE UN GRUPO - requiere ser miembro o tener rol adecuado
// ============================
Route::get('grupos/{grupo}/usuarios', [GrupoController::class, 'verUsuarios'])
    ->middleware([
        'auth',
        RolMiddleware::class . ':Administrador,Gestor_grupos,Miembro_grupo',
        VerificarMiembroGrupo::class
    ])
    ->name('grupos.ver_usuarios');

// ============================
// RUTAS DE SOLICITUDES DENTRO DE GRUPOS - requiere auth + verified + miembro del grupo
// ============================
Route::prefix('grupos/{grupo}/solicitudes')
    ->name('grupos.solicitudes.')->middleware([
    'auth',
    'verified',
    VerificarMiembroGrupo::class,
    ])->group(function () {

    // Documentos
    Route::get('/documento/{documento}/descargar', [DocumentoController::class, 'descargar'])->name('documento.descargar');
    Route::get('/documento/{documento}/ver', [DocumentoController::class, 'ver'])->name('documento.ver');
    Route::delete('/documento/{documento}/eliminar', [DocumentoController::class, 'eliminar'])->name('documento.eliminar');

    // CRUD de solicitudes
    Route::get('/', [SolicitudController::class, 'index'])->name('index');
    Route::get('/crear', [SolicitudController::class, 'create'])->name('create');
    Route::post('/guardar', [SolicitudController::class, 'store'])->name('store');
    Route::get('/editar/{solicitud}', [SolicitudController::class, 'edit'])->name('edit');
    Route::put('/actualizar/{solicitud}', [SolicitudController::class, 'update'])->name('update');
    Route::delete('/eliminar/{solicitud}', [SolicitudController::class, 'destroy'])->name('destroy');

    // Acciones de estado
    Route::patch('/{solicitud}/completar', [SolicitudController::class, 'completar'])->name('completar');
    Route::patch('/{solicitud}/revertir', [SolicitudController::class, 'revertir'])->name('revertir');

    // Detalle
    Route::get('/{solicitud}', [SolicitudController::class, 'show'])->name('show');
});


require __DIR__.'/auth.php';


