<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;

class CuentaController extends Controller
{
    public function devCarreraFacu()
    {

        try {
            $data = DB::select('
                SELECT c.c_carrera, f.c_facultad, f.l_facultad, c.l_carrera 
                FROM carrera AS c
                INNER JOIN facultad AS f ON c.c_facultad = f.c_facultad
            ');

            $coleccion = collect($data);

            $resultado = $coleccion->groupBy('c_facultad')->map(function ($items, $codigoFacultad) {
                return [
                    'codigo_facultad' => $codigoFacultad,
                    'nombre_facultad' => $items->first()->l_facultad,
                    'carreras' => $items->map(function ($item) {
                        return [
                            'codigo_carrera' => $item->c_carrera,
                            'nombre_carrera' => $item->l_carrera
                        ];
                    })->values()
                ];
            })->values();

            return response()->json($resultado);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al obtener carreras y facultades',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'correo' => 'required|email',
            'contrasena' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Llamar al procedimiento almacenado
        $resultado = DB::select('EXEC proLogin ?, ?', [
            $request->correo,
            $request->contrasena
        ]);

        // Si no hay resultados, credenciales incorrectas
        if (empty($resultado)) {
            return response()->json([
                'success' => false,
                'message' => 'Correo o contraseña incorrectos'
            ]);
        }

        // Si devuelve Resultado = 0, también es incorrecto
        if (isset($resultado[0]->Resultado) && $resultado[0]->Resultado == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Correo o contraseña incorrectos'
            ]);
        }

        // Credenciales válidas, devolver datos del usuario
        $usuario = (array) $resultado[0];
        unset($usuario['l_contrasena']); // Por seguridad, no devolver la contraseña

        return response()->json([
            'success' => true,
            'message' => 'Inicio de sesión exitoso',
            'usuario' => $usuario
        ]);
    }
    
 

    public function registrarCuenta(Request $request)
    {
       

        $reglas = [
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'correo' => 'required|email|unique:Usuario,l_correoElectronico',
            'contrasena' => 'required',
            'rol' => 'required|in:1,2,3',
            'fechaNacimiento' => 'required|date',
        ];

        if ($request->rol == 2) {
            $reglas = array_merge($reglas, [
                'descripcion' => 'required|string|max:255',
                'creador' => 'required',
            ]);
        } elseif ($request->rol == 3) {
            $reglas = array_merge($reglas, [
                'codigoEstudiante' => 'required|string|unique:Estudiante,c_estudiante',
                'carrera' => 'required|exists:Carrera,c_carrera',
            ]);
        }

        $validator = Validator::make($request->all(), $reglas);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        //dd($request);

         try {
            $resultado = DB::select('EXEC proCrearCuenta 
                :rol, 
                :nombre, 
                :apellido, 
                :fechaNacimiento, 
                :correo, 
                :contrasena, 
                :codigoEstudiante, 
                :idCarrera, 
                :idMentorCreador, 
                :descripcionMentor, 
                :linkedin', [
                    'rol' => $request->rol,
                    'nombre' => $request->nombre,
                    'apellido' => $request->apellido,
                    'fechaNacimiento' => $request->fechaNacimiento,
                    'correo' => $request->correo,
                    'contrasena' => $request->contrasena,
                    'codigoEstudiante' => $request->codigoEstudiante,
                    'idCarrera' => $request->carrera,
                    'idMentorCreador' => $request->creador,
                    'descripcionMentor' => $request->descripcion ?? '',
                    'linkedin' => $request->linkedin ?? ''
            ]);


            return response()->json([
                'success' => true,
                'message' => 'Cuenta creada correctamente.',
                'usuario' => $resultado[0]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al crear la cuenta.',
                'error' => $e->getMessage() 
            ], 500);
        }

            return response()->json(['success' => true]);
        }

}
