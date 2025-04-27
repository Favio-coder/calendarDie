<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ejemploController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'appName' => config('app.name'),
    ]);
});

Route::get('/obtenerDatos', [ejemploController::class, 'obtenerData']);
