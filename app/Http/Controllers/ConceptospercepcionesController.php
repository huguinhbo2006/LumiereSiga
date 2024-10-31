<?php

namespace App\Http\Controllers;
use App\Conceptospercepcione;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class ConceptospercepcionesController extends BaseController
{
    function mostrar(){
        try{
            $conceptos = Conceptospercepcione::where('eliminado', '=', '0')->get();
            $respuesta = array();
            foreach ($conceptos as $concepto) {
                $concepto->personal = ($concepto->docentes === 1) ? 'Docente' : 'Administrativos';
                $respuesta[] = $concepto;
            }
            return response()->json($respuesta, 200);
        }catch(Exception $e){
            return response()->json('Error en le servidor', 400);
        }
    }

    function nuevo(Request $request){
        try{
            $concepto = Conceptospercepcione::create([
                'nombre' => $request['nombre'],
                'docentes' => $request['docentes'],
                'activo' => 1,
                'eliminado' => 0
            ]);

            return response()->json($concepto, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function activar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 2 || intval($request['id']) === 3){
                return response()->json('No se puede activar este concepto', 400);
            }
            $concepto = Conceptospercepcione::find($request['id']);
            $concepto->activo = 1;
            $concepto->save();

            return response()->json($concepto, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 2 || intval($request['id']) === 3){
                return response()->json('No se puede desactivar este concepto', 400);
            }
            $concepto = Conceptospercepcione::find($request['id']);
            $concepto->activo = 0;
            $concepto->save();

            return response()->json($concepto, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 2 || intval($request['id']) === 3){
                return response()->json('No se puede modificar este concepto', 400);
            }
            $concepto = Conceptospercepcione::find($request['id']);
            $concepto->nombre = $request['nombre'];
            $concepto->docentes = $request['docentes'];
            $concepto->save();

            return response()->json($concepto, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 2 || intval($request['id']) === 3){
                return response()->json('No se puede activar este concepto', 400);
            }
            $concepto = Conceptospercepcione::find($request['id']);
            $concepto->eliminado = 1;
            $concepto->save();

            return response()->json($concepto, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }
}