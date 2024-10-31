<?php

namespace App\Http\Controllers;
use App\Valesgerenciale;
use App\Calendario;
use App\Sucursale;
use App\Ingreso;
use App\Egreso;
use App\Nivele;
use App\Solicitudesvalesgerenciale;
include "funciones/FuncionesGenerales.php";

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ValesgerencialesController extends BaseController
{
    function nuevo(Request $request){
        try{
            $saldoTotalSucursal = saldoTotalSucursal($request['idSucursal']);
            if($request['monto'] > $saldoTotalSucursal){
                return response()->json("No se cuenta con el saldo suficiente para realizar este vale", 400);
            }

            $folio = proximoFolioEgreso($request['idNivel'], $request['idCalendario'], $request['idSucursal']);

            DB::beginTransaction();

            $egreso = Egreso::create([
                'concepto' => 'Vale Gerencial',
                'monto' => $request['monto'],
                'observaciones' => $request['observaciones'],
                'idRubro' => 2,
                'idTipo' => 2,
                'idSucursal' => $request['idSucursal'],
                'idCalendario' => $request['idCalendario'],
                'idFormaPago' => 1,
                'idUsuario' => $request['idUsuario'],
                'idNivel' => $request['idNivel'],
                'folio' => $folio,
                'referencia' => 4,
                'activo' => 1,
                'eliminado' => 0,
            ]);

            $cantidad = numeroValesGerenciales($request['idCalendario'], $request['idSucursal']);
            $vale = Valesgerenciale::create([
                'idSucursal' => $request['idSucursal'],
                'monto' => $request['monto'],
                'aceptado' => 0,
                'idCalendario' => $request['idCalendario'],
                'observaciones' => $request['observaciones'],
                'idUsuarioCreo' => $request['idUsuario'],
                'idUsuarioRetorno' => 0,
                'idEgreso' => $egreso->id,
                'idIngreso' => 0,
                'folio' => 'V-'.($cantidad+1),
                'idNivel' => $request['idNivel'],
                'estatus' => 1,
                'activo' => 1,
                'eliminado' => 0
            ]);
            $vale->calendario = Calendario::find($vale->idCalendario)->nombre;
            $vale->bg = (intval($vale->estatus) === 1) ? 'bg-verde' : 'bg-rojo';
            
            DB::commit();
            return response()->json($vale, 200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json('Error de servidor', 400);
        }
    }

    function mostrar(Request $request){
        try {
            $vales = Valesgerenciale::leftjoin('calendarios', 'idCalendario', '=', 'calendarios.id')->
                    leftjoin('niveles', 'idNivel', '=', 'niveles.id')->
                    leftJoin('solicitudesvalesgerenciales', 'valesgerenciales.id', '=', 'solicitudesvalesgerenciales.idVale')->
                    select(
                        'valesgerenciales.*',
                        'calendarios.nombre as calendario',
                        'solicitudesvalesgerenciales.id as idSolicitud',
                        DB::raw("(CASE 
                            WHEN(valesgerenciales.estatus = 1) THEN 'bg-verde'
                            WHEN(valesgerenciales.estatus = 2) THEN 'bg-rojo'
                            WHEN(valesgerenciales.estatus = 3) THEN 'bg-amarillo'
                            END) AS bg")
                        )->
                    where('valesgerenciales.idSucursal', '=', $request['idSucursal'])->
                    where('valesgerenciales.eliminado', '=', 0)->get();
            $consulta = "SELECT * FROM calendarios WHERE fin > NOW() AND eliminado = 0 AND activo = 1";
            $calendarios = DB::select($consulta, array());
            $niveles = Nivele::where('eliminado', '=', 0)->get();

            $respuesta['vales'] = $vales;
            $respuesta['listas']['niveles'] = $niveles;
            $respuesta['listas']['calendarios'] = $calendarios;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function buscar(Request $request){
        try {
            $vales = Vale::leftjoin('calendarios', 'idCalendario', '=', 'calendarios.id')->
                    select(
                        'vales.id',
                        'vales.monto',
                        'calendarios.nombre as calendario',
                        'vales.idSucursalEntrada',
                        'vales.idSucursalSalida',
                        'vales.idCalendario',
                        'vales.idEgreso',
                        'vales.observaciones',
                        'vales.folio',
                        DB::raw("(CASE 
                            WHEN(vales.aceptado = 0) THEN 'bg-amarillo'
                            WHEN(vales.aceptado = 1) THEN 'bg-verde'
                            WHEN(vales.aceptado = 2) THEN 'bg-rojo'
                            END) AS bg")
                        )->
                    where('vales.idSucursalSalida', '=', $request['idSucursal'])->
                    where('vales.eliminado', '=', 0)->
                    where('vales.idUsuarioCreo', '=', $request['idUsuario'])->
                    where('vales.idCalendario', '=', $request['idCalendario'])->get();
            return response()->json($vales, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function aceptarModificacion(Request $request){
        try{
            $solicitud = Solicitudesvalesgerenciale::find($request['idSolicitud']);
            $saldoTotalSucursal = saldoTotalSucursal($request['idSucursal']);
            if($solicitud->monto > $saldoTotalSucursal){
                return response()->json("No se cuenta con el saldo suficiente para realizar este vale", 400);
            }
            DB::beginTransaction();

            $vale = Valesgerenciale::find($request['id']);
            $vale->monto = $solicitud->monto;
            $vale->observaciones = $solicitud->observaciones;
            $vale->estatus = 1;
            $vale->save();

            $vale->calendario = Calendario::find($vale->idCalendario)->nombre;
            $vale->bg = 'bg-verde';

            $egreso = Egreso::find($vale->idEgreso);
            $egreso->monto = $vale->monto;
            $egreso->save();

            $solicitud->delete();
            DB::commit();
            return response()->json($vale, 200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json('Error de servidor', 400);
        }
    }

    function rechazarModificacion(Request $request){
        try {
            DB::beginTransaction();
            $vale = Valesgerenciale::find($request['id']);
            $vale->estatus = 1;
            $vale->save();
            $vale->calendario = Calendario::find($vale->idCalendario)->nombre;
            $vale->bg = 'bg-verde';

            $solicitud = Solicitudesvalesgerenciale::find($request['idSolicitud']);
            $solicitud->delete();
            DB::commit();
            return response()->json($vale, 200);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request){
        try {
            DB::beginTransaction();
            $solicitud = Solicitudesvalesgerenciale::create([
                'idVale' => $request['id'],
                'monto' => $request['monto'],
                'observaciones' => $request['observaciones'],
                'activo' => 1,
                'eliminado' => 0
            ]);

            $vale = Valesgerenciale::find($request['id']);
            $vale->estatus = 3;
            $vale->save();
            $vale->calendario = Calendario::find($vale->idCalendario)->nombre;
            $vale->bg = 'bg-amarillo';
            DB::commit();
            return response()->json($vale, 200);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request){
        try{
            $vale = Vale::find($request['id']);
            $vale->aceptado = 2;
            $vale->save();

            return response()->json($vale, 200);
        }catch(Exception $e){
            return response()->json('Error de servidor', 400);
        }
    }

    function aceptar(Request $request){
        try{
            DB::beginTransaction();
            $vale = Valesgerenciale::find($request['id']);

            $folio = proximoFolioIngreso($request['idNivel'], $request['idCalendario'], $request['idSucursal']);
            $ingreso = Ingreso::create([
                'concepto' => 'Vale Gerencial de Sucursal '.$request['sucursal'],
                'monto' => $request['monto'],
                'observaciones' => $request['observaciones'],
                'idRubro' => 2,
                'idTipo' => 3,
                'idSucursal' => $request['idSucursal'],
                'idCalendario' => $request['idCalendario'],
                'idFormaPago' => 1,
                'idMetodoPago' => 1,
                'idUsuario' => $request['usuario'],
                'idNivel' => $vale->idNivel,
                'folio' => $folio,
                'referencia' => 4,
                'activo' => 1,
                'eliminado' => 0,
            ]);

            
            
            $vale->idUsuarioRetorno = $request['usuario'];
            $vale->idIngreso = $ingreso->id;
            $vale->estatus = 2;
            $vale->save();
            $vale->calendario = Calendario::find($vale->idCalendario)->nombre;
            $vale->bg = (intval($vale->estatus) === 1) ? 'bg-verde' : 'bg-rojo';
            DB::commit();
            return response()->json($vale, 200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json('Error de servidor', 400);
        }
    }
}