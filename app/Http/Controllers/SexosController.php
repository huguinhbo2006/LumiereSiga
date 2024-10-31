<?php

namespace App\Http\Controllers;
use App\Sexo;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class SexosController extends BaseController
{
    function mostrar(){
        try{
            $sexos = Sexo::where('eliminado', '=', 0)->get();
            return response()->json($sexos, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function nuevo(Request $request){
        try{
            $sexo = Sexo::create([
                'nombre' => $request['nombre'],
                'eliminado' => 0,
                'activo' => 1
            ]);


            return response()->json($sexo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            $sexo = Sexo::find($request['id']);
            $sexo->activo = 1;
            $sexo->save();

            return response()->json($sexo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            $sexo = Sexo::find($request['id']);
            $sexo->activo = 0;
            $sexo->save();

            return response()->json($sexo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $sexo = Sexo::find($request['id']);
            $sexo->eliminado = 1;
            $sexo->save();

            return response()->json($sexo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            $sexo = Sexo::find($request['id']);
            $sexo->nombre = $request['nombre'];
            $sexo->save();

            return response()->json($sexo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

}