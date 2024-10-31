<?php

namespace App\Http\Controllers;
use App\Empresascurso;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmpresascursosController extends BaseController
{
    function mostrar(){
        try{
            $empresas = Empresascurso::where('eliminado', '=', 0)->get();
            return response()->json($empresas, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function nuevo(Request $request){
        try{
            $empresa = Empresascurso::create([
                'nombre' => $request['nombre'],
                'eliminado' => 0,
                'activo' => 1
            ]);

            return response()->json($empresa, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            $empresa = Empresascurso::find($request['id']);
            $empresa->activo = 1;
            $empresa->save();

            return response()->json($empresa, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            $empresa = Empresascurso::find($request['id']);
            $empresa->activo = 0;
            $empresa->save();

            return response()->json($empresa, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $empresa = Empresascurso::find($request['id']);
            $empresa->eliminado = 1;
            $empresa->save();

            return response()->json($empresa, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            $empresa = Empresascurso::find($request['id']);
            $empresa->nombre = $request['nombre'];
            $empresa->save();

            return response()->json($empresa, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

}