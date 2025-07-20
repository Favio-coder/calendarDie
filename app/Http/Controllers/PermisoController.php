<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Validator;

class PermisoController extends Controller
{
    //
    function listPermisos(Request $request)
    {
        //dd($request);

        try {
            $permisos = DB::select('
                select * from permiso where c_usuario=:c_usuario
            ', [
                'c_usuario' => $request->c_usuario
            ]);



            return response()->json([
                'mensaje' => "Se devolvio correctamente los permisos",
                'permisos' => $permisos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al obtener permisos',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }

    function devPermiso(Request $request)
    {
        try {
            $permiso = DB::select('
                select top 1 q_activo from Permiso 
                    where 
                        c_usuario=:c_usuario and 
                        l_modulo=:modulo and 
                        l_descripcion=:descripcion and
                        l_tipo=:tipo
            ', [
                'c_usuario' => $request->c_usuario,
                'modulo' => $request->modulo,
                'descripcion' => $request->descripcion,
                'tipo' => $request->tipo
            ]);

            if ($permiso[0]->q_activo === '1') {
                $permiso = true;
            } else {
                $permiso = false;
            }

            return response()->json([
                'mensaje' => "Se devolvio correctamente el permiso",
                'permiso' => $permiso
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'mensaje' => 'Error al obtener permiso',
                'detalle' => $e->getMessage()
            ], 500);
        }
    }

    public function grabPermisos(Request $request)
    {
        $request->validate([
            'c_usuario'           => 'required|uuid|exists:Usuario,c_usuario',
            'permisos'            => 'required|array',
            'permisos.*.c_permiso' => 'required|integer|exists:Permiso,c_permiso',
            'permisos.*.q_activo' => 'required|in:0,1',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->permisos as $perm) {
                DB::table('Permiso')
                    ->where('c_permiso', $perm['c_permiso'])
                    ->where('c_usuario', $request->c_usuario)
                    ->update(['q_activo' => $perm['q_activo']]);
            }
            DB::commit();

            return response()->json(['success' => true], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar permisos',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
