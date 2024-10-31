<?php

namespace App\Http\Controllers;
use App\Departamento;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class DepartamentosController extends BaseController
{
    function nuevo(Request $request){
        try{
            $departamento = Departamento::create([
                'nombre' => $request['nombre'],
                'abreviatura' => $request['abreviatura'],
                'activo' => 1,
                'eliminado' => 0
            ]);

            return response()->json($departamento, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function mostrar(){
        try{
            $departamentos =  Departamento::where('eliminado', '=', 0)->get();
            return response()->json($departamentos, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            if(intval($request['id']) === 1){
                return response()->json('No se puede activar este departamento', 400);
            }
            $departamento = Departamento::find($request['id']);
            $departamento->activo = 1;
            $departamento->save();

            return response()->json($departamento, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            if(intval($request['id']) === 1){
                return response()->json('No se puede desactivar este departamento', 400);
            }
            $departamento = Departamento::find($request['id']);
            $departamento->activo = 0;
            $departamento->save();

            return response()->json($departamento, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            if(intval($request['id']) === 1){
                return response()->json('No se puede eliminar este departamento', 400);
            }
            $departamento = Departamento::find($request['id']);
            $departamento->eliminado = 1;
            $departamento->save();

            return response()->json($departamento, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            if(intval($request['id']) === 1){
                return response()->json('No se puede modificar este departamento', 400);
            }
            $departamento = Departamento::find($request['id']);
            $departamento->nombre = $request['nombre'];
            $departamento->abreviatura = $request['abreviatura'];
            $departamento->save();

            return response()->json($departamento, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }
}