<?php

namespace App\Http\Controllers;
use App\Universidade;
use App\Centrosuniversitario;
use App\Carrera;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CentrosuniversitariosController extends BaseController
{

    function mostrar(){
        try{
            $centros = Centrosuniversitario::where('eliminado', '=', 0)->get();
            $universidades = Universidade::where('eliminado', '=', 0)->get();
            $respuesta['datos'] = $centros;
            $respuesta['lista'] = $universidades;
            return response()->json($respuesta, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function nuevo(Request $request){
        try {
            $centro = Centrosuniversitario::create([
                'nombre' => $request['nombre'],
                'siglas' => $request['siglas'],
                'idUniversidad' => $request['idUniversidad'],
                'imagen' => $request['imagen'],
                'rgb' => $request['rgb'],
                'eliminado' => 0,
                'activo' => 1
            ]);
            $centro->universidad = Universidade::find($centro->idUniversidad)->nombre;

            return response()->json($centro, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request){
        try{
            $centro = Centrosuniversitario::find($request['id']);
            $centro->nombre = $request['nombre'];
            $centro->siglas = $request['siglas'];
            $centro->idUniversidad = $request['idUniversidad'];
            $centro->imagen = $request['imagen'];
            $centro->rgb = $request['rgb'];
            $centro->save();
            $centro->universidad = Universidade::find($centro->idUniversidad)->nombre;

            return response()->json($centro, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $centro = Centrosuniversitario::find($request['id']);
            $centro->eliminado = 1;
            $centro->save();

            return response()->json($centro, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            $centro = Centrosuniversitario::find($request['id']);
            $centro->activo = 1;
            $centro->save();

            return response()->json($centro, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            $centro = Centrosuniversitario::find($request['id']);
            $centro->activo = 0;
            $centro->save();

            return response()->json($centro, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }
}