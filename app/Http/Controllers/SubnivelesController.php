<?php

namespace App\Http\Controllers;
use App\Subnivele;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class SubnivelesController extends BaseController
{
    function mostrar(){
        try{
            $subniveles = Subnivele::where('eliminado', '=', '0')->get();
            return response()->json($subniveles, 200);
        }catch(Exception $e){
            return response()->json('Error en le servidor', 400);
        }
    }

    function nuevo(Request $request){
        try{
            $subnivel = Subnivele::create([
                'nombre' => $request['nombre'],
                'activo' => 1,
                'eliminado' => 0
            ]);

            return response()->json($subnivel, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request){
        try{
            $subnivel = Subnivele::find($request['id']);
            $subnivel->eliminado = 1;
            $subnivel->save();

            return response()->json($subnivel, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request){
        try{
            $subnivel = Subnivele::find($request['id']);
            $subnivel->nombre = $request['nombre'];
            $subnivel->save();

            return response()->json($subnivel, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function activar(Request $request){
        try{
            $subnivel = Subnivele::find($request['id']);
            $subnivel->activo = 1;
            $subnivel->save();

            return response()->json($subnivel, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function desactivar(Request $request){
        try{
            $subnivel = Subnivele::find($request['id']);
            $subnivel->activo = 0;
            $subnivel->save();

            return response()->json($subnivel, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }
}