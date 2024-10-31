<?php

namespace App\Http\Controllers;
use App\Carrera;
use App\Centrosuniversitario;
use App\Universidade;
use App\Calendario;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CarrerasController extends BaseController
{

    function nuevo(Request $request){
        try{
            $carrera = Carrera::create([
                'nombre' => $request['nombre'],
                'aspirantes' => $request['aspirantes'],
                'admitidos' => $request['admitidos'],
                'rechazados' => $request['rechazados'],
                'puntaje' => $request['puntaje'],
                'idCentroUniversitario' => $request['idCentroUniversitario'],
                'idUniversidad' => $request['idUniversidad'],
                'idCalendario' => $request['idCalendario'],
                'eliminado' => 0,
                'activo' => 1
            ]);

            return response()->json($carrera, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            $carrera = Carrera::find($request['id']);
            $carrera->nombre = $request['nombre'];
            $carrera->aspirantes = $request['aspirantes'];
            $carrera->admitidos = $request['admitidos'];
            $carrera->rechazados = $request['rechazados'];
            $carrera->puntaje = $request['puntaje'];
            $carrera->idCentroUniversitario = $request['idCentroUniversitario'];
            $carrera->idUniversidad = $request['idUniversidad'];
            $carrera->idCalendario = $request['idCalendario'];
            $carrera->save();

            return response()->json($carrera, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $carrera = Carrera::find($request['id']);
            $carrera->eliminado = 1;
            $carrera->save();

            return response()->json($carrera, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            $carrera = Carrera::find($request['id']);
            $carrera->activo = 0;
            $carrera->save();

            return response()->json($carrera, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            $carrera = Carrera::find($request['id']);
            $carrera->activo = 1;
            $carrera->save();

            return response()->json($carrera, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function cargar(Request $request){
        try {
            //return response()->json($request['archivo'], 400);
            DB::beginTransaction();
            $decodificacion = json_decode($request['archivo']);
            $encontrados = array();

            while ($dato = current($decodificacion)) {
                $finales = array();
                $centro = Centrosuniversitario::where('siglas', '=', trim(key($decodificacion)))->get();
                if(count($centro) > 0){
                    $idCentro = $centro[0]->id;
                    $idUniversidad = $centro[0]->idUniversidad;
                    $idCalendario = $request['idCalendario'];
                    foreach ($dato as $carrera) {
                        $registro = Carrera::create([
                            'nombre' => $carrera->carrera,
                            'aspirantes' => $carrera->aspirantes,
                            'admitidos' => $carrera->admitidos,
                            'rechazados' => $carrera->rechazados,
                            'puntaje' => $carrera->puntaje,
                            'idCentroUniversitario' => $idCentro,
                            'idUniversidad' => $idUniversidad,
                            'idCalendario' => $idCalendario,
                            'eliminado' => 0,
                            'activo' => 1
                        ]);
                    }
                    $finales['centro'] = key($decodificacion);
                    $finales['completo'] = true;
                    $encontrados[] = $finales;
                }else{
                    $finales['centro'] = key($decodificacion);
                    $finales['completo'] = false;
                    $encontrados[] = $finales;
                }
                next($decodificacion);
            }
            DB::commit();

            return response()->json($encontrados, 200);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json('Error en el servidor', 400);
        }
    }

    function mostrar(Request $request){
        try {
            $respuesta['datos'] = Carrera::where('eliminado', '=', 0)->get();
            $respuesta['listas']['centrosUniversitarios'] = Centrosuniversitario::where('eliminado', '=', 0)->get();
            $respuesta['listas']['universidades'] = Universidade::where('eliminado', '=', 0)->get();
            $respuesta['listas']['calendarios'] = Calendario::where('eliminado', '=', 0)->get();
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}