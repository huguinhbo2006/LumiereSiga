<?php

namespace App\Http\Controllers;
use App\Tipoobjeto;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
include "funciones/FuncionesGenerales.php";

class TipoobjetosController extends BaseController
{
    function mostrar(Request $request){
        try{
            $tipos = Tipoobjeto::where('eliminado', '=', 0)->get();
            return response()->json($tipos, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function nuevo(Request $request){
        try{
            $tipo = Tipoobjeto::create([
                'nombre' => $request['nombre'],
                'eliminado' => 0,
                'activo' => 1
            ]);
            return response()->json($tipo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            $tipo = Tipoobjeto::find($request['id']);
            $tipo->activo = 1;
            $tipo->save();
            return response()->json($tipo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            $tipo = Tipoobjeto::find($request['id']);
            $tipo->activo = 0;
            $tipo->save();
            return response()->json($tipo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $tipo = Tipoobjeto::find($request['id']);
            $tipo->eliminado = 1;
            $tipo->save();
            return response()->json($tipo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            $tipo = Tipoobjeto::find($request['id']);
            $tipo->nombre = $request['nombre'];
            $tipo->save();
            return response()->json($tipo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

}