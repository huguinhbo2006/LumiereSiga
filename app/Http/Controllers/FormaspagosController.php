<?php

namespace App\Http\Controllers;
use App\Formaspago;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormaspagosController extends BaseController
{
    function mostrar(){
        try{
            $formasPagos = Formaspago::where('eliminado', '=', 0)->get();
            return response()->json($formasPagos, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function nuevo(Request $request){
        try{
            $formaPago = Formaspago::create([
                'nombre' => $request['nombre'],
                'eliminado' => 0,
                'activo' => 1
            ]);

            return response()->json($formaPago, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 4){
                return response()->json('No se puede activar esta forma de pago', 400);
            }
            $formaPago = Formaspago::find($request['id']);
            $formaPago->activo = 1;
            $formaPago->save();

            return response()->json($formaPago, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 4){
                return response()->json('No se puede desactivar esta forma de pago', 400);
            }
            $formaPago = Formaspago::find($request['id']);
            $formaPago->activo = 0;
            $formaPago->save();

            return response()->json($formaPago, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 4){
                return response()->json('No se puede eliminar esta forma de pago', 400);
            }
            $formaPago = Formaspago::find($request['id']);
            $formaPago->eliminado = 1;
            $formaPago->save();

            return response()->json($formaPago, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 4){
                return response()->json('No se puede modificar esta forma de pago', 400);
            }
            $formaPago = Formaspago::find($request['id']);
            $formaPago->nombre = $request['nombre'];
            $formaPago->save();

            return response()->json($formaPago, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    } 
}