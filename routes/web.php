<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\SolicitudExportController;


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

    // Rutas para exportación
    Route::get('/export/csv', [SolicitudExportController::class, 'exportCSV'])->name('export.csv');
    Route::get('/export/excel', [SolicitudExportController::class, 'exportExcel'])->name('export.excel');
});


require __DIR__.'/auth.php';


