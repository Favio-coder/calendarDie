<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Validator;

class RecursoController extends Controller
{
    //
    function listRecursos()
    {
        try {
            $listRecursos = DB::select('select * from Recurso');

            return response()->json([
                'success' => true,
                'recursos' => $listRecursos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al devolver la lista de estudiantes.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    function elimRecurso(Request $request)
    {
        //dd($request);
        $validator = Validator::make($request->all(), [
            'c_recurso' => 'required|integer|exists:Recurso,c_recurso'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::statement('DELETE FROM Recurso WHERE c_recurso = :c_recurso', [
                'c_recurso' => $request->c_recurso
            ]);

            return response()->json([
                'message' => 'Se elimino correctamente',
                'success' => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al eliminar el recurso.',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }

    public function grabRecurso(Request $request)
    {
        //dd($request);
        $validator = Validator::make($request->all(), [
            'nombreRecurso' => 'required|string|max:100',
            'descripcionRecurso' => 'required|string|max:1000',
            'accion' => 'required|string|in:insertar,editar',
            'idRecurso' => 'nullable|integer|exists:Recurso,c_recurso'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::statement('EXEC proGrabModiRecurso 
            @modo = :modo, 
            @id_recurso = :id_recurso, 
            @nombre_recurso = :nombre, 
            @descripcion_recurso = :descripcion', [
                'modo' => $request->accion,
                'id_recurso' => $request->idRecurso,
                'nombre' => $request->nombreRecurso,
                'descripcion' => $request->descripcionRecurso
            ]);

            return response()->json(['mensaje' => 'Operación realizada con éxito']);
        } catch (\Exception $e) {
            return response()->json(['mensaje' => 'Error al ejecutar el procedimiento', 'error' => $e->getMessage()], 500);
        }
    }
}
