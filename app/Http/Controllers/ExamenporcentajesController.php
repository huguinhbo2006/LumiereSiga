<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use App\Examenporcentaje;
use App\Seccionesporcentaje;
use App\Seccione;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
include "funciones/FuncionesGenerales.php";

class ExamenporcentajesController extends BaseController
{
    function nuevo(Request $request){
        try {
            $porcentajes = Examenporcentaje::where('idExamen', '=', $request['idExamen'])->where('eliminado', '=', 0)->get();
            $porcentaje = intval($request['porcentaje']);
            foreach ($porcentajes as $registro) {
                $porcentaje = $porcentaje + $registro->porcentaje;
            }
            if(intval($porcentaje) > 100){
                return response()->json('No se puede exceder el 100%', 400);
            }
            $nuevo = Examenporcentaje::create([
                'idExamen' => $request['idExamen'],
                'porcentaje' => $request['porcentaje']['porcentaje'],
                'nombre' => $request['porcentaje']['nombre'],
                'eliminado' => 0,
                'activo' => 1
            ]);
            return response()->json($nuevo, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function mostrar(Request $request){
        try {
            $porcentajes = Examenporcentaje::where('idExamen', '=', $request['idExamen'])->
                                             where('eliminado', '=', 0)->
                                             select('id', 'nombre')->get();
            return response()->json($porcentajes, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function traerSecciones(Request $request){
        try {
            $secciones = Seccione::where('idExamen', '=', $request['idExamen'])->where('valido', '=', 1)->get();
            $pendientes = array();
            $agregadas = array();
            foreach ($secciones as $seccion) {
                $existe = Seccionesporcentaje::where('idSeccion', '=', $seccion->id)->
                                               where('idPorcentaje', '=', $request['idPorcentaje'])->
                                               where('eliminado', '=', 0)->get();
                if(count($existe) > 0){
                    $seccion->id = $existe[0]->id;
                    $agregadas[] = $seccion;
                }else{
                    $pendientes[] = $seccion;
                }
            }
            $respuesta['agregadas'] = $agregadas;
            $respuesta['pendientes'] = $pendientes;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function agregarSeccion(Request $request){
        try {
            $seccion = Seccionesporcentaje::create([
                'idSeccion' => $request['idSeccion'],
                'idPorcentaje' => $request['idPorcentaje'],
                'eliminado' => 0,
                'activo' => 1
            ]);
            return response()->json($seccion, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminarSeccion(Request $request){
        try {
            $seccion = Seccionesporcentaje::find($request['id']);
            $seccion->eliminado = 1;
            $seccion->save();
            return response()->json('Seccion eliminada', 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminarPorcentaje(Request $request){
        try {
            $porcentaje = Examenporcentaje::find($request['idPorcentaje']);
            $porcentaje->eliminado = 1;
            $porcentaje->save();
            return response()->json($porcentaje, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}