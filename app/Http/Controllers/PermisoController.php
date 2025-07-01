<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Validator;

class PermisoController extends Controller
{
    //
    function listPermisos(Request $request){
        //dd($request);

        try {
            $permisos = DB::select('
                select * from permiso where c_usuario=:c_usuario
            ', [
                'c_usuario'=>$request->c_usuario
            ]);

            

            return response()->json([
                'mensaje' => "Se devolvio correctamente los permisos",
                'permisos' => $permisos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al obtener carreras y facultades',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }
}
