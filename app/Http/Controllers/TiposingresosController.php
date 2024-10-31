<?php

namespace App\Http\Controllers;
use App\Tiposingreso;
use App\Rubro;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TiposingresosController extends BaseController
{
    function mostrar(){
        try{
            $respuesta['datos'] = Tiposingreso::where('eliminado', '=', 0)->get();
            $respuesta['lista'] = Rubro::where('eliminado', '=', 0)->get();
            return response()->json($respuesta, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function nuevo(Request $request){
        try{
            $tipoIngreso = Tiposingreso::create([
                'nombre' => $request['nombre'],
                'idRubro' => $request['idRubro'],
                'eliminado' => 0,
                'activo' => 1
            ]);

            return response()->json($tipoIngreso, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 2 || intval($request['id']) === 3 || intval($request['id']) === 4){
                return response()->json('No se puede activar este tipo de ingreso', 400);
            }
            $tipoIngreso = Tiposingreso::find($request['id']);
            $tipoIngreso->activo = 1;
            $tipoIngreso->save();

            return response()->json($tipoIngreso, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 2 || intval($request['id']) === 3 || intval($request['id']) === 4){
                return response()->json('No se puede desactivar este tipo de ingreso', 400);
            }
            $tipoIngreso = Tiposingreso::find($request['id']);
            $tipoIngreso->activo = 0;
            $tipoIngreso->save();

            return response()->json($tipoIngreso, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 2 || intval($request['id']) === 3 || intval($request['id']) === 4){
                return response()->json('No se puede eliminar este tipo de ingreso', 400);
            }
            $tipoIngreso = Tiposingreso::find($request['id']);
            $tipoIngreso->eliminado = 1;
            $tipoIngreso->save();

            return response()->json($tipoIngreso, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 2 || intval($request['id']) === 3 || intval($request['id']) === 4){
                return response()->json('No se puede modificar este tipo de ingreso', 400);
            }
            $tipoIngreso = Tiposingreso::find($request['id']);
            $tipoIngreso->nombre = $request['nombre'];
            $tipoIngreso->idRubro = $request['idRubro'];
            $tipoIngreso->save();

            return response()->json($tipoIngreso, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

}