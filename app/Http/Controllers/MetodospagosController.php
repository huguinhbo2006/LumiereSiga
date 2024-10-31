<?php

namespace App\Http\Controllers;
use App\Metodospago;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class metodosPagosController extends BaseController
{
    function mostrar(){
        try{
            $metodosPagos = Metodospago::where('eliminado', '=', 0)->get();
            return response()->json($metodosPagos, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function nuevo(Request $request){
        try{
            $metodoPago = Metodospago::create([
                'nombre' => $request['nombre'],
                'eliminado' => 0,
                'activo' => 1
            ]);

            return response()->json($metodoPago, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 2){
                return response()->json('No se puede activar este metodo de pago', 400);
            }
            $metodoPago = Metodospago::find($request['id']);
            $metodoPago->activo = 1;
            $metodoPago->save();

            return response()->json($metodoPago, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 2){
                return response()->json('No se puede desactivar este metodo de pago', 400);
            }
            $metodoPago = Metodospago::find($request['id']);
            $metodoPago->activo = 0;
            $metodoPago->save();

            return response()->json($metodoPago, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 2){
                return response()->json('No se puede eliminar este metodo de pago', 400);
            }
            $metodoPago = Metodospago::find($request['id']);
            $metodoPago->eliminado = 1;
            $metodoPago->save();

            return response()->json($metodoPago, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 2){
                return response()->json('No se puede modificar este metodo de pago', 400);
            }
            $metodoPago = Metodospago::find($request['id']);
            $metodoPago->nombre = $request['nombre'];
            $metodoPago->save();

            return response()->json($metodoPago, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

}