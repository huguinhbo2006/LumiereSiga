<?php

namespace App\Http\Controllers;
use App\Turno;
use App\Horario;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class TurnosController extends BaseController
{
    
    function mostrar(){
        try{
            $turnos = Turno::where('eliminado', '=', '0')->get();
            return response()->json($turnos, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function nuevo(Request $request){
        try{
            $turno = Turno::create([
                'nombre' => $request['nombre'],
                'activo' => 1,
                'eliminado' => 0
            ]);

            return response()->json($turno, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            $turno = Turno::find($request['id']);
            $turno->activo = 1;
            $turno->save();

            return response()->json($turno, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            $turno = Turno::find($request['id']);
            $turno->activo = 0;
            $turno->save();

            return response()->json($turno, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $turno = Turno::find($request['id']);
            $turno->eliminado = 1;
            $turno->save();

            return response()->json($turno, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            $turno = Turno::find($request['id']);
            $turno->nombre = $request['nombre'];
            $turno->save();

            return response()->json($turno, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }
}