<?php

namespace App\Http\Controllers;
use App\Ingreso;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
include "funciones/FuncionesGenerales.php";

class RespaldosController extends BaseController
{
    function traerIngresos(Request $request){
        try {
            $ingresos = Ingreso::select('ingresos.id', 'ingresos.imagen')->where('idCalendario', '=', $request['idCalendario'])->get();
            return response()->json($ingresos, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}