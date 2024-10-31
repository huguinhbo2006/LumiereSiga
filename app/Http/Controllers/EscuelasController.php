<?php

namespace App\Http\Controllers;
use App\Escuela;
use App\Tipoescuela;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EscuelasController extends BaseController
{
    function mostrar(){
        try{
            $respuesta['datos'] = Escuela::where('eliminado', '=', 0)->get();;
            $respuesta['lista'] = Tipoescuela::where('eliminado', '=', 0)->get();;
            return response()->json($respuesta, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function nuevo(Request $request){
        try{
            $escuela = Escuela::create([
                'nombre' => $request['nombre'],
                'idTipo' => $request['idTipo'],
                'grado' => $request['grado'],
                'activo' => 1,
                'eliminado' => 0
            ]);

            return response()->json($escuela, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request) {
        try{
            $escuela = Escuela::find($request['id']);
            $escuela->eliminado = 1;
            $escuela->save();

            return response()->json($escuela, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request) {
        try{
            $escuela = Escuela::find($request['id']);
            $escuela->activo = 1;
            $escuela->save();

            return response()->json($escuela, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request) {
        try{
            $escuela = Escuela::find($request['id']);
            $escuela->activo = 0;
            $escuela->save();

            return response()->json($escuela, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request) {
        try{
            $escuela = Escuela::find($request['id']);
            $escuela->nombre = $request['nombre'];
            $escuela->idTipo = $request['idTipo'];
            $escuela->grado = $request['grado'];
            $escuela->save();

            return response()->json($escuela, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }
}