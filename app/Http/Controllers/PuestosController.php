<?php

namespace App\Http\Controllers;
use App\Departamento;
use App\Puesto;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class PuestosController extends BaseController
{
    function nuevo(Request $request){
        try{
            $puesto = Puesto::create([
                'nombre' => $request['nombre'],
                'idDepartamento' => $request['idDepartamento'],
                'activo' => 1,
                'eliminado' => 0
            ]);

            return response()->json($puesto, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function mostrar(Request $request){
        try{
            $respuesta['datos'] = Puesto::where('eliminado', '=', 0)->get();
            $respuesta['lista'] = Departamento::where('eliminado', '=', 0)->get();
            return response()->json($respuesta, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            $puesto = Puesto::find($request['id']);
            $puesto->activo = 1;
            $puesto->save();

            return response()->json($puesto, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            $puesto = Puesto::find($request['id']);
            $puesto->activo = 0;
            $puesto->save();

            return response()->json($puesto, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $puesto = Puesto::find($request['id']);
            $puesto->eliminado = 1;
            $puesto->save();

            return response()->json($puesto, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            $puesto = Puesto::find($request['id']);
            $puesto->nombre = $request['nombre'];
            $puesto->idDepartamento = $request['idDepartamento'];
            $puesto->save();

            return response()->json($puesto, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }
}