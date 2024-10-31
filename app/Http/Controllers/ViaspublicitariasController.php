<?php

namespace App\Http\Controllers;
use App\Mediospublicitario;
use App\Viaspublicitaria;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class ViaspublicitariasController extends BaseController
{
    function nuevo(Request $request){
        try{
            $via = Viaspublicitaria::create([
                'nombre' => $request['nombre'],
                'idMedioPublicitario' => $request['idMedioPublicitario'],
                'activo' => 1,
                'eliminado' => 0
            ]);
            return response()->json($via, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function mostrar(){
        try{
            $respuesta['datos'] = Viaspublicitaria::where('eliminado', '=', 0)->get();
            $respuesta['lista'] = Mediospublicitario::where('eliminado', '=', 0)->get();
            return response()->json($respuesta, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            $via = Viaspublicitaria::find($request['id']);
            $via->activo = 1;
            $via->save();

            return response()->json($via, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            $via = Viaspublicitaria::find($request['id']);
            $via->activo = 0;
            $via->save();

            return response()->json($via, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $via = Viaspublicitaria::find($request['id']);
            $via->eliminado = 1;
            $via->save();

            return response()->json($via, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            $via = Viaspublicitaria::find($request['id']);
            $via->nombre = $request['nombre'];
            $via->idMedioPublicitario = $request['idMedioPublicitario'];
            $via->save();

            return response()->json($via, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }
}