<?php

namespace App\Http\Controllers;
use App\Lectura;
use App\Seccione;
use App\Examen;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class LecturasController extends BaseController
{
    function nuevo(Request $request){
    	try{
    		$lectura = Lectura::create([
                'idExamen' => $request['idExamen'],
                'idSeccion' => $request['idSeccion'],
    			'nombre' => $request['nombre'],
                'contenido' => $request['contenido'],
                'tipo' => $request['tipo'],
    			'activo' => 1,
    			'eliminado' => 0,
    		]);
    		return response()->json($lectura, 200);
    	}catch(Exception $e){
    		return response()->json("Error en el servidor", 400);
    	}
    }

    function mostrar(Request $request){
    	try{
            //return response()->json($request, 400);
    		$lecturas = Lectura::where('idSeccion', '=', $request['idSeccion'])->where('eliminado', '=', 0)->get();
            $respuesta = array();
            foreach ($lecturas as $lectura) {
                $lectura->descripcion = (intval($lectura->tipo) === 2) ? "<img src='$lectura->contenido' />" : str_replace('<br>', '\n', $lectura->contenido);
                $respuesta[] = $lectura;
            }
    		return response()->json($respuesta, 200);
    	}catch(Exception $e){
    		return response()->json("Error en el servidor", 400);
    	}
    }

    function modificar(Request $request){
        try{
            $lectura = Lectura::find($request['id']);
            $lectura->nombre = $request['nombre'];
            $lectura->contenido = $request['contenido'];
            $lectura->tipo = $request['tipo'];
            $lectura->save();
            return response()->json($lectura, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
    	try{
            $lectura = Lectura::find($request['id']);
            $lectura->eliminado = 1;
            $lectura->save();
            return response()->json($lectura, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    
}