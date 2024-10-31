<?php

namespace App\Http\Controllers;
use App\Calendario;
use App\Sucursale;
use App\Metasingreso;
use App\Ficha;
use App\Clases\Fichas;
use App\Clases\Metasingresos;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MetasingresosController extends BaseController
{
    function mostrar(Request $request){
        try {
            $metas = Metasingreso::join('sucursales', 'idSucursal', '=', 'sucursales.id')->
            join('calendarios', 'idCalendario', '=', 'calendarios.id')->
            select([
                'metasingresos.*',
                DB::raw('ELT(metasingresos.idMes, "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre") as mes'),
                'sucursales.nombre as sucursal',
                'calendarios.nombre as calendario',
            ])->where('idCalendario', '=', $request['calendarioID'])->get();
            $respuesta['listas']['metas'] = $metas;

            $consulta = "SELECT * FROM calendarios WHERE fin > NOW() AND eliminado = 0 AND activo = 1";
            $calendarios = DB::select($consulta, array());
            $respuesta['listas']['calendarios'] = $calendarios;
            $respuesta['listas']['sucursales'] = Sucursale::where('eliminado', '=', 0)->get();
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function nuevo(Request $request){
        try {
            $existe = Metasingreso::where('idCalendario', '=', $request['idCalendario'])->
            where('idSucursal', '=', $request['idSucursal'])->
            where('idMes', '=', $request['idMes'])->get();
            if(count($existe) > 0){
                $meta = $existe[0];
                $meta->meta = $request['meta'];
                $meta->save();

                $meta->calendario = Calendario::find($meta->idCalendario);
                $meta->sucursal = Sucursale::find($meta->idSucursal);
                $meta->mes = mesEntero($meta->idMes);
                return response()->json($meta, 200);
            }else{
                $meta = Metasingreso::create([
                    'idSucursal' => $request['idSucursal'],
                    'idCalendario' => $request['idCalendario'],
                    'idMes' => $request['idMes'],
                    'meta' => $request['meta'],
                    'activo' => 1,
                    'eliminado' => 0
                ]);

                $meta->calendario = Calendario::find($meta->idCalendario);
                $meta->sucursal = Sucursale::find($meta->idSucursal);
                $meta->mes = mesEntero($meta->idMes);

                return response()->json($meta, 200);
            }   
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request){
        try {
            $meta = Metasingreso::find($request['id']);
            $meta->meta = $request['meta'];
            $meta->save();
            $meta->calendario = Calendario::find($meta->idCalendario);
            $meta->sucursal = Sucursale::find($meta->idSucursal);
            $meta->mes = mesEntero($meta->idMes);

            return response()->json($request, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request){
        try {
            $meta = Metasingreso::find($request['id']);
            $meta->delete();

            $meta->calendario = Calendario::find($meta->idCalendario)->nombre;
            $meta->sucursal = Sucursale::find($meta->idSucursal)->nombre;
            $mes = mesEntero($meta->idMes);

            return response()->json($meta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function metas(Request $request){
        try {
            $funciones = new Metasingresos();
            if(intval($request['sucursalID']) === 1){
                $metas = $funciones->metasCalendario($request['calendarioID']);
                $respuesta = $funciones->ventas($metas);
                return response()->json($respuesta, 200);
            }else{
                return response()->json('No se pueden mostrar las estadisticas en esta sucursal', 400);
            }
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}