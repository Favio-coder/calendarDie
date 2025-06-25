<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Validator;

class SesionController extends Controller
{
    //
    function listSesionXprograma(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'c_programa' => 'required|integer|exists:programa,c_programa',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Traemos todas las sesiones y recursos asociados en una sola consulta
            $datos = DB::select("
            SELECT 
                s.*, 
                sr.c_sesionrecurso, 
                sr.l_nombre AS recurso_nombre, 
                sr.l_tipo AS recurso_tipo, 
                sr.l_linkArchivo AS recurso_url
            FROM Sesion s
            LEFT JOIN SesionRecurso sr ON s.c_sesion = sr.c_sesion
            WHERE s.c_programa = :c_programa
        ", [
                'c_programa' => $request->c_programa
            ]);

            $sesiones = [];

            foreach ($datos as $fila) {
                $id = $fila->c_sesion;

                // Creamos la sesión si no existe aún en el array
                if (!isset($sesiones[$id])) {
                    $sesiones[$id] = (object)[
                        'c_sesion' => $fila->c_sesion,
                        'l_sesion' => $fila->l_sesion,
                        'c_programa' => $fila->c_programa,
                        'l_descripcion' => $fila->l_descripcion,
                        'l_linkSesion' => $fila->l_linkSesion,
                        'l_linkGrabacion' => $fila->l_linkGrabacion,
                        'l_fotoSesion' => $fila->l_fotoSesion,
                        'f_insercion' => $fila->f_insercion,
                        'recursosSesion' => []
                    ];
                }

                //dd($fila);
                // Solo si l_nombre del recurso está presente, lo agregamos
                if (trim($fila->recurso_nombre) !== '') {
                    $sesiones[$id]->recursosSesion[] = [
                        'c_sesionrecurso' => $fila->c_sesionrecurso,
                        'nombre' => $fila->recurso_nombre,
                        'tipo' => $fila->recurso_tipo ?? '',
                        'url' => $fila->recurso_url ?? ''
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'sesiones' => array_values($sesiones)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al devolver la lista de sesiones.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function grabSesion(Request $request)
    {
        $data = $request->all();

        // Normalizar valores 'undefined' => ''
        foreach (['l_linkSesion', 'l_linkGrabacion'] as $field) {
            if (!isset($data[$field]) || $data[$field] === 'undefined') {
                $data[$field] = '';
            }
        }

        // Validación
        $validator = Validator::make($data, [
            'l_sesion' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if ($value === 'undefined') {
                        $fail("El campo Nombre de la sesión no puede estar vacío.");
                    }
                },
            ],
            'l_descripcion' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if ($value === 'undefined') {
                        $fail("El campo Descripción no puede estar vacío.");
                    }
                },
            ],
            'l_linkSesion' => 'nullable|url',
            'l_linkGrabacion' => 'nullable|url',
            'c_programa' => [
                'required',
                'integer',
                'exists:programa,c_programa',
                function ($attribute, $value, $fail) {
                    if ($value === 'undefined') {
                        $fail("El campo Programa no puede ser 'undefined'.");
                    }
                },
            ],
            'imagen' => 'nullable|file|mimes:jpg,jpeg,png,gif',
            'recursosArchivos.*' => 'nullable|file|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            // Guardar imagen (si existe)
            $rutaImagen = '';
            if ($request->hasFile('imagen')) {
                $archivoImagen = $request->file('imagen');
                $nombreImagen = uniqid() . '_' . $archivoImagen->getClientOriginalName();
                $archivoImagen->move(public_path('imagen_sesion'), $nombreImagen);
                $rutaImagen = 'imagen_sesion/' . $nombreImagen;
            }

            // Insertar sesión y obtener ID con insertGetId() — más seguro que SCOPE_IDENTITY()
            $c_sesion = DB::table('sesion')->insertGetId([
                'l_sesion' => $request->l_sesion ?? '',
                'c_programa' => $request->c_programa,
                'l_descripcion' => $request->l_descripcion ?? '',
                'l_linkSesion' => $request->l_linkSesion ?? '',
                'l_linkGrabacion' => $request->l_linkGrabacion ?? '',
                'l_fotoSesion' => $rutaImagen
            ]);

            // Insertar recursos (si existen)
            if ($request->hasFile('recursosArchivos')) {
                foreach ($request->file('recursosArchivos') as $archivo) {
                    $nombreOriginal = $archivo->getClientOriginalName();
                    $extension = strtolower($archivo->getClientOriginalExtension());
                    $nombreGuardado = uniqid() . '_' . $nombreOriginal;
                    $archivo->move(public_path('archivos_recursos'), $nombreGuardado);
                    $rutaArchivo = 'archivos_recursos/' . $nombreGuardado;

                    DB::table('SesionRecurso')->insert([
                        'c_sesion' => $c_sesion,
                        'l_nombre' => pathinfo($nombreOriginal, PATHINFO_FILENAME),
                        'l_tipo' => $extension,
                        'l_linkarchivo' => $rutaArchivo
                    ]);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Se creó la sesión correctamente'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al guardar la sesión',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }

    
    function elimSesion(Request $request){
        //dd($request);
        try {

            DB::delete('delete from SesionRecurso where c_sesion=:c_sesion', [
                'c_sesion' => $request->c_sesion
            ]);

            DB::delete('delete from Sesion where c_sesion=:c_sesion and c_programa=:c_programa', [
                'c_sesion' => $request->c_sesion, 
                'c_programa' => $request->c_programa
            ]);

            return response()->json([
                'success' => true,
                // 'sesiones' => array_values($sesiones)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al eliminar una sesión del programa',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
