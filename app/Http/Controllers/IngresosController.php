<?php

namespace App\Http\Controllers;
use App\Ingreso;
use App\Ingresosolicitude;
use App\Calendario;

//Clases personalizadas
use App\Clases\Folios;
use App\Clases\Imagenes;
use App\Clases\Ingresos;
use Carbon\Carbon;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IngresosController extends BaseController
{
    function nuevo(Request $request){
        try {
            $folios = new Folios();
            $funciones = new Ingresos();
            $folio = $folios->proximoIngreso($request['idNivel'], $request['idCalendario'], $request['sucursalID']);
            
            $request['imagen'] = ($request['idFormaPago'] === 1) ? null : $request['imagen'];
            $request['idBanco'] = ($request['idFormaPago'] === 1) ? null : $request['idBanco'];
            $request['numeroReferencia'] = ($request['idFormaPago'] === 1) ? null : $request['numeroReferencia'];
            $request['nombreCuenta'] = ($request['idFormaPago'] === 1) ? null : $request['nombreCuenta'];

            $ingreso = Ingreso::create([
                'concepto' => $request['concepto'],
                'monto' => $request['monto'],
                'observaciones' => $request['observaciones'],
                'idRubro' => $request['idRubro'],
                'idTipo' => $request['idTipo'],
                'idSucursal' => $request['sucursalID'],
                'idCalendario' => $request['idCalendario'],
                'idFormaPago' => $request['idFormaPago'],
                'idMetodoPago' => $request['idMetodoPago'],
                'idUsuario' => $request['usuarioID'],
                'referencia' => $request['referencia'],
                'idNivel' => $request['idNivel'],
                'folio' => $folio,
                'activo' => 1,
                'eliminado' => 0,
                'imagen' => $request['imagen'],
                'idBanco' => $request['idBanco'],
                'numeroReferencia' => $request['numeroReferencia'],
                'nombreCuenta' => $request['nombreCuenta'],
                'idCuenta' => $request['idCuenta'],
                'fecha' => (isset($request['fecha']) && strlen($request['fecha']) > 0) ? $request['fecha'] : Carbon::now(),
            ]);

            $ingreso = $funciones->completar($ingreso);
            return response()->json($ingreso, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function mostrar(Request $request){
        try {
            $funciones = new Ingresos();

            $registros = $funciones->busquedaGeneral();
            $datos = $registros->whereRaw("DATE_FORMAT(ingresos.created_at,'%y-%m-%d') = CURDATE()")->
                where('ingresos.idUsuario', '=', $request['usuarioID'])->
                where('ingresos.idSucursal', '=', $request['sucursalID'])->get();

            $respuesta['listas'] = $funciones->listas();
            $respuesta['datos'] = $datos;

            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function buscar(Request $request){
        try{
            $funciones = new Ingresos();
            $busqueda = $funciones->busquedaGeneral();

            ($request['idCalendario'] !== 0) ? $busqueda->where('calendarios.id', '=', $request['idCalendario']) : null;
            ($request['idRubro'] !== 0) ? $busqueda->where('rubros.id', '=', $request['idRubro']) : null;
            ($request['idTipo'] !== 0) ? $busqueda->where('tiposingresos.id', '=', $request['idTipo']) : null;
            ($request['idSucursal'] !== 0) ? $busqueda->where('sucursales.id', '=', $request['idSucursal']) : null;
            ($request['idMetodoPago'] !== 0) ? $busqueda->where('metodospagos.id', '=', $request['idMetodoPago']) : null;
            ($request['idFormaPago'] !== 0) ? $busqueda->where('formaspagos.id', '=', $request['idFormaPago']) : null;
            ($request['idNivel'] !== 0) ? $busqueda->where('niveles.id', '=', $request['idNivel']) : null;
            
            $datos = $busqueda->get();
            
            return response()->json($datos, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try {
            $funciones = new Ingresos();

            $ingreso = Ingreso::find($request['id']);
            $ingreso->activo = 0;
            $ingreso->save();

            if(intval($ingreso->referencia) === 2 || intval($ingreso->referencia) === 3){
                $abono = $funciones->traerAbono($request['id']);
                $abono->eliminado = 1;
                $abono->save();
            }

            $ingreso = $funciones->completar($ingreso);

            return response()->json($ingreso, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request){
        try {
            $funciones = new Ingresos();
            $ingreso = $funciones->modificar($request);
            $ingreso = $funciones->completar($ingreso);
            return response()->json($ingreso, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function buscarGerentes(Request $request){
        try{
            $fechaInicio = Carbon::now()->addDay(1)->format('Y-m-d');
            $fechaFin = ($request['dias'] !== null && $request['dias'] !== '') ? Carbon::now()->subDay($request['dias'])->format('Y-m-d') : Carbon::now()->format('Y-m-d');
            $funciones = new Ingresos();
            $busqueda = $funciones->busquedaGeneral();
            $busqueda->whereBetween('ingresos.created_at', [$fechaFin, $fechaInicio])->
                       where('ingresos.idSucursal', '=', $request['sucursalID'])->get();
            return response()->json($ingresos, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function cargar(Request $request) {
        try {
            $ingreso = Ingreso::find($request['id']);
            $ingreso->imagen = $request['imagen'];
            $ingreso->save();
            return response()->json($ingreso, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function voucher(Request $request){
        try {
            $ingreso = Ingreso::find($request['id']);
            return response()->json($ingreso->imagen, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function solicitudes(Request $request){
        try {
            $funciones = new Ingresos();
            return response()->json($funciones->solicitudes(), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function solicitar(Request $request){
        try {
            $existe = Ingresosolicitude::where('idIngreso', '=', $request['id'])->where('estatus', '=', 1)->get();
            if(count($existe) > 0){
                return response()->json('Ya existe una solicitud para modificar este ingreso', 400);
            }
            $solicitud = Ingresosolicitude::create([
                'idUsuarioSolicito' => $request['usuarioID'],
                'idUsuarioAcepto' => 0,
                'idIngreso' => $request['id'],
                'concepto' => $request['concepto'],
                'monto' => $request['monto'],
                'observaciones' => $request['observaciones'],
                'idRubro' => $request['idRubro'],
                'idTipo' => $request['idTipo'],
                'idFormaPago' => $request['idFormaPago'],
                'idMetodoPago' => $request['idMetodoPago'],
                'fecha' => $request['fecha'],
                'idBanco' => $request['idBanco'],
                'nombreCuenta' => $request['nombreCuenta'],
                'numeroReferencia' => $request['numeroReferencia'],
                'idCuenta' => $request['idCuenta'],
                'estatus' => 1,
                'eliminado' => 0,
                'activo' => 1
            ]);
            return response()->json($solicitud, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function aceptar(Request $request){
        try {
            $modificacion = Ingresosolicitude::find($request['id']);
            $modificacion->estatus = 2;
            $modificacion->idUsuarioAcepto = $request['usuarioID'];
            $modificacion->save();

            $ingreso = Ingreso::find($modificacion->idIngreso);
            $ingreso->concepto = $modificacion->concepto;
            $ingreso->monto = $modificacion->monto;
            $ingreso->observaciones = $modificacion->observaciones;
            $ingreso->idRubro = $modificacion->idRubro;
            $ingreso->idTipo = $modificacion->idTipo;
            $ingreso->idFormaPago = $modificacion->idFormaPago;
            $ingreso->idMetodoPago = $modificacion->idMetodoPago;
            $ingreso->fecha = $modificacion->fecha;
            $ingreso->idBanco = $modificacion->idBanco;
            $ingreso->nombreCuenta = $modificacion->nombreCuenta;
            $ingreso->numeroReferencia = $modificacion->numeroReferencia;
            $ingreso->idCuenta = $modificacion->idCuenta;
            $ingreso->save();
            return response()->json($modificacion, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function rechazar(Request $request){
        try {
            $modificacion = Ingresosolicitude::find($request['id']);
            $modificacion->estatus = 3;
            $modificacion->idUsuarioAcepto = $request['usuarioID'];
            $modificacion->save();

            return response()->json($modificacion, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}