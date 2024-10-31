<?php

namespace App\Http\Controllers;
use App\Conceptosabono;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class ConceptosabonosController extends BaseController
{
    function mostrar(){
        try{
            $conceptos = Conceptosabono::where('eliminado', '=', '0')->get();
            return response()->json($conceptos, 200);
        }catch(Exception $e){
            return response()->json('Error en le servidor', 400);
        }
    }

    function nuevo(Request $request){
        try{
            $concepto = Conceptosabono::create([
                'nombre' => $request['nombre'],
                'activo' => 1,
                'eliminado' => 0
            ]);

            return response()->json($concepto, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function activar(Request $request){
        try{
            $concepto = Conceptosabono::find($request['id']);
            $concepto->activo = 1;
            $concepto->save();

            return response()->json($concepto, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            $concepto = Conceptosabono::find($request['id']);
            $concepto->activo = 0;
            $concepto->save();

            return response()->json($concepto, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            $concepto = Conceptosabono::find($request['id']);
            $concepto->nombre = $request['nombre'];
            $concepto->save();

            return response()->json($concepto, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $concepto = Conceptosabono::find($request['id']);
            $concepto->eliminado = 1;
            $concepto->save();

            return response()->json($concepto, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }
}