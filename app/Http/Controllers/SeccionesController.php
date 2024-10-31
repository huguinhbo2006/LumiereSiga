<?php

namespace App\Http\Controllers;
use App\Examene;
use App\Seccione;
use App\Calendario;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeccionesController extends BaseController
{
    function nuevo(Request $request){
        try{
            $seccion = Seccione::create([
                'nombre' => $request['nombre'],
                'tiempo' => $request['tiempo'],
                'idExamen' => $request['idExamen'],
                'instrucciones' => $request['instrucciones'],
                'valido' => !$request['valido'],
                'activo' => 1,
                'eliminado' => 0
            ]);
            return response()->json($seccion, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function mostrar(Request $request){
        try{
            $secciones = Seccione::where('idExamen', '=', $request['id'])->where('eliminado', '=', 0)->get();
            return response()->json($secciones, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function activar(Request $request) {
        try{
            $seccion = Seccione::find($request['id']);
            $seccion->activo = 1;
            $seccion->save();
            return response()->json($seccion, 200);
        }catch(Exception $e){
            return respone()->json('Error en el servidor', 400);
        }
    }

    function desactivar(Request $request) {
        try{
            $seccion = Seccione::find($request['id']);
            $seccion->activo = 0;
            $seccion->save();
            return response()->json($seccion, 200);
        }catch(Exception $e){
            return respone()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request) {
        try{
            $seccion = Seccione::find($request['id']);
            $seccion->eliminado = 1;
            $seccion->save();
            return response()->json($seccion, 200);
        }catch(Exception $e){
            return respone()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request) {
        try{
            $seccion = Seccione::find($request['id']);
            $seccion->nombre = $request['nombre'];
            $seccion->tiempo = $request['tiempo'];
            $seccion->valido = !$request['valido'];
            $seccion->instrucciones = $request['instrucciones'];
            $seccion->save();
            return response()->json($seccion, 200);
        }catch(Exception $e){
            return respone()->json('Error en el servidor', 400);
        }
    }
}