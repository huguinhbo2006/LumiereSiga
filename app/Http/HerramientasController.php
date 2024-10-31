<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalificadorController extends BaseController
{
    function formatearNumeros(Request $request){
        try {
            $respuesta['numero'] = number_format($request['numero'], 2, '.', ',');
            return response()->json($respuesta);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}