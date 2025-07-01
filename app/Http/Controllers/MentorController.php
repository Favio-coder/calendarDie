<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Validator;

class MentorController extends Controller
{
    //
    function listMentores()
    {
        try {
            $mentores = DB::select('select u.l_nombre, u.l_apellido, u.l_fotoPerfil, m.l_descripcion, m.l_linkedin from Usuario as u inner join 
                            MentorInvitado as m on u.c_usuario = m.c_usuario');

            return response()->json([
                'success' => true,
                'mentores' => $mentores
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
