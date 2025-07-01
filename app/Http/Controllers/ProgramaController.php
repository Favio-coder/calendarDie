<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Validator;

class ProgramaController extends Controller
{
    //
    function listProgramas(){
        try {
            $programas = DB::select('select * from Programa');

            return response()->json([
                'success' => true,
                'programas' => $programas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al devolver la lista de mentores.',
                'error' => $e->getMessage()
            ], 500);
        }   
    }
}
