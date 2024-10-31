<?php

namespace App\Http\Controllers;
use App\Tiposegreso;
use App\Rubrosegreso;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TiposegresosController extends BaseController
{
    function mostrar(Request $request){
        try{
            $respuesta['datos'] = Tiposegreso::where('eliminado', '=', 0)->get();
            $respuesta['lista'] = Rubrosegreso::where('eliminado', '=', 0)->get();
            return response()->json($respuesta, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function nuevo(Request $request){
        try{
            $tipoEgreso = Tiposegreso::create([
                'nombre' => $request['nombre'],
                'idRubro' => $request['idRubro'],
                'eliminado' => 0,
                'activo' => 1
            ]);

            return response()->json($tipoEgreso, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 2 || intval($request['id']) === 3 || intval($request['id']) === 4){
                return response()->json('No se puede activar este tipo de egreso', 400);
            }
            $tipoEgreso = Tiposegreso::find($request['id']);
            $tipoEgreso->activo = 1;
            $tipoEgreso->save();

            return response()->json($tipoEgreso, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 2 || intval($request['id']) === 3 || intval($request['id']) === 4){
                return response()->json('No se puede desactivar este tipo de egreso', 400);
            }
            $tipoEgreso = Tiposegreso::find($request['id']);
            $tipoEgreso->activo = 0;
            $tipoEgreso->save();

            return response()->json($tipoEgreso, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 2 || intval($request['id']) === 3 || intval($request['id']) === 4){
                return response()->json('No se puede eliminar este tipo de egreso', 400);
            }
            $tipoEgreso = Tiposegreso::find($request['id']);
            $tipoEgreso->eliminado = 1;
            $tipoEgreso->save();

            return response()->json($tipoEgreso, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 2 || intval($request['id']) === 3 || intval($request['id']) === 4){
                return response()->json('No se puede modificar este tipo de egreso', 400);
            }
            $tipoEgreso = Tiposegreso::find($request['id']);
            $tipoEgreso->nombre = $request['nombre'];
            $tipoEgreso->idRubro = $request['idRubro'];
            $tipoEgreso->save();

            return response()->json($tipoEgreso, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

}