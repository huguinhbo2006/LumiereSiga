<?php

namespace App\Http\Controllers;
use App\Campania;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CampaniasController extends BaseController
{
    function mostrar(){
        try{
            $campanias = Campania::where('eliminado', '=', 0)->get();
            return response()->json($campanias, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function nuevo(Request $request){
        try{
            $campania = Campania::create([
                'nombre' => $request['nombre'],
                'eliminado' => 0,
                'activo' => 1
            ]);

            return response()->json($campania, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            $campania = Campania::find($request['id']);
            $campania->activo = 1;
            $campania->save();

            return response()->json($campania, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            $campania = Campania::find($request['id']);
            $campania->activo = 0;
            $campania->save();

            return response()->json($campania, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $campania = Campania::find($request['id']);
            $campania->eliminado = 1;
            $campania->save();
            
            return response()->json($campania, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            $campania = Campania::find($request['id']);
            $campania->nombre = $request['nombre'];
            $campania->save();

            return response()->json($campania, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

}