<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Validator;

class ConfiguracionController extends Controller
{
    //
    function genEstadisticas()
    {
        try {
            $est = DB::select(
                'SELECT 
                        c.l_carrera AS NombreCarrera,
                        COUNT(e.c_carrera) AS CantidadEstudiantes
                    FROM 
                        Estudiante e
                    JOIN 
                        Carrera c ON e.c_carrera = c.c_carrera
                    GROUP BY 
                        c.l_carrera
                    ORDER BY 
                        CantidadEstudiantes DESC'
            );

            //dd($est);

            return response()->json([
                'estadisticas' => $est,
                'mensaje' => 'Estadísticas devueltas correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al obtener estadísticas',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }

    function detMatriculaProgram(Request $request)
    {
        //dd($request);

        try {
            $mentoresInscritos = DB::select('select 
                        pd.c_programaDet,
                        pd.c_mentorInvitado,
                        pd.c_asignadoPor,
                        pd.c_programa,
                        u.c_usuario,
                        u.l_nombre,
                        u.l_apellido
                    from ProgramaDet as pd
                    inner join Usuario as u 
                    on u.c_usuario = pd.c_mentorInvitado
                    where c_asignadoPor=:c_asignadoPor
                ', [
                'c_asignadoPor' => $request->c_usuario
            ]);
            $estudiantesInscritos = DB::select(
                'select 
                        est.c_usuario,
                        u.l_nombre,
                        u.l_apellido,
                        est.c_estudiante,
                        equi.c_equipo, 
                        equi.l_equipo,
                        equid.c_equipoDet, 
                        equid.c_estudiante, 
                        equid.c_usuario, 
                        equid.c_equipo,
                        mat.c_equipo AS equipo_matricula,
                        mat.c_programa,
                        mat.c_matricula,
                        pro.l_programa
                    FROM Equipo AS equi
                    INNER JOIN EquipoDet AS equid ON equi.c_equipo = equid.c_equipo
                    INNER JOIN Estudiante AS est ON equid.c_estudiante = est.c_estudiante
                    INNER JOIN Usuario AS u ON est.c_usuario = u.c_usuario
                    LEFT JOIN Matricula AS mat ON mat.c_estudiante = est.c_estudiante
                        AND mat.c_equipo = equid.c_equipo  -- ⬅️ Coincidencia exacta de equipo
                    LEFT JOIN Programa AS pro ON pro.c_programa = mat.c_programa
                    WHERE mat.c_programa IS NOT NULL;
                '
            );

            return response()->json([
                'Mentores' =>  $mentoresInscritos,
                'Estudiantes' =>  $estudiantesInscritos,
                'mensaje' => 'Estudiantes y mentores listados correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al obtener la lista de estudiantes y mentores',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }

    function programaAsignado()
    {
        try {


            return response()->json([
                'estadisticas' => 'est',
                'mensaje' => 'Equipo registrado correctamente.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al obtener programa asignado',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }

    public function grabMatriculaMentorEstudiante(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'asignaciones' => 'required|array|min:1',
            'tipoEnvio' => 'required|string|in:profesor,estudiante',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $asignaciones = $request->input('asignaciones');
        $tipo = $request->input('tipoEnvio');


        //dd($request);
        try {
            foreach ($asignaciones as $asignado) {
                //dd($asignado);
                // Validaciones por tipo
                if ($tipo === 'profesor') {
                    $subValidator = Validator::make($asignado, [
                        'c_usuario' => 'required|string',
                        'c_mentorCreador' => 'required|string',
                        'c_programa' => 'required'
                    ]);

                    if ($subValidator->fails()) {
                        return response()->json([
                            'success' => false,
                            'errors' => $subValidator->errors()
                        ], 422);
                    }

                    $programaId = (int) $asignado['c_programa'];

                    DB::insert('
                    INSERT INTO ProgramaDet (c_mentorInvitado, c_asignadoPor, c_programa)
                    VALUES (:mentorInvitado, :asignadoPor, :programa)
                ', [
                        'mentorInvitado' => $asignado['c_usuario'],
                        'asignadoPor' => $asignado['c_mentorCreador'],
                        'programa' => $programaId
                    ]);
                } elseif ($tipo === 'estudiante') {
                    $subValidator = Validator::make($asignado, [
                        'c_estudiante' => 'required|string',
                        'c_usuario' => 'required|string',
                        'c_programa' => 'required'
                    ]);

                    if ($subValidator->fails()) {
                        return response()->json([
                            'success' => false,
                            'errors' => $subValidator->errors()
                        ], 422);
                    }

                    $programaId = (int) $asignado['c_programa'];
                    $equipoId = (int) $asignado['c_equipo'];

                    DB::insert('
                    INSERT INTO matricula (c_estudiante, c_programa, f_matricula, c_usuario, c_equipo)
                    VALUES (:estudiante, :programa, GETDATE(), :usuario, :equipo)
                ', [
                        'estudiante' => $asignado['c_estudiante'],
                        'programa' => $programaId,
                        'usuario' => $asignado['c_usuario'],
                        'equipo' => $equipoId
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'mensaje' => $tipo === 'profesor'
                    ? 'Mentores registrados correctamente.'
                    : 'Estudiantes matriculados correctamente.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Error durante el registro.',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }

    public function elimMatriculaMentorEst(Request $request)
    {
        //dd($request);


        $validator = Validator::make($request->all(), [
            'c_matricula' => 'required',
            'tipo' => 'required|string|in:profesor,estudiante',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $tipo = $request->input('tipo');

        try {
            // Validaciones por tipo
            if ($tipo === 'profesor') {
            } elseif ($tipo === 'estudiante') {
                $matriculaId = (int) $request->c_matricula;
                //dd($matriculaId);
                db::delete('
                        delete from Matricula where c_matricula=:c_matricula
                    ', [
                    'c_matricula' => $matriculaId
                ]);
            }

            return response()->json([
                'success' => true,
                'mensaje' => $tipo === 'profesor'
                    ? 'Mentore(s) eliminados correctamente'
                    : 'Estudiante(s) eliminados correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Error durante la eliminación',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }

    function eliminarUsuario(Request $request)
    {
        //dd($request);
        $validator = Validator::make($request->all(), [
            'c_usuario' => 'required|string',
            'c_rol' => 'required|string|in:2,3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::statement("EXEC proEliminarCuenta @idUsuario = ?, @c_rol = ?", [
                $request->c_usuario,
                $request->c_rol
            ]);


            return response()->json([
                'success' => true,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Error durante la eliminación',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }
}
