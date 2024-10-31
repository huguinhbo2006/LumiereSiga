<?php

namespace App\Http\Controllers;
use App\Vale;
use App\Calendario;
use App\Sucursale;
use App\Ingreso;
use App\Egreso;
use App\Nivele;
use App\Clases\Vales;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ValesController extends BaseController
{
    function nuevo(Request $request){
        try{
            $saldoTotalSucursal = saldoTotalSucursal($request['idSucursalSalida']);
            if($request['monto'] > $saldoTotalSucursal){
                return response()->json("No se cuenta con el saldo suficiente para realizar este vale", 400);
            }

            $folio = proximoFolioEgreso($request['idNivel'], $request['idCalendario'], $request['idSucursalSalida']);

            DB::beginTransaction();

            $egreso = Egreso::create([
                'concepto' => 'Vale',
                'monto' => $request['monto'],
                'observaciones' => $request['observaciones'],
                'idRubro' => 2,
                'idTipo' => 2,
                'idSucursal' => $request['idSucursalSalida'],
                'idCalendario' => $request['idCalendario'],
                'idFormaPago' => 1,
                'idUsuario' => $request['idUsuarioCreo'],
                'idNivel' => $request['idNivel'],
                'folio' => $folio,
                'referencia' => 4,
                'activo' => 1,
                'eliminado' => 0,
            ]);

            $cantidad = numeroVales($request['idCalendario'], $request['idSucursalSalida']);
            $vale = Vale::create([
                'idSucursalSalida' => $request['idSucursalSalida'],
                'idSucursalEntrada' => $request['idSucursalEntrada'],
                'monto' => $request['monto'],
                'aceptado' => 0,
                'idCalendario' => $request['idCalendario'],
                'observaciones' => $request['observaciones'],
                'idUsuarioCreo' => $request['idUsuarioCreo'],
                'idEgreso' => $egreso->id,
                'folio' => 'V-'.($cantidad+1),
                'idNivel' => $request['idNivel'],
                'activo' => 1,
                'eliminado' => 0
            ]);
            $vale->calendario = Calendario::find($vale->idCalendario)->nombre;
            

            DB::commit();
            return response()->json($vale, 200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json('Error de servidor', 400);
        }
    }

    function mostrarCreados(Request $request){
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
            $calendarios = Calendario::where('eliminado', '=', 0)->get();
            $niveles = Nivele::where('eliminado', '=', 0)->get();

            $respuesta['vales'] = $vales;
            $respuesta['niveles'] = $niveles;
            $respuesta['calendarios'] = $calendarios;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function recibidos(){
        try {
            $funciones = new Vales();
            return response()->json($funciones->recibidos(), 200);
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

    function modificar(Request $request){
        try{
            $vale = Vale::find($request['id']);
            $vale->monto = $request['monto'];
            $vale->observaciones = $request['observaciones'];
            $vale->idSucursalEntrada = $request['idSucursalEntrada'];
            $vale->idCalendario = $request['idCalendario'];
            $vale->save();

            $egreso = Egreso::find($vale->idEgreso);
            $egreso->monto = $vale->monto;
            $egreso->save();

            return response()->json($request, 200);
        }catch(Exception $e){
            return response()->json('Error de servidor', 400);
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
            $funciones = new Vales();
            $ingreso = $funciones->crearIngreso($request);
            $vale = Vale::find($request['id']);   
            $vale->idUsuarioAcepto = $request['usuarioID'];
            $vale->idIngreso = $ingreso->id;
            $vale->aceptado = 1;
            $vale->save();
            return response()->json($vale, 200);
        }catch(Exception $e){
            return response()->json('Error de servidor', 400);
        }
    }
}