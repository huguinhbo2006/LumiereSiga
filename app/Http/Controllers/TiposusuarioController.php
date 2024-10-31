<?php

namespace App\Http\Controllers;
use App\Tipousuario;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class TiposusuarioController extends BaseController
{
    function nuevo(Request $request){
    	try{
    		$tipo = Tipousuario::create([
    			'nombre' => $request['nombre'],
    			'eliminado' => 0,
    			'activo' => 1
    		]);

            return response()->json($request, 200);
    	}catch(Exception $e){
    		return response()->json("Error en el servidor", 400);
    	}
    }

    function mostrar(){
    	try{
    		$tipos = Tipousuario::where('eliminado', '=', 0)->take(100)->get();
    		return response()->json($tipos, 200);
    	}catch(Exception $e){
    		return response()->json("Error en el servidor", 400);
    	}
    }

    function modificar(Request $request){
        try{
            $tipo = Tipousuario::find($request['id']);
            $tipo->nombre = $request['nombre'];
            $tipo->save();

            return response()->json($tipo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            $tipo = Tipousuario::find($request['id']);
            $tipo->activo = 1;
            $tipo->save();

            return response()->json($tipo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            $tipo = Tipousuario::find($request['id']);
            $tipo->activo = 0;
            $tipo->save();

            return response()->json($tipo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $tipo = Tipousuario::find($request['id']);
            $tipo->eliminado = 1;
            $tipo->save();

            return response()->json($tipo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }
}