<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Validator;



class EquipoController extends Controller
{
    //
    function listEstudiantesxCarrera()
    {
        try {
            $estudiantesXcarrera = db::select('
                SELECT 
                    e.c_usuario, 
                    e.c_estudiante, 
                    u.l_nombre, 
                    u.l_apellido, 
                    c.c_carrera,
                    c.l_carrera
                    FROM Estudiante AS e
                    inner JOIN Usuario AS u ON u.c_usuario = e.c_usuario
                    LEFT JOIN Carrera AS c ON e.c_carrera = c.c_carrera;
            ');

            return response()->json([
                'success' => true,
                'estudiantes' => $estudiantesXcarrera
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al devolver la lista de estudiantes.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    function grabEquipo(Request $request)
    {
        //dd($request);
        $validator = Validator::make($request->all(), [
            'nombreEquipo' => 'required|string',
            'descripcionEquipo' => 'required|string',
            'estudiantes' => 'required|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $estudiantes = json_decode($request->input('estudiantes'), true);

        // Validar que es un array y no nulo
        if (!is_array($estudiantes)) {
            return response()->json([
                'errors' => [
                    'estudiantes' => ['Error interno al decodificar estudiantes.']
                ]
            ], 422);
        }

        // Validar que no esté vacío
        if (empty($estudiantes)) {
            return response()->json([
                'errors' => [
                    'estudiantes' => ['Debe incluir al menos un estudiante.']
                ]
            ], 422);
        }

        // Validar líder
        $tieneLider = collect($estudiantes)->contains(function ($e) {
            return isset($e['q_lider']) && $e['q_lider'] == 1;
        });

        if (!$tieneLider) {
            return response()->json([
                'errors' => [
                    'estudiantes' => ['Debe haber al menos un estudiante como líder.']
                ]
            ], 422);
        }


        //Guardar logo de equipo
        $logoRuta = null;
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $nombreLogo = uniqid() . '_' . $logo->getClientOriginalName();
            $logo->move(public_path('logos_equipo'), $nombreLogo);
            $logoRuta = 'logos_equipo/' . $nombreLogo;
        }

        try {
            // Ejecutar el procedimiento almacenado
            DB::statement(
                'EXEC proCrearEquipo @nombreEquipo = ?, @descripcionEquipo = ?, @logoEquipo = ?, @jsonEstudiantes = ?',
                [
                    $request->input('nombreEquipo'),
                    $request->input('descripcionEquipo'),
                    $logoRuta ?? '',
                    json_encode($estudiantes)
                ]
            );

            return response()->json(['mensaje' => 'Equipo registrado correctamente.'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al guardar el equipo.',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }

    function editEquipo(Request $request){
        $validator = Validator::make($request->all(), [
            'c_equipo' => 'required|string',
            'nombreEquipo' => 'required|string',
            'descripcionEquipo' => 'required|string',
            'estudiantes' => 'required|string',
            'estudiantesEliminar'=> 'required|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'logoActual' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $estudiantes = json_decode($request->input('estudiantes'), true);

        // Validar que es un array y no nulo
        if (!is_array($estudiantes)) {
            return response()->json([
                'errors' => [
                    'estudiantes' => ['Error interno al decodificar estudiantes.']
                ]
            ], 422);
        }

        // Validar que no esté vacío
        if (empty($estudiantes)) {
            return response()->json([
                'errors' => [
                    'estudiantes' => ['Debe incluir al menos un estudiante.']
                ]
            ], 422);
        }

        // Validar líder
        $tieneLider = collect($estudiantes)->contains(function ($e) {
            return isset($e['q_lider']) && $e['q_lider'] == 1;
        });

        if (!$tieneLider) {
            return response()->json([
                'errors' => [
                    'estudiantes' => ['Debe haber al menos un estudiante como líder.']
                ]
            ], 422);
        }

        $logoRuta = $request->input('logoActual'); 

        if ($request->hasFile('logo')) {
            $logoAnterior = $request->input('logoActual');
            if ($logoAnterior && file_exists(public_path($logoAnterior))) {
                @unlink(public_path($logoAnterior));
            }

            // 2) Guardar el nuevo logo
            $nuevoLogo   = $request->file('logo');
            $nombreLogo  = uniqid() . '_' . $nuevoLogo->getClientOriginalName();
            $nuevoLogo->move(public_path('logos_equipo'), $nombreLogo);

            $logoRuta = 'logos_equipo/' . $nombreLogo; 
        }

        try {
            // Ejecutar el procedimiento almacenado
            DB::statement(
            'EXEC proEditarEquipo ?, ?, ?, ?, ?, ?',
            [
                $request->input('c_equipo'),            
                $request->input('nombreEquipo'),         
                $request->input('descripcionEquipo'),    
                $logoRuta ?? '',                        
                $request->input('estudiantesEliminar'),  
                $request->input('estudiantes')          
                ]
            );

            return response()->json(['mensaje' => 'Equipo editado correctamente.'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al editar el equipo.',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }


    function listEquipo(){
        try {
            $Equipo = db::select(' 
                select * from Equipo
            ');

            $EquipoDet = db::select('
                select * from EquipoDet
            ');

            return response()->json([
                'success' => true,
                'Equipo' => $Equipo,
                'EquipoDet' => $EquipoDet
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al devolver la lista de equipo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    function elimEquipo(Request $request){
        try{
            DB::statement('EXEC proElimEquipo ?', [$request->c_equipo]);
            return response()->json([
                'success' => true,
                'message' => 'Equipo eliminado exitosamente'
            ]);
        }catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al eliminar el equipo',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
