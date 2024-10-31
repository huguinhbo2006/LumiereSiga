<?php

namespace App\Http\Controllers;
use App\Modalidade;
use App\Modalidaddia;
use App\Horario;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class ModalidadesController extends BaseController
{
    
    function mostrar(){
        try{
            $modalidades = Modalidade::where('eliminado', '=', '0')->get();
            return response()->json($modalidades, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function nueva(Request $request){
        try{
            $modalidad = Modalidade::create([
                'nombre' => $request['nombre'],
                'activo' => 1,
                'eliminado' => 0
            ]);

            return response()->json($modalidad, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 2 || intval($request['id']) === 3 || intval($request['id']) === 4){
                return response()->json('No se puede modificar la modalidad', 400);
            }
            $modalidad = Modalidade::find($request['id']);
            $modalidad->activo = 1;
            $modalidad->save();

            return response()->json($modalidad, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 2 || intval($request['id']) === 3 || intval($request['id']) === 4){
                return response()->json('No se puede modificar la modalidad', 400);
            }
            $modalidad = Modalidade::find($request['id']);
            $modalidad->activo = 0;
            $modalidad->save();

            return response()->json($modalidad, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 2 || intval($request['id']) === 3 || intval($request['id']) === 4){
                return response()->json('No se puede modificar la modalidad', 400);
            }
            $modalidad = Modalidade::find($request['id']);
            $modalidad->eliminado = 1;
            $modalidad->save();

            return response()->json($modalidad, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            if(intval($request['id']) === 1 || intval($request['id']) === 2 || intval($request['id']) === 3 || intval($request['id']) === 4){
                return response()->json('No se puede modificar la modalidad', 400);
            }
            $modalidad = Modalidade::find($request['id']);
            $modalidad->nombre = $request['nombre'];
            $modalidad->save();

            return response()->json($modalidad, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }
}