<?php

namespace App\Http\Controllers;
use App\Opcione;
use App\Modulo;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class OpcionesController extends BaseController
{
    function mostrar(){
        $respuesta['datos'] = Opcione::where('eliminado', '=', 0)->get();
        $respuesta['lista'] = Modulo::where('eliminado', '=', 0)->get();
        return response()->json($respuesta);
    }

    function nuevo(Request $request){
        try{
            $opcion = Opcione::create([
                'nombre' => $request['nombre'],
                'icono' => $request['icono'],
                'color' => $request['color'],
                'ruta' => $request['ruta'],
                'idModulo' => $request['idModulo'],
                'activo' => 1,
                'eliminado' => 0
            ]);
            return response()->json($opcion, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request){
        try {
            $opcion = Opcione::find($request['id']);
            $opcion->nombre = $request['nombre'];
            $opcion->icono = $request['icono'];
            $opcion->color = $request['color'];
            $opcion->ruta = $request['ruta'];
            $opcion->save();

            return response()->json($opcion, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 200);
        }
    }

    function activar(Request $request){
        try{
            $opcion = Opcione::find($request['id']);
            $opcion->activo = 1;
            $opcion->save();
            return response()->json($opcion, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            $opcion = Opcione::find($request['id']);
            $opcion->activo = 0;
            $opcion->save();
            return response()->json($opcion, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $opcion = Opcione::find($request['id']);
            $opcion->eliminado = 1;
            $opcion->save();
            return response()->json($opcion, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }
}