<?php

namespace App\Http\Controllers;
use App\Metasme;
use App\Calendario;
use App\Sucursale;
use App\Nivele;
use App\Subnivele;
use App\Categoria;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
include "funciones/FuncionesGenerales.php";

class MetasmesController extends BaseController
{
    function nuevo(Request $request){
        try {
            $existe = Metasme::where('idCalendario', '=', $request['idCalendario'])->
            where('idSucursal', '=', $request['idSucursal'])->
            where('idNivel', '=', $request['idNivel'])->
            where('idSubnivel', '=', $request['idSubnivel'])->
            where('mes', '=', $request['mes'])->get();
            if(count($existe) > 0){
                $meta = $existe[0];
                $meta->calendario = Calendario::find($meta->idCalendario)->nombre;
                $meta->sucursal = Sucursale::find($meta->idSucursal)->nombre;
                $mes = mesEntero($meta->mes);

                $mensaje = "Ya existe una meta en el mes de ".$mes." para la sucursal ".$meta->sucursal." en el calendario ".$meta->calendario;
                return response()->json($mensaje, 400);
            }else{
                $meta = Metasme::create([
                    'idCalendario' => $request['idCalendario'],
                    'idSucursal' => $request['idSucursal'],
                    'idNivel' => $request['idNivel'],
                    'idSubnivel' => $request['idSubnivel'],
                    'mes' => $request['mes'],
                    'meta' => $request['meta'],
                    'eliminado' => 0,
                    'activo' => 1
                ]);

                $meta->calendario = Calendario::find($meta->idCalendario)->nombre;
                $meta->sucursal = Sucursale::find($meta->idSucursal)->nombre;
                $meta->nivel = Nivele::find($meta->idNivel)->nombre;
                $meta->subnivel = Subnivele::find($meta->idSubnivel)->nombre;
                $mes = mesEntero($meta->mes);

                return response()->json($meta, 200);
            }
        } catch (Exception $e) {
            
        }
    }

    function modificar(Request $request){
        try {
            $meta = Metasme::find($request['id']);
            $meta->meta = $request['meta'];
            $meta->save();
            $meta->calendario = Calendario::find($meta->idCalendario)->nombre;
            $meta->sucursal = Sucursale::find($meta->idSucursal)->nombre;
            $mes = mesEntero($meta->mes);

            return response()->json($meta, 200);
        } catch (Exception $e) {
            
        }
    }

    function mostrar(Request $request){
        try {
            $metas = Metasme::join('sucursales', 'idSucursal', '=', 'sucursales.id')->
            join('calendarios', 'idCalendario', '=', 'calendarios.id')->
            join('niveles', 'idNivel', '=', 'niveles.id')->
            join('subniveles', 'idSubnivel', '=', 'subniveles.id')->
            select(
                'metasmes.*', 
                'calendarios.nombre as calendario',
                'sucursales.nombre as sucursal',
                'niveles.nombre as nivel',
                'subniveles.nombre as subnivel'
            )->
            where('metasmes.eliminado', '=', 0)->get();
            $consulta = "SELECT * FROM calendarios WHERE fin > NOW() AND eliminado = 0 AND activo = 1";
            $calendarios = DB::select($consulta, array());
            $sucursales = Sucursale::where('eliminado', '=', 0)->get();
            $niveles = Nivele::where('eliminado', '=', 0)->get();
            $subniveles = Subnivele::where('eliminado', '=', 0)->get();

            $respuesta['listas']['calendarios'] = $calendarios;
            $respuesta['listas']['sucursales'] = $sucursales;
            $respuesta['listas']['metas'] = $metas;
            $respuesta['listas']['niveles'] = $niveles;
            $respuesta['listas']['subniveles'] = $subniveles;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request){
        try {
            $meta = Metasme::find($request['id']);
            $meta->delete();

            $meta->calendario = Calendario::find($meta->idCalendario)->nombre;
            $meta->sucursal = Sucursale::find($meta->idSucursal)->nombre;
            $mes = mesEntero($meta->mes);

            return response()->json($meta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}