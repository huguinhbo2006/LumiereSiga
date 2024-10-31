<?php

namespace App\Http\Controllers;
use App\Paridade;
use App\Cursosparidade;
use App\Curso;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class ParidadesController extends BaseController
{
    function nuevo(Request $request){
    	try {
    		$paridad = Paridade::create([
    			'nombre' => $request['nombre'],
    			'eliminado' => 0,
    			'activo' => 1
    		]);

            return response()->json($paridad, 200);
    	} catch (Exception $e) {
    		return response()->json('Error en el servidor', 400);
    	}
    }

    function mostrar(){
    	try {
    		$paridades = Paridade::where('eliminado', '=', 0)->get();
    		return response()->json($paridades, 200);
    	} catch (Exception $e) {
    		return response()->json('Error en el servidor', 400);
    	}
    }

    function activar(Request $request){
    	try {
    		$paridad = Paridade::find($request['id']);
    		$paridad->activo = 1;
    		$paridad->save();

            return response()->json($paridad, 200);
    	} catch (Exception $e) {
    		return response()->json('Error en el servidor', 400);
    	}
    }

    function desactivar(Request $request){
    	try {
    		$paridad = Paridade::find($request['id']);
    		$paridad->activo = 0;
    		$paridad->save();

            return response()->json($paridad, 200);
    	} catch (Exception $e) {
    		return response()->json('Error en el servidor', 400);
    	}
    }

    function eliminar(Request $request){
    	try {
    		$paridad = Paridade::find($request['id']);
    		$paridad->eliminado = 1;
    		$paridad->save();

            return response()->json($paridad, 200);
    	} catch (Exception $e) {
    		return response()->json('Error en el servidor', 400);
    	}
    }

    function modificar(Request $request){
    	try {
    		$paridad = Paridade::find($request['id']);
    		$paridad->nombre = $request['nombre'];
    		$paridad->save();

            return response()->json($paridad, 200);
    	} catch (Exception $e) {
    		return response()->json('Error en el servidor', 400);
    	}
    }
}