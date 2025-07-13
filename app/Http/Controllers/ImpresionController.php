<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class ImpresionController extends Controller
{
    //
    function genPdfEquipo(Request $request)
    {
        $equipoImpresion = db::select('select 
                                e.c_estudiante, 
                                u.l_nombre, 
                                u.l_apellido, 
                                eq.c_equipo,
                                eq.l_equipo, 
                                eq.l_logoEquipo 
                            from Usuario as u
                            inner join Estudiante as e
                            on u.c_usuario = e.c_usuario
                            left join EquipoDet as ed
                            on u.c_usuario = ed.c_usuario
                            left join Equipo as eq
                            on eq.c_equipo = ed.c_equipo
                            where eq.c_equipo= :c_equipo', [
            'c_equipo' => $request['equipo']['c_equipo']
        ]);

        if (empty($equipoImpresion)) {
            return response()->json([
                'success' => false,
                'errors' => 'Equipo no encontrado'
            ], 500);
        }


        $nombreEquipo = $equipoImpresion[0]->l_equipo;
        $logo = $equipoImpresion[0]->l_logoEquipo;
        $logo = str_replace('logos_equipo/', '', $logo);
        $pdf = Pdf::loadView('pdf.compromiso-equipo', [
            'equipo' => $nombreEquipo,
            'logo' => $logo,
            'integrantes' => $equipoImpresion
        ]);

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="compromiso_equipo.pdf"');
    }

    public function genPdfCompromiso(Request $request)
    {
        $estudiante = $request->all(); 
        $estudiante = $estudiante['usuario'];
        
        if (empty($estudiante['l_equipo']) || empty($estudiante['l_programa'])) {
            return response()->json([
                'success' => false,
                'message' => 'Faltan datos del equipo o del programa.'
            ], 400);
        }

        setlocale(LC_TIME, 'es_ES.UTF-8'); // Importante para Linux/macOS
        Carbon::setLocale('es');           // Asegura que Carbon use español

        $fecha = ucfirst(Carbon::now()->translatedFormat('l j \d\e F \d\e\l Y'));


        $dataPdf = [
            'equipo'      => $estudiante['l_equipo'],
            'programa'    => $estudiante['l_programa'],
            'fecha'       => $fecha,
            'usuario'     => $estudiante 
        ];

        $pdf = Pdf::loadView('pdf.compromiso-estudiante', $dataPdf);

        return response($pdf->output(), 200)
            ->header('Content-Type',        'application/pdf')
            ->header('Content-Disposition', 'inline; filename="compromiso_equipo.pdf"');
    }
}
