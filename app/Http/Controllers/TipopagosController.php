<?php

namespace App\Http\Controllers;
use App\Tipopago;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipopagosController extends BaseController
{
    function mostrar(Request $request){
        try{
            $tipos = Tipopago::where('eliminado', '=', 0)->get();
            return response()->json($tipos, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function nuevo(Request $request){
        try{
            $tipo = Tipopago::create([
                'nombre' => $request['nombre'],
                'comision' => $request['comision'],
                'valeCorte' => $request['valeCorte'],
                'corte' => ($request['valeCorte']) ? $request['corte'] : 0,
                'eliminado' => 0,
                'activo' => 1
            ]);

            return response()->json($tipo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            $tipo = Tipopago::find($request['id']);
            $tipo->activo = 1;
            $tipo->save();

            return response()->json($tipo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            $tipo = Tipopago::find($request['id']);
            $tipo->activo = 0;
            $tipo->save();

            return response()->json($tipo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $tipo = Tipopago::find($request['id']);
            $tipo->eliminado = 1;
            $tipo->save();

            return response()->json($tipo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            $tipo = Tipopago::find($request['id']);
            $tipo->nombre = $request['nombre'];
            $tipo->comision = $request['comision'];
            $tipo->valeCorte = $request['valeCorte'];
            $tipo->corte = ($request['valeCorte']) ? $request['corte'] : 0;
            $tipo->save();

            return response()->json($tipo, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

}