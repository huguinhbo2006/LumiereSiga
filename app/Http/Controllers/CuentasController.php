<?php

namespace App\Http\Controllers;
use App\Cuenta;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class CuentasController extends BaseController
{
    function nuevo(Request $request){
        try{
            $cuenta = Cuenta::create([
                'nombre' => $request['nombre'],
                'cuenta' => $request['cuenta'],
                'activo' => 1,
                'eliminado' => 0
            ]);

            return response()->json($cuenta, 200);
        }catch(Exception $e){
            return response()->json('Error de servidor', 400);
        }
    }

    function mostrar(Request $request){
        try {
            $cuentas = Cuenta::where('eliminado', '=', 0)->get();
            return response()->json($cuentas, 200);
        } catch (Exception $e) {
            return response()->json('Error de servidor', 400);
        }
    }

    function activar(Request $request){
        try{
            $cuenta = Cuenta::find($request['id']);
            $cuenta->activo = 1;
            $cuenta->save();

            return response()->json($cuenta, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function desactivar(Request $request){
        try{
            $cuenta = Cuenta::find($request['id']);
            $cuenta->activo = 0;
            $cuenta->save();

            return response()->json($cuenta, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request){
        try{
            $cuenta = Cuenta::find($request['id']);
            $cuenta->eliminado = 1;
            $cuenta->save();

            return response()->json($cuenta, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request){
        try{
            $cuenta = Cuenta::find($request['id']);
            $cuenta->cuenta = $request['cuenta'];
            $cuenta->nombre = $request['nombre'];
            $cuenta->save();

            return response()->json($cuenta, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }
}