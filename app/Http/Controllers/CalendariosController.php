<?php

namespace App\Http\Controllers;
use App\Calendario;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendariosController extends BaseController
{
    
    function mostrar(){
        try{
            $calendarios = Calendario::where('eliminado', '=', '0')->get();
            return response()->json($calendarios, 200);
        }catch(Exception $e){
            return response()->json('Error al traer calendarios', 400);
        }
    }

    function nuevo(Request $request){
        try{
            $calendario = Calendario::create([
                'nombre' => $request['nombre'],
                'inicio' => $request['inicio'],
                'fin' => $request['fin'],
                'activo' => 1,
                'eliminado' => 0
            ]);

            return response()->json($calendario, 200);
        }catch(Exception $e){
            return response()->json("Error al crear calendario", 400);
        }
    }

    function activar(Request $request){
        try{
            $calendario = Calendario::find($request['id']);
            $calendario->activo = 1;
            $calendario->save();

            return response()->json($calendario, 200);
        }catch(Exception $e){
            return response()->json("Error al activar calendario", 400);
        }
    }

    function desactivar(Request $request){
        try{
            $calendario = Calendario::find($request['id']);
            $calendario->activo = 0;
            $calendario->save();

            return response()->json($calendario, 200);
        }catch(Exception $e){
            return response()->json("Error al desactivar calendario", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $calendario = Calendario::find($request['id']);
            $calendario->eliminado = 1;
            $calendario->save();

            return response()->json($calendario, 200);
        }catch(Exception $e){
            return response()->json("Error al eliminar calendario", 400);
        }
    }

    function modificar(Request $request){
        try{
            $calendario = Calendario::find($request['id']);
            $calendario->nombre = $request['nombre'];
            $calendario->inicio = $request['inicio'];
            $calendario->fin = $request['fin'];
            $calendario->save();
            
            return response()->json($calendario, 200);
        }catch(Exception $e){
            return response()->json("Error al eliminar calendario", 400);
        }
    }
}