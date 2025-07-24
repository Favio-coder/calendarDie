<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CuentaController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\RecursoController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\ProgramaController;
use App\Http\Controllers\SesionController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\ImpresionController;
use App\Http\Controllers\ActividadController;

// Vistas renderizadas en VUE
Route::get('/', function () {
    return Inertia::render('Acceso', [
        'appName' => config('app.name'),
    ]);
});

Route::get('/acceso', function () {
    return Inertia::render('Acceso', [
        'appName' => config('app.name'),
    ]);
})->name('acceso');

Route::get('/login', function () {
    return redirect('/acceso');
})->name('login');

Route::post('/login', [CuentaController::class, 'login']);

Route::middleware(['auth'])->group(function () {
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
    Route::post('/logout', [CuentaController::class, 'logout']);
    Route::post('/registrarCuenta', [CuentaController::class, 'registrarCuenta']);
    Route::post('/editarCuenta', [CuentaController::class, 'editarCuenta']);
    Route::post('/cambiarContra', [ConfiguracionController::class, 'cambiarContra']);
    Route::post('/elimCuenta', [CuentaController::class, 'eliminarCuenta']);
    Route::post('/editarCuentaOficial', [CuentaController::class, 'editarCuentaOficial']);


    //Obtener carreras
    Route::get('/obtenerCarreras', [CuentaController::class, 'devCarreraFacu']);
    Route::post('/listUsuarios', [CuentaController::class, 'devusuarios']);
    Route::get('/obtenerEstudiantesXcarrera', [EquipoController::class, 'listEstudiantesxCarrera']);

    //Modulo de equipo
    Route::post('/grabarEquipo', [EquipoController::class, 'grabEquipo']);
    Route::get('/listEquipo', [EquipoController::class, 'listEquipo']);
    Route::post('/elimEquipo', [EquipoController::class, 'elimEquipo']);
    Route::post('/editEquipo', [EquipoController::class, 'editEquipo']);

    //Modulo de actividades 
    Route::get('/listActividad', [ActividadController::class, 'listActividad']);
    Route::post('/grabActividad', [ActividadController::class, 'grabActividad']);
    Route::post('/elimActividad', [ActividadController::class, 'elimActividad']);

    //Modulo de recurso 
    Route::get('/listRecursos', [RecursoController::class, 'listRecursos']);
    Route::post('/elimRecurso', [RecursoController::class, 'elimRecurso']);
    Route::post('/grabRecurso', [RecursoController::class, 'grabRecurso']);

    //Modulo de mentores
    Route::get('/listMentores', [MentorController::class, 'listMentores']);

    //Modulo de programas 
    Route::get('/listProgramas', [ProgramaController::class, 'listProgramas']);
    Route::post('/grabPrograma', [ProgramaController::class, 'grabPrograma']);
    Route::post('/editPrograma', [ProgramaController::class, 'editPrograma']);
    Route::post('/elimPrograma', [ProgramaController::class, 'elimPrograma']);

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
    Route::post('/devPermiso', [PermisoController::class, 'devPermiso']);
    Route::post('/listMentoresOficiales', [ConfiguracionController::class, 'listMentoresOficiales']);
    Route::post('/grabPermisos', [PermisoController::class, 'grabPermisos']);

    //Impresión 
    Route::post('/genPdfEquipo', [ImpresionController::class, 'genPdfEquipo']);
    Route::post('/genPdfCompromiso', [ImpresionController::class, 'genPdfCompromiso']);
    Route::post('/genPdfRecurso', [ImpresionController::class, 'genPdfRecurso']);
});
