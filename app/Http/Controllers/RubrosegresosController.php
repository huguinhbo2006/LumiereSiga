<?php

namespace App\Http\Controllers;
use App\Rubrosegreso;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RubrosegresosController extends BaseController
{
    function mostrar(){
        try{
            $rubros = Rubrosegreso::where('eliminado', '=', 0)->get();
            return response()->json($rubros, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function nuevo(Request $request){
        try{
            $rubro = Rubrosegreso::create([
                'nombre' => $request['nombre'],
                'eliminado' => 0,
                'activo' => 1
            ]);

            return response()->json($rubro, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 2){
                return response()->json('No se puede activar este rubro', 400);
            }
            $rubro = Rubrosegreso::find($request['id']);
            $rubro->activo = 1;
            $rubro->save();

            return response()->json($rubro, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 2){
                return response()->json('No se puede desactivar este rubro', 400);
            }
            $rubro = Rubrosegreso::find($request['id']);
            $rubro->activo = 0;
            $rubro->save();

            return response()->json($rubro, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 2){
                return response()->json('No se puede eliminar este rubro', 400);
            }
            $rubro = Rubrosegreso::find($request['id']);
            $rubro->eliminado = 1;
            $rubro->save();

            return response()->json($rubro, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 2){
                return response()->json('No se puede modificar este rubro', 400);
            }
            $rubro = Rubrosegreso::find($request['id']);
            $rubro->nombre = $request['nombre'];
            $rubro->save();

            return response()->json($rubro, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

}