<?php

namespace App\Http\Controllers;
use App\Seccionpregunta;
use App\Seccione;
include "logs.php";

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeccionpreguntasController extends BaseController
{
    function nuevo(Request $request){
        try{
            $preguntas = $request['preguntas'];
            for ($i=0; $i < $request['cantidad']; $i++) {
                $pregunta = $preguntas[$i];
                if($pregunta['respuesta'] === "A" || $pregunta['respuesta'] === "B" || $pregunta['respuesta'] === "C" || $pregunta['respuesta'] === "D"){
                    $tipo = 1;
                } else {
                    $tipo = 2;
                }
                Seccionpregunta::create([
                    'respuesta' => $pregunta['respuesta'],
                    'numero' => $pregunta['numero'],
                    'idSeccion' => $pregunta['seccion'],
                    'tipo' => $tipo,
                    'activo' => 1,
                    'eliminado' => 0
                ]);
            }
            return response()->json($request, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function mostrar(Request $request){
        try{
            $preguntas = Seccionpregunta::where('idSeccion', '=', $request['seccion'])->where('eliminado', '=', 0)->get();
            return response()->json($preguntas, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function existen(Request $request) {
        try{
            $preguntas = Seccionpregunta::where('idSeccion', '=', $request['seccion'])->where('eliminado', '=', 0)->get();
            return response()->json(count($preguntas), 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }
}