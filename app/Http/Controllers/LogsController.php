<?php

namespace App\Http\Controllers;
use App\Log;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class LogsController extends BaseController
{
    function error(Request $request){
        try{
            $log = Log::create([
                'nombre' => $request['user'],
                'accion' => $request['accion'],
                'error' => $request['mensaje'],
                'activo' => 1,
                'eliminado' => 0
            ]);
        }catch(Exception $e){
            return response()->json('Error al guardar log', 400);
        }
    }
}