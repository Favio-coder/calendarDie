<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ejemploController extends Controller
{
    //

    public function obtenerData(){

        $data = [
            'mensaje' => 'Bienvenido a la aplicación Vue + Laravel',
            'items' => ['Elemento 10', 'EFavio', 'Elemento 30']
        ];

        return response()->json($data);
    }
}
