<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Usuario;

class AuthController extends Controller
{


    public function login(Request $request)
    {
        $credentials = $request->only('l_correoElectronico', 'contrasena');

        if (Auth::attempt($credentials)) {
            $usuario = Auth::user();
            return response()->json([
                'success' => true,
                'usuario' => $usuario
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Credenciales incorrectas'
        ], 401);
    }
}
