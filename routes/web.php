<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ejemploController;
use App\Http\Controllers\CuentaController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\RecursoController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\ProgramaController;
use App\Http\Controllers\SesionController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\PermisoController;

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

Route::get('/programas', function () {
    return Inertia::render('Programas', [
        'appName' => config('app.name'),
    ]);
});

// Rutas para consumir con get y post 

// Acceso
Route::post('/login', [CuentaController::class, 'login']);
Route::post('/registrarCuenta', [CuentaController::class, 'registrarCuenta']);
Route::post('/elimCuenta', [CuentaController::class, 'eliminarCuenta']);

//Obtener carreras
Route::get('/obtenerCarreras', [CuentaController::class, 'devCarreraFacu']);
Route::post('/listUsuarios', [CuentaController::class, 'devusuarios']);
Route::get('/obtenerEstudiantesXcarrera', [EquipoController::class, 'listEstudiantesxCarrera']);

//Modulo de equipo
Route::post('/grabarEquipo', [EquipoController::class, 'grabEquipo']);
Route::get('/listEquipo', [EquipoController::class, 'listEquipo']);
Route::post('/elimEquipo', [EquipoController::class, 'elimEquipo']);

//Modulo de recurso 
Route::get('/listRecursos', [RecursoController::class, 'listRecursos']);
Route::post('/elimRecurso', [RecursoController::class, 'elimRecurso']);
Route::post('/grabRecurso', [RecursoController::class, 'grabRecurso']);

//Modulo de mentores
Route::get('/listMentores', [MentorController::class, 'listMentores']);

//Modulo de programas 
Route::get('/listProgramas', [ProgramaController::class, 'listProgramas']);

//Modulo de sesión
Route::post('/listSesionXprograma', [SesionController::class, 'listSesionXprograma']);
Route::post('/grabSesion', [SesionController::class, 'grabSesion']);
Route::post('/elimSesion', [SesionController::class, 'elimSesion']);

//Modulo de configuración
Route::get('/genEstadisticas', [ConfiguracionController::class, 'genEstadisticas']);
Route::post('/detMatriculaProgram', [ConfiguracionController::class, 'detMatriculaProgram']);
Route::post('/grabMatriculaMentorEstudiante', [ConfiguracionController::class, 'grabMatriculaMentorEstudiante']);
Route::post('/elimMatriculaMentorEst', [ConfiguracionController::class, 'elimMatriculaMentorEst']);
Route::post('/eliminarUsuario', [ConfiguracionController::class, 'eliminarUsuario']);


//Modulo de permisos 
Route::post('/listPermisos', [PermisoController::class, 'listPermisos']);
