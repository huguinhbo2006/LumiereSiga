<?php

namespace App\Http\Controllers;
use App\Banco;
use App\Log;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class BancosController extends BaseController
{
    function nuevo(Request $request){
        try{
            $banco = Banco::create([
                'nombre' => $request['nombre'],
                'activo' => 1,
                'eliminado' => 0
            ]);

            return response()->json($banco, 200);
        }catch(Exception $e){
            return response()->json('Error de servidor', 400);
        }
    }

    function mostrar(){
        try {
            $bancos = Banco::where('eliminado', '=', 0)->get();
            return response()->json($bancos, 200);
        } catch (Exception $e) {
            return response()->json('Error de servidor', 400);
        }
    }

    function activar(Request $request){
        try{
            $banco = Banco::find($request['id']);
            $banco->activo = 1;
            $banco->save();

            return response()->json($banco, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function desactivar(Request $request){
        try{
            $banco = Banco::find($request['id']);
            $banco->activo = 0;
            $banco->save();

            return response()->json($banco, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request){
        try{
            $banco = Banco::find($request['id']);
            $banco->eliminado = 1;
            $banco->save();

            return response()->json($banco, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request){
        try{
            $banco = Banco::find($request['id']);
            $banco->nombre = $request['nombre'];
            $banco->save();
            
            return response()->json($banco, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }
}