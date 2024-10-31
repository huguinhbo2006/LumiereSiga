<?php

namespace App\Http\Controllers;
use App\Universidade;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UniversidadesController extends BaseController
{

    function nuevo(Request $request){
        try{
            $centro = Universidade::create([
                'nombre' => $request['nombre'],
                'siglas' => $request['siglas'],
                'eliminado' => 0,
                'activo' => 1
            ]);

            return response()->json($centro, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function mostrar(){
        try{
            $universidades = Universidade::where('eliminado', '=' , 0)->get();
            return response()->json($universidades, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            $centro = Universidade::find($request['id']);
            $centro->nombre = $request['nombre'];
            $centro->siglas = $request['siglas'];
            $centro->save();

            return response()->json($centro, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $centro = Universidade::find($request['id']);
            $centro->eliminado = 1;
            $centro->save();

            return response()->json($centro, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            $centro = Universidade::find($request['id']);
            $centro->activo = 1;
            $centro->save();

            return response()->json($centro, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            $centro = Universidade::find($request['id']);
            $centro->activo = 0;
            $centro->save();

            return response()->json($centro, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }
}