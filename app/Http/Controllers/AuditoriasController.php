<?php

namespace App\Http\Controllers;
use App\Calendario;
use App\Ingreso;
use App\Banco;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditoriasController extends BaseController
{
    function listas(){
        try {
            $respuesta['listas']['calendarios'] = Calendario::where('eliminado', '=', 0)->get();
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function ingresos(Request $request){
        try {
            $ingresos = Ingreso::leftjoin('alumnoabonos', 'ingresos.id', '=', 'alumnoabonos.idIngreso')->
            leftjoin('fichas', 'alumnoabonos.idFicha', '=', 'fichas.id')->
            join('formaspagos', 'ingresos.idFormaPago', '=', 'formaspagos.id')->
            select(
                'ingresos.id as id',
                'ingresos.folio as folio',
                'ingresos.fecha as fecha',
                'ingresos.created_at as fecha1',
                'formaspagos.nombre as forma',
                DB::raw("IF(ingresos.idFormaPago <> 1, IF(LENGTH(ingresos.imagen) > 0, 'fas fa-check text-success', 'fas fa-times text-danger'), 'N/A') as voucher"),
                'ingresos.monto',
                'fichas.folio as ficha',
                DB::raw("IF(ingresos.auditado = 0, '', 'bg-verde') as bg"),
                DB::raw("IF(ingresos.activo = 0, 'fas fa-times text-danger', 'fas fa-check text-success') as activo"),
                'ingresos.auditado as auditado',
                'ingresos.idBanco',
                'ingresos.idFormaPago'
            )->where('ingresos.idCalendario', '=', $request['idCalendario'])->get();
            foreach ($ingresos as $ingreso) {
                if(intval($ingreso->idFormaPago) !== 1){
                    $ingreso->banco = Banco::find($ingreso->idBanco)->nombre;    
                }else{
                    $ingreso->banco = 'N/A';
                }
                if(is_null($ingreso->ficha)){
                    $ingreso->ficha = "-";
                }
                if(is_null($ingreso->fecha)){
                    $ingreso->fecha = $ingreso->fecha1;
                }
            }
            return response()->json($ingresos, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function auditarIngreso(Request $request){
        try {
            $ingreso = Ingreso::find($request['id']);
            $ingreso->auditado = true;
            $ingreso->save();
            return response()->json($ingreso, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function desauditarIngreso(Request $request){
        try {
            $ingreso = Ingreso::find($request['id']);
            $ingreso->auditado = false;
            $ingreso->save();
            return response()->json($ingreso, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function voucherIngreso(Request $request){
        try {
            $ingreso = Ingreso::find($request['id']);
            return response()->json($ingreso->imagen, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}