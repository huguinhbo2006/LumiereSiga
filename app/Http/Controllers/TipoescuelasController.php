<?php

namespace App\Http\Controllers;
use App\Tipoescuela;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipoescuelasController extends BaseController
{
    function mostrar(Request $request){
        try{
            $tiposEscuelas = Tipoescuela::where('eliminado', '=', 0)->get();
            return response()->json($tiposEscuelas, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function nuevo(Request $request){
        try{
            $tipoEscuela = Tipoescuela::create([
                'nombre' => $request['nombre'],
                'eliminado' => 0,
                'activo' => 1
            ]);

            return response()->json($tipoEscuela, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            $tipoEscuela = Tipoescuela::find($request['id']);
            $tipoEscuela->activo = 1;
            $tipoEscuela->save();

            return response()->json($tipoEscuela, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            $tipoEscuela = Tipoescuela::find($request['id']);
            $tipoEscuela->activo = 0;
            $tipoEscuela->save();

            return response()->json($tipoEscuela, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $tipoEscuela = Tipoescuela::find($request['id']);
            $tipoEscuela->eliminado = 1;
            $tipoEscuela->save();

            return response()->json($tipoEscuela, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            $tipoEscuela = Tipoescuela::find($request['id']);
            $tipoEscuela->nombre = $request['nombre'];
            $tipoEscuela->save();

            return response()->json($tipoEscuela, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

}