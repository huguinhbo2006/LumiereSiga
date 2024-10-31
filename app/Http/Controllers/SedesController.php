<?php

namespace App\Http\Controllers;
use App\Sede;
use App\Clases\Imagenes;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class SedesController extends BaseController
{
    
    function mostrar(){
        try{
            $sedes = Sede::where('eliminado', '=', '0')->get();
            return response()->json($sedes, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function nuevo(Request $request){
        try{
            $sedes = Sede::create([
                'nombre' => $request['nombre'],
                'imagen' => $request['imagen'],
                'activo' => 1,
                'eliminado' => 0
            ]);

            return response()->json($sede, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function activar(Request $request){
        try{
            $sede = Sede::find($request['id']);
            $sede->activo = 1;
            $sede->save();

            return response()->json($sede, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function desactivar(Request $request){
        try{
            $sede = Sede::find($request['id']);
            $sede->activo = 0;
            $sede->save();

            return response()->json($sede, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request){
        try{
            $sede = Sede::find($request['id']);
            $sede->eliminado = 1;
            $sede->save();

            return response()->json($sede, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request){
        try{
            $sede = Sede::find($request['id']);
            $sede->nombre = $request['nombre'];
            $sede->imagen = $request['imagen'];
            $sede->save();

            return response()->json($sede, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }
}