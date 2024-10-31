<?php

namespace App\Http\Controllers;
use App\Categoria;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class CategoriasController extends BaseController
{
    
    function mostrar(){
        try{
            $categorias = Categoria::where('eliminado', '=', '0')->get();
            return response()->json($categorias, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function nuevo(Request $request){
        try{
            $categoria = Categoria::create([
                'nombre' => $request['nombre'],
                'activo' => 1,
                'eliminado' => 0
            ]);

            return response()->json($categoria, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function activar(Request $request){
        try{
            $categoria = Categoria::find($request['id']);
            $categoria->activo = 1;
            $categoria->save();

            return response()->json($categoria, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function desactivar(Request $request){
        try{
            $categoria = Categoria::find($request['id']);
            $categoria->activo = 0;
            $categoria->save();

            return response()->json($categoria, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request){
        try{
            $categoria = Categoria::find($request['id']);
            $categoria->eliminado = 1;
            $categoria->save();

            return response()->json($categoria, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request){
        try{
            $categoria = Categoria::find($request['id']);
            $categoria->nombre = $request['nombre'];
            $categoria->save();

            return response()->json($categoria, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }
}