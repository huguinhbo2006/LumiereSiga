<?php

namespace App\Http\Controllers;
use App\Motivosbachillerato;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MotivosbachilleratosController extends BaseController
{
    function mostrar(){
        try{
            $motivos = Motivosbachillerato::where('eliminado', '=', 0)->get();
            return response()->json($motivos, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function nuevo(Request $request){
        try{
            $motivo = Motivosbachillerato::create([
                'nombre' => $request['nombre'],
                'eliminado' => 0,
                'activo' => 1
            ]);

            return response()->json($motivo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            $motivo = Motivosbachillerato::find($request['id']);
            $motivo->activo = 1;
            $motivo->save();

            return response()->json($motivo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            $motivo = Motivosbachillerato::find($request['id']);
            $motivo->activo = 0;
            $motivo->save();

            return response()->json($motivo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $motivo = Motivosbachillerato::find($request['id']);
            $motivo->eliminado = 1;
            $motivo->save();

            return response()->json($motivo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            $motivo = Motivosbachillerato::find($request['id']);
            $motivo->nombre = $request['nombre'];
            $motivo->save();

            return response()->json($motivo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

}