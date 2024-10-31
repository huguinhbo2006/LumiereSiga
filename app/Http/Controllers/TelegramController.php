<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
include "funciones/telegram.php";

class TelegramController extends BaseController
{
    function error(Request $request){
        try {
            errores($request['archivo'], $request['linea'], $request['mensaje'], $request['usuario'], $request['url']);
            return response()->json('Mensaje Enviado', 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function soporte(Request $request){
        try {
            soporte($request['mensaje'], $request['usuario']);
            return response()->json('Mensaje Enviado', 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}