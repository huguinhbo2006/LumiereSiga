<?php

namespace App\Http\Controllers;
use App\Tipocomputadora;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
include "funciones/FuncionesGenerales.php";

class TipocomputadorasController extends BaseController
{
    function mostrar(Request $request){
        try{
            $tipos = Tipocomputadora::where('eliminado', '=', 0)->get();
            return response()->json($tipos, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function nuevo(Request $request){
        try{
            $tipo = Tipocomputadora::create([
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
            $tipo = Tipocomputadora::find($request['id']);
            $tipo->activo = 1;
            $tipo->save();
            return response()->json($tipo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            $tipo = Tipocomputadora::find($request['id']);
            $tipo->activo = 0;
            $tipo->save();
            return response()->json($tipo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $tipo = Tipocomputadora::find($request['id']);
            $tipo->eliminado = 1;
            $tipo->save();
            return response()->json($tipo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            $tipo = Tipocomputadora::find($request['id']);
            $tipo->nombre = $request['nombre'];
            $tipo->save();
            return response()->json($tipo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

}