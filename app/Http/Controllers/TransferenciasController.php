<?php

namespace App\Http\Controllers;
use App\Transferencia;
use App\Calendario;
use App\Sucursale;
use App\Ingreso;
use App\Egreso;
use App\Nivele;
use Carbon\Carbon;
include "funciones/FuncionesGenerales.php";

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferenciasController extends BaseController
{
    function nuevo(Request $request){
        try{
            $saldoTotalSucursal = saldoTotalSucursal($request['sucursalID']);
            if($request['monto'] > $saldoTotalSucursal){
                return response()->json("No se cuenta con el saldo suficiente para realizar este vale", 400);
            }

            $cantidad = Transferencia::where('idCalendario', '=', $request['idCalendario'])->where('idSucursalSalida', '=', $request['idSucursalSalida'])->get();
            $transferencia = Transferencia::create([
                'idSucursalSalida' => $request['sucursalID'],
                'idSucursalEntrada' => $request['idSucursalEntrada'],
                'monto' => $request['monto'],
                'aceptado' => 0,
                'idCalendario' => $request['idCalendario'],
                'idUsuarioCreo' => $request['usuarioID'],
                'idNivel' => $request['idNivel'],
                'activo' => 1,
                'eliminado' => 0
            ]);

            $transferencia->sucursal = Sucursale::find($transferencia->idSucursalEntrada)->nombre;
            $transferencia->calendario = Calendario::find($transferencia->idCalendario)->nombre;
            $transferencia->montoFormato = "$".number_format($transferencia->monto, 2, '.', ',');
            $transferencia->fechaFormato = Carbon::parse($transferencia->created_at)->format('d-m-Y h:i:s');

            return response()->json($transferencia, 200);
        }catch(Exception $e){
            return response()->json('Error de servidor', 400);
        }
    }

    function creados(Request $request){
        try {
            $transferencias = Transferencia::join('calendarios', 'idCalendario', '=', 'calendarios.id')->
            join('sucursales', 'idSucursalEntrada', '=', 'sucursales.id')->
            select(
                'transferencias.id',
                'transferencias.idNivel',
                'transferencias.idCalendario',
                'transferencias.idSucursalEntrada',
                'transferencias.monto',
                'calendarios.nombre as calendario',
                DB::raw("CONCAT('$',FORMAT(transferencias.monto,2)) AS montoFormato"),
                'sucursales.nombre as sucursal',
                DB::raw("DATE_FORMAT(transferencias.created_at, '%d-%m-%Y %H:%i:%s') as fechaFormato"),
                db::raw("(CASE 
                            WHEN transferencias.aceptado = 1 THEN 'bg-verde'
                            WHEN transferencias.aceptado = 2 THEN 'bg-verde'
                        END) AS bg")
            )->
            where('transferencias.idSucursalSalida', '=', $request['sucursalID'])->
            where('transferencias.eliminado', '=', 0)->
            where('transferencias.idCalendario', '=', $request['calendarioID'])->get();
            $listas['niveles'] = Nivele::where('eliminado', '=', 0)->get();
            $listas['calendarios'] = Calendario::where('eliminado', '=', 0)->get();
            $listas['sucursales'] = Sucursale::where('eliminado', '=', 0)->get();

            $respuesta['transferencias'] = $transferencias;
            $respuesta['listas'] = $listas;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function recibidos(Request $request){
        try {
            $transferencias = Transferencia::join('calendarios', 'idCalendario', '=', 'calendarios.id')->
            join('niveles', 'idNivel', '=', 'niveles.id')->
            where('idSucursalEntrada', '=', $request['idSucursal'])->
            where('transferencias.eliminado', '=', 0)->
            where('transferencias.aceptado', '=', 0)->
            select('transferencias.*', 'calendarios.nombre as calendario', 'niveles.nombre as nivel')->get();
            return response()->json($transferencias, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request){
        try{
            $transferencia = Transferencia::find($request['id']);
            $transferencia->monto = $request['monto'];
            $transferencia->idSucursalEntrada = $request['idSucursalEntrada'];
            $transferencia->idCalendario = $request['idCalendario'];
            $transferencia->idNivel = $request['idNivel'];
            $transferencia->save();

            return response()->json($transferencia, 200);
        }catch(Exception $e){
            return response()->json('Error de servidor', 400);
        }
    }

    function eliminar(Request $request){
        try{
            $transferencia = Transferencia::find($request['id']);
            $transferencia->delete();

            return response()->json($transferencia, 200);
        }catch(Exception $e){
            return response()->json('Error de servidor', 400);
        }
    }

    function aceptar(Request $request){
        try{
            $sucursalEntrada = Sucursale::find($request['idSucursalEntrada']);
            $sucursalSalida = Sucursale::find($request['idSucursalSalida']);
            $usuarioCreo = Sucursale::find($request['idUsuarioCreo']);
            $usuarioAcepto = $request['idUsuarioAcepto'];
            $transferencia = Transferencia::find($request['id']);

            $folioIngreso = proximoFolioIngreso($transferencia->idNivel, $request['idCalendario'], $request['idSucursalEntrada']);
            $folioEgreso = proximoFolioEgreso($transferencia->idNivel, $request['idCalendario'], $request['idSucursalSalida']);

            $ingreso = Ingreso::create([
                'concepto' => 'Transferencia de Corporativo',
                'monto' => $request['monto'],
                'observaciones' => 'Transferencia de efectivo',
                'idRubro' => 2,
                'idTipo' => 4,
                'idSucursal' => $request['idSucursalEntrada'],
                'idCalendario' => $request['idCalendario'],
                'idFormaPago' => 1,
                'idMetodoPago' => 1,
                'idUsuario' => $request['idUsuarioAcepto'],
                'idNivel' => $transferencia->idNivel,
                'folio' => $folioIngreso,
                'referencia' => 5,
                'activo' => 1,
                'eliminado' => 0,
            ]);

            $egreso = Egreso::create([
                'concepto' => 'Transferencia a Sucursal',
                'monto' => $request['monto'],
                'observaciones' => 'Transferencia',
                'idRubro' => 2,
                'idTipo' => 3,
                'idSucursal' => $request['idSucursalSalida'],
                'idCalendario' => $request['idCalendario'],
                'idFormaPago' => 1,
                'idUsuario' => $request['idUsuarioCreo'],
                'idNivel' => $request['idNivel'],
                'folio' => $folioEgreso,
                'referencia' => 5,
                'activo' => 1,
                'eliminado' => 0,
            ]);

            
            $transferencia->idUsuarioAcepto = $request['idUsuarioAcepto'];
            $transferencia->idIngreso = $ingreso->id;
            $transferencia->aceptado = 1;
            $transferencia->idEgreso = $egreso->id;
            $transferencia->save();

            return response()->json($transferencia, 200);
        }catch(Exception $e){
            return response()->json('Error de servidor', 400);
        }
    }

    function rechazar(Request $request){
        try{
            $sucursalEntrada = Sucursale::find($request['idSucursalEntrada']);
            $transferencia = Transferencia::find($request['id']);
            $transferencia->aceptado = 2;
            $transferencia->save();

            return response()->json($transferencia, 200);
        }catch(Exception $e){
            return response()->json('Error de servidor', 400);
        }
    }
}