<?php

namespace App\Http\Controllers;
use App\Marcaprocesadore;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
include "funciones/FuncionesGenerales.php";

class MarcaprocesadoresController extends BaseController
{
    function mostrar(Request $request){
        try{
            $marcas = Marcaprocesadore::where('eliminado', '=', 0)->get();
            return response()->json($marcas, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function nuevo(Request $request){
        try{
            $marca = Marcaprocesadore::create([
                'nombre' => $request['nombre'],
                'eliminado' => 0,
                'activo' => 1
            ]);
            return response()->json($marca, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            $marca = Marcaprocesadore::find($request['id']);
            $marca->activo = 1;
            $marca->save();
            return response()->json($marca, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            $marca = Marcaprocesadore::find($request['id']);
            $marca->activo = 0;
            $marca->save();
            return response()->json($marca, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $marca = Marcaprocesadore::find($request['id']);
            $marca->eliminado = 1;
            $marca->save();
            return response()->json($marca, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            $marca = Marcaprocesadore::find($request['id']);
            $marca->nombre = $request['nombre'];
            $marca->save();
            return response()->json($marca, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

}