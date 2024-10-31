<?php

namespace App\Http\Controllers;
use App\Modulo;
use App\Opcione;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class ModulosController extends BaseController
{
    function nuevo(Request $request){
        try{
            $modulo = Modulo::create([
                'nombre' => $request['nombre'],
                'icono' => $request['icono'],
                'color' => $request['color'],
                'identificador' => $request['identificador'],
                'activo' => 1,
                'eliminado' => 0
            ]);
            response()->json($modulo, 200);
        }catch(Exception $e){
            response()->json('Error en el servidor', 400);
        }
    }

    function mostrar(){
        try{
            $modulos =  Modulo::where('eliminado', '=', 0)->get();
            return response()->json($modulos, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            $modulo = Modulo::find($request['id']);
            $modulo->activo = 1;
            $modulo->save();
            return response()->json($modulo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            $modulo = Modulo::find($request['id']);
            $modulo->activo = 0;
            $modulo->save();
            return response()->json($modulo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $modulo = Modulo::find($request['id']);
            $modulo->eliminado = 1;
            $modulo->save();
            return response()->json($modulo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            $modulo = Modulo::find($request['id']);
            $modulo->nombre = $request['nombre'];
            $modulo->icono = $request['icono'];
            $modulo->identificador = $request['identificador'];
            $modulo->color = $request['color'];
            $modulo->save();
            return response()->json($modulo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function traer(){
        try{
            $resultado = array();
            $final = array();
            $modulos =  Modulo::where('eliminado', '=', 0)->get();
            foreach ($modulos as $modulo) {
                $resultado = $modulo;
                $opciones = Opcione::where('eliminado', '=', 0)->where('idModulo', '=', $modulo['id'])->get();
                $resultado['opciones'] = $opciones;
                $final[] = $resultado;
            }
            return response()->json($final, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }
}