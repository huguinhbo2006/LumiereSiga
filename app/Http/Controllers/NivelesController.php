<?php

namespace App\Http\Controllers;
use App\Nivele;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class NivelesController extends BaseController
{
    function mostrar(){
        try{
            $niveles = Nivele::where('eliminado', '=', '0')->get();
            return response()->json($niveles, 200);
        }catch(Exception $e){
            return response()->json('Error en le servidor', 400);
        }
    }

    function nuevo(Request $request){
        try{
            $nivel = Nivele::create([
                'nombre' => $request['nombre'],
                'activo' => 1,
                'eliminado' => 0
            ]);

            return response()->json($nivel, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function activar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 2){
                return response()->json('No se pude activar este nivel', 400);
            }
            $nivel = Nivele::find($request['id']);
            $nivel->activo = 1;
            $nivel->save();

            return response()->json($nivel, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 2){
                return response()->json('No se pude desactivar este nivel', 400);
            }
            $nivel = Nivele::find($request['id']);
            $nivel->activo = 0;
            $nivel->save();

            return response()->json($nivel, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 2){
                return response()->json('No se pude modificar este nivel', 400);
            }
            $nivel = Nivele::find($request['id']);
            $nivel->nombre = $request['nombre'];
            $nivel->save();

            return response()->json($nivel, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 2){
                return response()->json('No se pude eliminar este nivel', 400);
            }
            $nivel = Nivele::find($request['id']);
            $nivel->eliminado = 1;
            $nivel->save();

            return response()->json($nivel, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }
}