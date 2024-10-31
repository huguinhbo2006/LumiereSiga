<?php

namespace App\Http\Controllers;
use App\Estado;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstadosController extends BaseController
{
    function mostrar(){
        try{
            $estados = Estado::where('eliminado', '=', 0)->get();
            return response()->json($estados, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function nuevo(Request $request){
        try{
            $estado = Estado::create([
                'nombre' => $request['nombre'],
                'eliminado' => 0,
                'activo' => 1
            ]);

            return response()->json($estado, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            $estado = Estado::find($request['id']);
            $estado->activo = 1;
            $estado->save();

            return response()->json($estado, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            $estado = Estado::find($request['id']);
            $estado->activo = 0;
            $estado->save();

            return response()->json($estado, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $estado = Estado::find($request['id']);
            $estado->eliminado = 1;
            $estado->save();

            return response()->json($estado, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            $estado = Estado::find($request['id']);
            $estado->nombre = $request['nombre'];
            $estado->save();

            return response()->json($estado, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

}