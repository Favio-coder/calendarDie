<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ActividadController extends Controller
{
    //
    function listActividad()
    {
        try {
            $actividades = DB::select('Exec proListActividadesCompleto');

            return response()->json([
                'success' => true,
                'actividades' => $actividades
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al listar las actividades',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function grabActividad(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'c_usuario'        => 'required|string|uuid|exists:Usuario,c_usuario',
            'l_actividad'      => 'required|string|max:255',
            'l_descripcion'    => 'required|string|max:1000',
            'l_diaActividad'   => 'required|date|after_or_equal:today',
            'l_horaActividad'  => 'required|date_format:H:i',
            'recursos'         => 'nullable|string',
            'recursos.*'       => 'integer|distinct|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }


        $horaSql   = Carbon::createFromFormat('H:i', $request->l_horaActividad)->format('H:i:s');
        $recursos = $request->recursos ?? '';


        try {
            $row = DB::select(
                'EXEC proCrearActividad ?, ?, ?, ?, ?, ?',
                [
                    $request->c_usuario,
                    $request->l_actividad,
                    $request->l_descripcion,
                    $horaSql,
                    $request->l_diaActividad,
                    $recursos
                ]
            );

            $idActividad = $row[0]->c_actividad ?? null;

            return response()->json([
                'success'      => true,
                'c_actividad'  => $idActividad,
                'message'      => 'Actividad creada correctamente.'
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la actividad.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    function elimActividad(Request $request){
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|exists:Actividad,c_actividad',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            
            db::statement('exec proElimActividad :c_actividad', [
                'c_actividad' => $request->id
            ]);

            return response()->json([
                'success'      => true,
                'message'      => 'Actividad eliminada correctamente.'
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la actividad.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
