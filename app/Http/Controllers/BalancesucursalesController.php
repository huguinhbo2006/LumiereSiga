<?php

namespace App\Http\Controllers;
use App\Ingreso;
use App\Egreso;
use App\Formaspago;
use App\Vale;
use App\Valeadministrativo;
use App\Sucursale;
use App\Clases\Balances;
use App\Clases\Ingresos;
use App\Clases\Egresos;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BalancesucursalesController extends BaseController
{
    function mostrar(Request $request){
        try{
          $funciones = new Balances();

          $respuesta['listas'] = $funciones->listas();
          $respuesta['ingresos'] = $funciones->ingresos($request['id']);
          $respuesta['egresos'] = $funciones->egresos($request['id']);
          $administrativo = floatval($funciones->total($request['id'])) - floatval($funciones->administrativo($request['id']));
          $respuesta['total'] = number_format($administrativo, 2, '.', ',');
          $respuesta['vales'] = number_format($funciones->vales($request['id']), 2, '.', ',');
          $respuesta['administrativo'] = number_format($funciones->administrativo($request['id']));

          return response()->json($respuesta, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function corte(Request $request){
        try{
          $funciones = new Balances();
          $respuesta = array();

          $respuesta['ingresos'] = $funciones->ingresosAdministrativo($request['sucursalID'], $request['usuarioID']);

          $respuesta['egresos'] = $funciones->egresosAdministrativo($request['sucursalID'], $request['usuarioID']);

          $total = floatval($funciones->total($request['id'])) - floatval($funciones->administrativo($request['id']));
          $respuesta['total'] = number_format($total, 2, '.', ',');
          $respuesta['existe'] = $funciones->existeValeAdministrativo($request['sucursalID']);
          $respuesta['vales'] = number_format($funciones->vales($request['id']), 2, '.', ',');
          $respuesta['administrativo'] = number_format($funciones->administrativo($request['id']));

          return response()->json($respuesta, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function nuevoVale(Request $request){
      try {
        $funciones = new Balances();
        $vale = $funciones->crearValeAdministrativo($request['sucursalID']);
        return response()->json($vale, 200);
      } catch (Exception $e) {
        return response()->json('Error en el servidor', 400);
      }
    }

    function saldoVale(Request $request){
      try {
        $funciones = new Balances();
        
        $total = floatval($funciones->total($request['sucursalID'])) - floatval($funciones->administrativo($request['sucursalID']));
        if($total < floatval($request['monto'])){
          return response()->json('No cuentas con suficiente efectivo para realizar este vale', 400);
        }

        $vale = Valeadministrativo::where('idSucursal', '=', $request['sucursalID'])->get()[0];
        $vale->monto = floatval($vale->monto) + floatval($request['monto']);
        $vale->save();

        return response()->json($vale, 200);
      } catch (Exception $e) {
        return response()->json('Error en el servidor', 400);
      }
    }

    function saldoCaja(Request $request){
      try {
        $funciones = new Balances();

        $vale = Valeadministrativo::where('idSucursal', '=', $request['sucursalID'])->get()[0];
        $vale->monto = floatval($vale->monto) - floatval($request['monto']);
        $vale->save();

        return response()->json($vale, 200);
      } catch (Exception $e) {
        return response()->json('Error en el servidor', 400);
      }
    }
}