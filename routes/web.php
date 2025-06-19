<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ejemploController;
use App\Http\Controllers\CuentaController;
use App\Http\Controllers\EquipoController;


// Vistas renderizadas en VUE
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'appName' => config('app.name'),
    ]);
});

Route::get('/acceso', function () {
    return Inertia::render('Acceso', [
        'appName' => config('app.name'),
    ]);
});

Route::get('/inicio', function () {
    return Inertia::render('Inicio', [
        'appName' => config('app.name'),
    ]);
});


Route::get('/actividades', function () {
    return Inertia::render('Actividades', [
        'appName' => config('app.name'),
    ]);
});

Route::get('/prueba', function () {
    return Inertia::render('Prueba', [
        'appName' => config('app.name'),
    ]);
});

Route::get('/configuracion', function () {
    return Inertia::render('Configuracion', [
        'appName' => config('app.name'),
    ]);
});

Route::get('/recursos', function () {
    return Inertia::render('Recursos', [
        'appName' => config('app.name'),
    ]);
});


// Rutas para consumir con get y post 
Route::post('/login', [CuentaController::class, 'login']);
Route::post('/registrarCuenta', [CuentaController::class, 'registrarCuenta']);
Route::get('/obtenerCarreras', [CuentaController::class, 'devCarreraFacu']);
Route::post('/listUsuarios', [CuentaController::class, 'devusuarios']);
Route::get('/obtenerEstudiantesXcarrera', [EquipoController::class, 'listEstudiantesxCarrera']);
Route::post('/grabarEquipo', [EquipoController::class, 'grabEquipo']);
Route::get('/listEquipo', [EquipoController::class, 'listEquipo']);
Route::post('/elimEquipo', [EquipoController::class, 'elimEquipo']);