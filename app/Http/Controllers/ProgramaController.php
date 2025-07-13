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

    function grabPrograma(Request $request){
        $validator = Validator::make($request->all(), [
            'nombrePrograma' => 'required|string',
            'descripcionPrograma' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
         try {
            $rows = DB::select(
                'INSERT INTO Programa (l_programa, l_descripcion)
                OUTPUT INSERTED.c_programa AS IdCreado     
                VALUES (:l_programa, :l_descripcion);',
                [
                    'l_programa'    => $request->nombrePrograma,
                    'l_descripcion' => $request->descripcionPrograma
                ]
            );

            $idPrograma = (string) $rows[0]->IdCreado;


            return response()->json([
                'success' => true,
                'idPrograma' => $idPrograma,
                'message' => 'Programa creado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al crear el programa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    function editPrograma(Request $request){
        $validator = Validator::make($request->all(), [
            'id_programa' => 'required|string',
            'nombrePrograma' => 'required|string',
            'descripcionPrograma' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
    
        //dd($request);

        try {
            DB::statement('update programa set l_programa=:nombrePrograma, l_descripcion=:descripcionPrograma where c_programa=:id_programa', 
                [
                    'id_programa' => $request->id_programa,
                    'nombrePrograma' => $request->nombrePrograma,
                    'descripcionPrograma' => $request->descripcionPrograma
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Programa actualizado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al editar el programa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    function elimPrograma(Request $request){
        //dd($request);
        
        $idPrograma = $request->c_programa;
        
        //dd($idPrograma);

        try {
            DB::delete('delete from programa where c_programa=:c_programa', ['c_programa' => $idPrograma]);
            DB::delete('delete from Sesion where c_programa=:c_programa', ['c_programa' => $idPrograma]);
            
            return response()->json([
                'success' => true,
                'message' => 'Programa eliminado correctamente'
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
