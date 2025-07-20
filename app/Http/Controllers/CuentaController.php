<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;

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

    public function devusuarios(Request $request)
    {
        try {
            $data = DB::select('
                    SELECT 
                        u.c_usuario,
                        u.l_nombre, 
                        u.l_apellido, 
                        u.c_rol, 
                        u.f_nacimiento, 
                        u.l_correoElectronico, 
                        u.l_fotoPerfil,
                        mi.c_mentorCreador, 
                        mi.l_descripcion,
                        mi.l_linkedin,
                        NULL AS c_estudiante,
                        NULL AS c_carrera,
                        NULL AS c_facultad,
                        NULL AS c_equipo,
                        NULL AS l_equipo
                    FROM usuario AS u
                    INNER JOIN MentorInvitado AS mi ON u.c_usuario = mi.c_usuario
                    WHERE mi.c_mentorCreador = :id_mentorCreador

                    UNION ALL

                    SELECT 
                        u.c_usuario,
                        u.l_nombre, 
                        u.l_apellido, 
                        u.c_rol, 
                        u.f_nacimiento, 
                        u.l_correoElectronico, 
                        u.l_fotoPerfil,
                        NULL AS c_mentorCreador, 
                        NULL AS l_descripcion,
                        NULL AS l_linkedin,
                        e.c_estudiante, 
                        e.c_carrera,
                        c.c_facultad,
                        ed.c_equipo,
                        eq.l_equipo
                    FROM usuario AS u
                    INNER JOIN Estudiante AS e ON u.c_usuario = e.c_usuario
                    INNER JOIN Carrera AS c ON e.c_carrera = c.c_carrera
                    LEFT JOIN EquipoDet AS ed ON ed.c_estudiante = e.c_estudiante
                    LEFT JOIN Equipo AS eq ON ed.c_equipo = eq.c_equipo
                    ', ["id_mentorCreador" => $request->c_usuario]);

            return response()->json([
                'success' => true,
                'message' => 'Éxito',
                'usuarios' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al obtener usuarios',
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
        $resultado = DB::select('EXEC proObtenerUsuario ?', [
            $request->correo
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

        // Verificar Hash
        if (!Hash::check($request->contrasena, $usuario['l_contrasena'])) {
            return response()->json([
                'success' => false,
                'message' => 'Correo o contraseña incorrectos'
            ]);
        }

        Auth::loginUsingId($usuario['c_usuario']);
        unset($usuario['l_contrasena']);

        return response()->json([
            'success' => true,
            'message' => 'Inicio de sesión exitoso',
            'usuario' => $usuario
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada'
        ]);
    }



    public function registrarCuenta(Request $request)
    {
        $reglas = [
            'nombre' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u'
            ],
            'apellido' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u'
            ],
            'correo' => 'required|email|unique:Usuario,l_correoElectronico',
            'contrasena' => 'required',
            'rol' => 'required|in:1,2,3',
            'fechaNacimiento' => 'required|date',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
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

        // $rutaImagen = null;
        // if ($request->hasFile('foto')) {
        //     $rutaImagen = $request->file('foto')->store('usuarios', 'public');
        // }

        //$data = $request->all();

        //Hasheo de contraseña
        $hashPassword = Hash::make($request->contrasena);
        //dd($hashPassword);
        // Guardar imagen si existe
        $fotoRuta = null;
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $nombreFoto = uniqid() . '_' . $foto->getClientOriginalName();

            $foto->move(public_path('fotos_usuarios'), $nombreFoto);
            $fotoRuta = 'fotos_usuarios/' . $nombreFoto;
        }
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
                        :linkedin, 
                        :fotoPerfil', [
                'rol' => $request->rol,
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'fechaNacimiento' => $request->fechaNacimiento,
                'correo' => $request->correo,
                'contrasena' => $hashPassword,
                'codigoEstudiante' => $request->codigoEstudiante,
                'idCarrera' => $request->carrera,
                'idMentorCreador' => $request->creador,
                'descripcionMentor' => $request->descripcion ?? '',
                'linkedin' => $request->linkedin ?? '',
                'fotoPerfil' => $fotoRuta ?? ''
            ]);


            return response()->json([
                'success' => true,
                'message' => 'Cuenta creada correctamente.',
                'usuario' => $resultado[0],
                'foto' => $fotoRuta ? asset("fotos_usuarios/{$fotoRuta}") : null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al crear la cuenta.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function editarCuenta(Request $request)
    {
        //dd($request);
        // Validar datos
        $reglas = [
            'c_usuario' => 'required|string',
            'nombre' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u'
            ],
            'apellido' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u'
            ],
            'fechaNacimiento' => 'required|date',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ];

        if ($request->rol == 2) {
            $reglas = array_merge($reglas, [
                'descripcion' => 'required|string|max:255',
                'linkedin' => 'nullable|string|max:100',
            ]);
        } elseif ($request->rol == 3) {
            $reglas = array_merge($reglas, [
                'codigoEstudiante' => 'required|string',
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

        // Procesar foto nueva si existe
        $fotoRuta = null;
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $nombreFoto = uniqid() . '_' . $foto->getClientOriginalName();
            $foto->move(public_path('fotos_usuarios'), $nombreFoto);
            $fotoRuta = 'fotos_usuarios/' . $nombreFoto;
        }

        try {
            // Ejecutar SP
            $resultado = DB::select('EXEC proEditarCuenta 
            :c_usuario,
            :nombre,
            :apellido,
            :fechaNacimiento,
            :codigoEstudiante,
            :idCarrera,
            :descripcionMentor,
            :linkedin,
            :fotoPerfil', [
                'c_usuario' => $request->c_usuario,
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'fechaNacimiento' => $request->fechaNacimiento,
                'codigoEstudiante' => $request->codigoEstudiante ?? '',
                'idCarrera' => $request->carrera ?? null,
                'descripcionMentor' => $request->descripcion ?? '',
                'linkedin' => $request->linkedin ?? '',
                'fotoPerfil' => $fotoRuta ?? ''
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cuenta actualizada correctamente.',
                'usuario' => $resultado[0],
                'foto' => $fotoRuta ? asset("fotos_usuarios/{$fotoRuta}") : null
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al editar la cuenta.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function editarCuentaOficial(Request $request)
    {
        //dd($request);
        // Validar datos
        $validator = Validator::make($request->all(), [
            'c_usuario' => 'required|string',
            'nombre' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u'
            ],
            'apellido' => [
                'required',
                'string',
                'max:100',
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/u'
            ],
            'fechaNacimiento' => 'required|date',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Procesar la foto si se carga una nueva
        $fotoRuta = null;

        // Si se envía una nueva foto
        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $nombreFoto = uniqid() . '_' . $foto->getClientOriginalName();
            $foto->move(public_path('fotos_usuarios'), $nombreFoto);
            $fotoRuta = 'fotos_usuarios/' . $nombreFoto;
        }
        // Si no se envió una nueva foto pero se envió la existente
        elseif ($request->has('fotoExistente')) {
            $fotoRuta = $request->input('fotoExistente');
        }



        //dd($request);
        try {
            // Realizar la actualización
            DB::statement("
            UPDATE Usuario SET 
                l_nombre = :nombre,
                l_apellido = :apellido,
                f_nacimiento = :fechaNacimiento,
                l_fotoPerfil = :fotoPerfil
            WHERE c_usuario = :c_usuario
        ", [
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'fechaNacimiento' => $request->fechaNacimiento,
                'fotoPerfil' => $fotoRuta ?? '',
                'c_usuario' => $request->c_usuario
            ]);

            $usuarioActualizado = (array) DB::table('Usuario')
                ->where('c_usuario', $request->c_usuario)
                ->first();

            unset($usuarioActualizado['l_contrasena']);

            return response()->json([
                'success' => true,
                'message' => 'Cuenta actualizada correctamente.',
                'usuario' => $usuarioActualizado
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar la cuenta.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
