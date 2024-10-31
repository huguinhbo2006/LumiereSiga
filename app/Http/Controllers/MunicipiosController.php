<?php

namespace App\Http\Controllers;
use App\Municipio;
use App\Estado;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MunicipiosController extends BaseController
{
    function mostrar(Request $request){
        try{
            $respuesta['datos'] = Municipio::where('eliminado', '=', 0)->get();
            $respuesta['lista'] = Estado::where('eliminado', '=', 0)->get();
            return response()->json($respuesta, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function nuevo(Request $request){
        try{
            $municipio = Municipio::create([
                'nombre' => $request['nombre'],
                'idEstado' => $request['idEstado'],
                'eliminado' => 0,
                'activo' => 1
            ]);

            return response()->json($municipio, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            $municipio = Municipio::find($request['id']);
            $municipio->activo = 1;
            $municipio->save();

            return response()->json($municipio, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            $municipio = Municipio::find($request['id']);
            $municipio->activo = 0;
            $municipio->save();

            return response()->json($municipio, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $municipio = Municipio::find($request['id']);
            $municipio->eliminado = 1;
            $municipio->save();

            return response()->json($municipio, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            $municipio = Municipio::find($request['id']);
            $municipio->nombre = $request['nombre'];
            $municipio->save();

            return response()->json($municipio, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

}