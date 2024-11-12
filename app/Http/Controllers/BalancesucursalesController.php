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
            $usuario = $request['idUsuario'];
            $sucursal = $request['idSucursal'];
            $formas = Formaspago::where('eliminado', '=', 0)->get();
            $resultado = array();
            $totalIngresos = 0;
            $totalEgresos = 0;
            
            $ingresos = array();
            $ingreso = array();
            foreach ($formas as $forma) {
                $total = Ingreso::select(DB::raw('SUM(ingresos.monto) as total'))->
                                  where('eliminado', '=', 0)->
                                  where('activo', '=', 1)->
                                  where('idSucursal', '=', $sucursal)->
                                  where('idUsuario', '=', $usuario)->
                                  where('idFormaPago', '=', $forma->id)->
                                  whereDay('created_at', '=', date('d'))->
                                  whereMonth('created_at', '=', date('m'))->
                                  whereYear('created_at', '=', date('Y'))->
                                  get();
                $ingreso['forma'] = $forma->nombre;
                $ingreso['cantidad'] = ($total[0]->total === null) ? number_format(0, 2, '.', ',') : number_format($total[0]->total, 2, '.', ',');
                $ingresos[] = $ingreso;
            }
            $resultado['ingresos'] = $ingresos;

            $egresos = array();
            $egreso = array();
            foreach ($formas as $forma) {
                $total = Egreso::select(DB::raw('SUM(egresos.monto) as total'))->
                                  where('eliminado', '=', 0)->
                                  where('activo', '=', 1)->
                                  where('idUsuario', '=', $usuario)->
                                  where('idSucursal', '=', $sucursal)->
                                  where('idFormaPago', '=', $forma->id)->
                                  whereDay('created_at', '=', date('d'))->
                                  get();
                $egreso['forma'] = $forma->nombre;
                $egreso['cantidad'] = ($total[0]->total === null) ? number_format(0, 2, '.', ',') : number_format($total[0]->total, 2, '.', ',');
                $egresos[] = $egreso;
            }
            $resultado['egresos'] = $egresos;

            $totalEgresos = Egreso::select(DB::raw('SUM(egresos.monto) as total'))->
                                  where('eliminado', '=', 0)->
                                  where('activo', '=', 1)->
                                  where('idSucursal', '=', $sucursal)->
                                  where('idFormaPago', '=', 1)->
                                  get();
            $totalIngresos = Ingreso::select(DB::raw('SUM(ingresos.monto) as total'))->
                                  where('eliminado', '=', 0)->
                                  where('activo', '=', 1)->
                                  where('idSucursal', '=', $sucursal)->
                                  where('idFormaPago', '=', 1)->
                                  get();

            $resultado['total'] = floatval($totalIngresos[0]->total) - floatval($totalEgresos[0]->total);

            $valeAdministrativo = Valeadministrativo::where('idSucursal', '=', $sucursal)->get();
            if(count($valeAdministrativo) > 0){
              $resultado['total'] = floatval($resultado['total']) - floatval($valeAdministrativo[0]->monto);
              $resultado['administrativo'] = number_format($valeAdministrativo[0]->monto, 2, '.', ',');
              $resultado['id'] = $valeAdministrativo[0]->id;
              $resultado['existe'] = true;
            } else {
              $resultado['existe'] = false;
            }

            $resultado['total'] = number_format($resultado['total'], 2, '.', ',');

            return response()->json($resultado, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function nuevoValeAdministrativo(Request $request){
      try {
        $total = str_replace(',', '', $request['total']);
        $total = floatval($total);
        if($total < floatval($request['monto'])){
          return response()->json('No cuentas con suficiente efectivo para realizar este vale', 400);
        }

        $vale = Valeadministrativo::create([
          'idSucursal' => $request['idSucursal'],
          'monto' => $request['monto'],
          'activo' => 1,
          'eliminado' => 0
        ]);

        return response()->json($vale, 200);
      } catch (Exception $e) {
        return response()->json('Error en el servidor', 400);
      }
    }

    function agregarSaldoValeAdministrativo(Request $request){
      try {
        $monto = $request['monto'];
        $total = str_replace(',', '', $request['total']);
        $total = floatval($total);
        if($total < floatval($monto)){
          return response()->json('No cuentas con suficiente efectivo para realizar este vale', 400);
        }
        $vale = Valeadministrativo::find($request['id']);
        $vale->monto = floatval($vale->monto) + floatval($monto);
        $vale->save();

        return response()->json($vale, 200);
      } catch (Exception $e) {
        return response()->json('Error en el servidor', 400);
      }
    }

    function quitarSaldoValeAdministrativo(Request $request){
      try {
        $monto = $request['monto'];
        $vale = Valeadministrativo::find($request['id']);
        if($vale->monto < $monto){
          return response()->json('No cuentas con esa cantidad en tu vale administrativo', 400);
        }
        $vale->monto = floatval($vale->monto) - floatval($monto);
        $vale->save();

        return response()->json($vale, 200);
      } catch (Exception $e) {
        return response()->json('Error en el servidor', 400);
      }
    }
}