<?php

namespace App\Http\Controllers;
use App\Conceptosdescuento;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class ConceptosdescuentosController extends BaseController
{
    function mostrar(){
        try{
            $conceptos = Conceptosdescuento::where('eliminado', '=', '0')->get();
            return response()->json($conceptos, 200);
        }catch(Exception $e){
            return response()->json('Error en le servidor', 400);
        }
    }

    function nuevo(Request $request){
        try{
            $concepto = Conceptosdescuento::create([
                'nombre' => $request['nombre'],
                'tipo' => $request['tipo'],
                'monto' => $request['monto'],
                'forzar' => $request['forzar'],
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
            $concepto = Conceptosdescuento::find($request['id']);
            $concepto->activo = 1;
            $concepto->save();

            return response()->json($concepto, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            $concepto = Conceptosdescuento::find($request['id']);
            $concepto->activo = 0;
            $concepto->save();

            return response()->json($concepto, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            $concepto = Conceptosdescuento::find($request['id']);
            $concepto->nombre = $request['nombre'];
            $concepto->tipo = $request['tipo'];
            $concepto->monto = $request['monto'];
            $concepto->forzar = $request['forzar'];
            $concepto->save();

            return response()->json($concepto, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $concepto = Conceptosdescuento::find($request['id']);
            $concepto->eliminado = 1;
            $concepto->save();

            return response()->json($concepto, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }
}