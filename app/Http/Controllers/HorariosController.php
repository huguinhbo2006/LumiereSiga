<?php

namespace App\Http\Controllers;
use App\Horario;
use App\Turno;
use Illuminate\Support\Facades\DB;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class HorariosController extends BaseController
{

    function mostrar(Request $request){
        try{

            $respuesta['datos'] = Horario::select(
                'horarios.*',
                DB::raw('CONCAT(horarios.inicio, " - ", horarios.fin) as nombre')
            )->where('eliminado', '=', 0)->get();
            $respuesta['lista'] = Turno::where('eliminado', '=', 0)->get();
            return response()->json($respuesta, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function nuevo(Request $request){
        try{
            $horario = Horario::create([
                'inicio' => $request['inicio'],
                'fin' => $request['fin'],
                'idTurno' => $request['idTurno'],
                'activo' => 1,
                'eliminado' => 0
            ]);
            $horario->nombre = $horario->inicio.' - '.$horario->fin;

            return response()->json($horario, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            $horario = Horario::find($request['id']);
            $horario->activo = 1;
            $horario->save();
            $horario->nombre = $horario->inicio.' - '.$horario->fin;

            return response()->json($horario, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            $horario = Horario::find($request['id']);
            $horario->activo = 0;
            $horario->save();
            $horario->nombre = $horario->inicio.' - '.$horario->fin;

            return response()->json($horario, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $horario = Horario::find($request['id']);
            $horario->eliminado = 1;
            $horario->save();
            $horario->nombre = $horario->inicio.' - '.$horario->fin;

            return response()->json($horario, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            $horario = Horario::find($request['id']);
            $horario->inicio = $request['inicio'];
            $horario->fin = $request['fin'];
            $horario->save();
            $horario->nombre = $horario->inicio.' - '.$horario->fin;

            return response()->json($horario, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }
}