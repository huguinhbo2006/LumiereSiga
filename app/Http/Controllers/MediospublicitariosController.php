<?php

namespace App\Http\Controllers;
use App\Mediospublicitario;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MediospublicitariosController extends BaseController
{
    function mostrar(){
        try{
            $medios = Mediospublicitario::where('eliminado', '=', 0)->get();
            return response()->json($medios, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function nuevo(Request $request){
        try{
            $medio = Mediospublicitario::create([
                'nombre' => $request['nombre'],
                'eliminado' => 0,
                'activo' => 1
            ]);

            return response()->json($medio, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            $medio = Mediospublicitario::find($request['id']);
            $medio->activo = 1;
            $medio->save();

            return response()->json($medio, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            $medio = Mediospublicitario::find($request['id']);
            $medio->activo = 0;
            $medio->save();

            return response()->json($medio, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $medio = Mediospublicitario::find($request['id']);
            $medio->eliminado = 1;
            $medio->save();

            return response()->json($medio, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            $medio = Mediospublicitario::find($request['id']);
            $medio->nombre = $request['nombre'];
            $medio->save();

            return response()->json($medio, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

}