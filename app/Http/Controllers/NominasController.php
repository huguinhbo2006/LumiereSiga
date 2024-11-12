<?php

namespace App\Http\Controllers;
use App\Clases\Nominas;
use App\Clases\Sucursales;
use App\Clases\Folios;
use App\Clases\Consultas;
use App\Nomina;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NominasController extends BaseController
{

    function nuevo(Request $request){
        try {
            $personales = $request['personales'];
            $deducciones = $request['deducciones'];
            $percepciones = $request['percepciones'];

            $cantidad = Nomina::where('idCalendario', '=', $personales['idCalendario'])->
                                where('idNivel', '=', $personales['idNivel'])->
                                where('idDepartamento', '=', $personales['idDepartamento'])->
                                where('idSucursal', '=', $request['sucursalNomina'])->get();
            $departamento = Departamento::find($personales['idDepartamento']);
            $calendario = Calendario::find($personales['idCalendario']);
            $nivel = Nivele::find($personales['idNivel']);

            $separado[] = explode('-', $calendario->nombre);
            $year = $separado[0][0];
            $letra = $separado[0][1]; 
            $folio = substr($year, -2).$letra.substr($nivel->nombre, 0, 1).'-'.$departamento->abreviatura.(count($cantidad)+1);

            $empleado = Empleado::find($personales['idEmpleado']);
            $nomina = Nomina::create([
                'idEmpleado' => $personales['idEmpleado'],
                'idCalendario' => $personales['idCalendario'],
                'idNivel' => $personales['idNivel'],
                'idSucursal' => $request['sucursalNomina'],
                'idPuesto' => $personales['idPuesto'],
                'idDepartamento' => $personales['idDepartamento'],
                'quincena' => $personales['quincena'],
                'fechaInicio' => $personales['fechaInicio'],
                'fechaFin' => $personales['fechaFin'],
                'fechaExpedicion' => $personales['fechaExpedicion'],
                'observaciones' => $personales['observaciones'],
                'estatus' => 0,
                'eliminado' => 0,
                'activo' => 1,
                'folio' => $folio,
                'idBanco' => $personales['idBanco']
            ]);

            $totalDeduccionesEfectivo = 0;
            $totalDeduccionesDeposito = 0;
            foreach ($deducciones as $dato) {
                $deduccion = Deduccione::create([
                    'idNomina' => $nomina->id,
                    'idFormaPago' => $dato['idFormaPago'],
                    'monto' => $dato['monto'],
                    'idConcepto' => $dato['idConcepto'],
                    'cantidad' => $dato['cantidad'],
                    'valorUnitario' => $dato['valorUnitario'],
                    'eliminado' => 0,
                    'activo' => 1
                ]);
                if($deduccion->idFormaPago === 1){
                    $totalDeduccionesEfectivo = $totalDeduccionesEfectivo + floatval($deduccion->monto);
                }else{
                    $totalDeduccionesDeposito = $totalDeduccionesDeposito + floatval($deduccion->monto);
                }
            }

            $totalPercepcionesEfectivo = 0;
            $totalPercepcionesDeposito = 0;
            foreach ($percepciones as $dato) {
                $percepcion = Percepcione::create([
                    'idNomina' => $nomina->id,
                    'idFormaPago' => $dato['idFormaPago'],
                    'monto' => $dato['monto'],
                    'idConcepto' => $dato['idConcepto'],
                    'cantidad' => $dato['cantidad'],
                    'valorUnitario' => $dato['valorUnitario'],
                    'eliminado' => 0,
                    'activo' => 1
                ]);
                if($percepcion->idFormaPago === 1){
                    $totalPercepcionesEfectivo = $totalPercepcionesEfectivo + floatval($percepcion->monto);
                }else{
                    $totalPercepcionesDeposito = $totalPercepcionesDeposito + floatval($percepcion->monto);
                }
            }

            $totalNomina = Nomina::find($nomina->id);
            $totalNomina->total = ($totalPercepcionesEfectivo + $totalPercepcionesDeposito) - ($totalDeduccionesEfectivo + $totalDeduccionesDeposito);
            $totalNomina->save();
            return response()->json($nomina, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function mostrar(Request $request){
        try {
            $nominas = Nomina::join('empleados', 'idEmpleado', '=', 'empleados.id')->
            join('sucursales', 'nominas.idSucursal', '=', 'sucursales.id')->
            join('calendarios', 'idCalendario', '=', 'calendarios.id')->
            join('departamentos', 'nominas.idDepartamento', '=', 'departamentos.id')->
            join('niveles', 'idNivel', '=', 'niveles.id')->
            select(
                'nominas.*',
                'departamentos.nombre as departamento',
                'niveles.nombre as nivel',
                'sucursales.nombre as sucursal',
                'empleados.nombre as empleado',
                'calendarios.nombre as calendario',
                DB::raw("(CASE 
                    WHEN(nominas.estatus = 0) THEN 'bg-rojo'
                    WHEN(nominas.estatus = 1) THEN 'bg-amarillo'
                    WHEN(nominas.estatus = 2) THEN 'bg-verde'
                    END) AS bg")
            )->
            where('nominas.idSucursal', '=', $request['idSucursal'])->
            where('nominas.eliminado', '=', 0)->orderBy('nominas.created_at', 'DESC')->get();

            $listas['niveles'] = Nivele::where('eliminado', '=', 0)->get();
            $listas['calendarios'] = Calendario::where('eliminado', '=', 0)->whereRaw('fin > NOW()')->get();
            $listas['departamentos'] = Departamento::where('eliminado', '=', 0)->get();
            $listas['puestos'] = Puesto::where('eliminado', '=', 0)->get();
            $listas['empleados'] = Empleado::where('eliminado', '=', 0)->get();
            $listas['conceptosDeducciones'] = Conceptosdeduccione::where('eliminado', '=', 0)->get();
            $listas['conceptosPercepciones'] = Conceptospercepcione::where('eliminado', '=', 0)->get();
            $listas['bancos'] = Banco::where('eliminado', '=', 0)->get();

            $respuesta['nominas'] = $nominas;
            $respuesta['listas'] = $listas;
            
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Erro en el servidor', 400);
        }
    }

    function nomina(Request $request){
        try {
            $listas['niveles'] = Nivele::where('eliminado', '=', 0)->get();
            $listas['calendarios'] = Calendario::where('eliminado', '=', 0)->get();
            $listas['departamentos'] = Departamento::where('eliminado', '=', 0)->get();
            $listas['puestos'] = Puesto::where('eliminado', '=', 0)->get();
            $listas['empleados'] = Empleado::where('eliminado', '=', 0)->get();
            $listas['conceptosDeducciones'] = Conceptosdeduccione::where('eliminado', '=', 0)->get();
            $listas['conceptosPercepciones'] = Conceptospercepcione::where('eliminado', '=', 0)->get();
            $listas['bancos'] = Banco::where('eliminado', '=', 0)->get();

            $nomina = Nomina::find($request['id']);
            $nomina->deducciones = Deduccione::join('conceptosdeducciones', 'idConcepto', '=', 'conceptosdeducciones.id')->
                                                    select('deducciones.*', 'conceptosdeducciones.nombre as concepto')->
                                                    where('idNomina', '=', $nomina->id)->
                                                    where('deducciones.eliminado', '=', 0)->get();
            $nomina->percepciones = Percepcione::join('conceptospercepciones', 'idConcepto', '=', 'conceptospercepciones.id')->
                                                select('percepciones.*', 'conceptospercepciones.nombre as concepto')->
                                                where('idNomina', '=', $nomina->id)->
                                                where('percepciones.eliminado', '=', 0)->get();
            $respuesta['nomina'] = $nomina;
            $respuesta['listas'] = $listas;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function creadas(){
        try {
            $funciones = new Nominas();
            $respuesta['nominas'] = $funciones->creadas();
            $respuesta['listas'] = $funciones->listas();
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Erro en el servidor', 400);
        }
    }

    function autorizadas(Request $request){
        try {
            $funciones = new Nominas();
            $nominas = $funciones->autorizadas($request['sucursalID']);
            return response()->json($nominas, 200);
        } catch (Exception $e) {
            return response()->json('Erro en el servidor', 400);
        }
    }

    function autorizar(Request $request){
        try {
            foreach ($request['nominas'] as $registro) {
                $nomina = Nomina::find($registro['id']);
                $nomina->estatus = 1;
                $nomina->save();
            }
            return response()->json($request, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function cobrar(Request $request){
        $consultas = new Consultas();
        try {
            $funciones = new Nominas();
            $sucursales = new Sucursales();

            $consultas->start();
            $nomina = $funciones->datos($request['id']);
            $totalEfectivo = floatval($nomina->percepcionesEfectivo) - floatval($nomina->deduccionesEfectivo);
            $totalDeposito = floatval($nomina->percepcionesDeposito) - floatval($nomina->deduccionesDeposito);

            $saldoSucursal = $sucursales->saldo($request['sucursalID']);
            if($totalEfectivo > $saldoSucursal){
                return response()->json("No cuentas con suficiente saldo para realizar este egreso", 400);
            }

            if(floatval($totalEfectivo) > 0){
                $funciones->crearEgreso($nomina, $request['usuarioID'], $totalEfectivo, 1);    
            }

            if(floatval($totalDeposito)){
                $funciones->crearEgreso($nomina, $request['usuarioID'], $totalDeposito, 4);    
            }

            $actualizar = $funciones->cobrar($nomina->id);
            $consultas->commit();
            return response()->json($actualizar, 200);
        } catch (Exception $e) {
            $consultas->rollback();
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request){
        try {
            //return response()->json('No se pueden modificar nominas comuniquese a sistemas', 400);
            $nomina = Nomina::find($request['id']);
            $nomina->quincena = $request['quincena'];
            $nomina->fechaInicio = $request['fechaInicio'];
            $nomina->fechaFin = $request['fechaFin'];
            $nomina->fechaExpedicion = $request['fechaExpedicion'];
            $nomina->idBanco = $request['idBanco'];
            $nomina->idPuesto = $request['idPuesto'];
            $nomina->idEmpleado = $request['idEmpleado'];
            $nomina->observaciones = $request['observaciones'];
            $nomina->save();

            return response()->json($nomina, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function cuenta(Request $request) {
        try {
            $nomina = Nomina::find($request['id']);
            $docentes = ($nomina->idDepartamento === 1) ? 1 : 2;
            $percepciones = Percepcione::join('conceptospercepciones', 'idConcepto', '=', 'conceptospercepciones.id')->
                                         select('percepciones.*', 'conceptospercepciones.nombre as concepto')->
                                         where('idNomina', '=', $request['id'])->
                                         where('percepciones.eliminado', '=', 0)->get();
            $deducciones = Deduccione::join('conceptosdeducciones', 'idConcepto', '=', 'conceptosdeducciones.id')->
                                       select('deducciones.*', 'conceptosdeducciones.nombre as concepto')->
                                       where('idNomina', '=', $request['id'])->
                                       where('deducciones.eliminado', '=', 0)->get();

            $listaDeducciones = Conceptosdeduccione::where('docentes', '=', $docentes)->get();
            $listaPercepciones = Conceptospercepcione::where('docentes', '=', $docentes)->get();
            $respuesta['percepciones'] = $percepciones;
            $respuesta['deducciones'] = $deducciones;
            $respuesta['listaPercepciones'] = $listaPercepciones;
            $respuesta['listaDeducciones'] = $listaDeducciones;
            $respuesta['profesor'] = ($nomina->idDepartamento === 1) ? true : false;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function agregarPercepcion(Request $request) {
        try {
            $nomina = Nomina::find($request['nomina']);
            if(intval($nomina->estatus) === 2){
                $dato = $request['percepcion'];
                $solictud = Solicitudnomina::create([
                    'forma' => 1,
                    'idModificacion' => 0,
                    'idNomina' => $request['nomina'],
                    'idFormaPago' => $dato['idFormaPago'],
                    'monto' => (floatval($dato['cantidad']) * floatval($dato['valorUnitario'])),
                    'idConcepto' => $dato['idConcepto'],
                    'cantidad' => $dato['cantidad'],
                    'valorUnitario' => $dato['valorUnitario'],
                    'idUsuario' => $request['usuarioID'],
                    'estatus' => 1,
                    'tipo' => 1,
                    'eliminado' => 0,
                    'activo' => 1
                ]);
                $solictud->agrego = 0;
                return response()->json($solictud, 200);
            }else{
                $dato = $request['percepcion'];
                $percepcion = Percepcione::create([
                    'idNomina' => $request['nomina'],
                    'idFormaPago' => $dato['idFormaPago'],
                    'monto' => (floatval($dato['cantidad']) * floatval($dato['valorUnitario'])),
                    'idConcepto' => $dato['idConcepto'],
                    'cantidad' => $dato['cantidad'],
                    'valorUnitario' => $dato['valorUnitario'],
                    'eliminado' => 0,
                    'activo' => 1
                ]);
                $concepto = Conceptospercepcione::find($percepcion->idConcepto);
                calcularTotalNomina($request['nomina']);

                return response()->json($percepcion, 200);
            }
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function agregarDeduccion(Request $request) {
        try {
            $nomina = Nomina::find($request['nomina']);
            if(intval($nomina->estatus) === 2){
                $dato = $request['deduccion'];
                $solictud = Solicitudnomina::create([
                    'forma' => 1,
                    'idModificacion' => 0,
                    'idNomina' => $request['nomina'],
                    'idFormaPago' => $dato['idFormaPago'],
                    'monto' => (floatval($dato['cantidad']) * floatval($dato['valorUnitario'])),
                    'idConcepto' => $dato['idConcepto'],
                    'cantidad' => $dato['cantidad'],
                    'valorUnitario' => $dato['valorUnitario'],
                    'idUsuario' => $request['usuarioID'],
                    'estatus' => 1,
                    'tipo' => 2,
                    'eliminado' => 0,
                    'activo' => 1
                ]);
                $solictud->agrego = 0;
                return response()->json($solictud, 200);
            }else{
                $dato = $request['deduccion'];
                $deduccion = Deduccione::create([
                    'idNomina' => $request['nomina'],
                    'idFormaPago' => $dato['idFormaPago'],
                    'monto' => (floatval($dato['cantidad']) * floatval($dato['valorUnitario'])),
                    'idConcepto' => $dato['idConcepto'],
                    'cantidad' => $dato['cantidad'],
                    'valorUnitario' => $dato['valorUnitario'],
                    'eliminado' => 0,
                    'activo' => 1
                ]);
                $concepto = Conceptosdeduccione::find($deduccion->idConcepto);
                calcularTotalNomina($request['nomina']);

                return response()->json($deduccion, 200);
            }
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminarPercepcion(Request $request) {
        try {
            $percepcion = Percepcione::find($request['id']);

            if(intval($percepcion->idFormaPago) === 1){
                if(eliminarEfectivo($request['idNomina'], $request['monto']))
                    return response()->json('No se puede tener un saldo negativo', 400);
            }else{
                if(eliminarDeposito($request['idNomina'], $request['monto']))
                    return response()->json('No se puede tener un saldo negativo', 400);
            }

            $nomina = Nomina::find($request['idNomina']);
            if(intval($nomina->estatus) === 2){
                $solictud = Solicitudnomina::create([
                    'forma' => 2,
                    'idModificacion' => $request['id'],
                    'idNomina' => $request['idNomina'],
                    'idFormaPago' => $request['idFormaPago'],
                    'monto' => $request['monto'],
                    'idConcepto' => $request['idConcepto'],
                    'cantidad' => 0,
                    'valorUnitario' => 0,
                    'idUsuario' => $request['usuarioID'],
                    'estatus' => 1,
                    'tipo' => 1,
                    'eliminado' => 0,
                    'activo' => 1
                ]);
                $solictud->agrego = 0;
                return response()->json($solictud, 200);
            }else{
                $percepcion->eliminado = 1;
                $percepcion->save();
                $concepto = Conceptospercepcione::find($percepcion->idConcepto);

                return response()->json($percepcion, 200);
            }
            return response()->json($percepcion, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminarDeduccion(Request $request) {
        try {
            $deduccion = Deduccione::find($request['id']);

            if(intval($deduccion->idFormaPago) === 1){
                if(eliminarEfectivo($request['idNomina'], $request['monto']))
                    return response()->json('No se puede tener un saldo negativo', 400);
            }else{
                if(eliminarDeposito($request['idNomina'], $request['monto']))
                    return response()->json('No se puede tener un saldo negativo', 400);
            }

            $nomina = Nomina::find($request['idNomina']);
            if(intval($nomina->estatus) === 2){
                $solictud = Solicitudnomina::create([
                    'forma' => 2,
                    'idModificacion' => $request['id'],
                    'idNomina' => $request['idNomina'],
                    'idFormaPago' => $request['idFormaPago'],
                    'monto' => $request['monto'],
                    'idConcepto' => $request['idConcepto'],
                    'cantidad' => 0,
                    'valorUnitario' => 0,
                    'idUsuario' => $request['usuarioID'],
                    'estatus' => 1,
                    'tipo' => 2,
                    'eliminado' => 0,
                    'activo' => 1
                ]);
                $solictud->agrego = 0;
                return response()->json($solictud, 200);
            }else{
                $deduccion->eliminado = 1;
                $deduccion->save();
                $concepto = Conceptosdeduccione::find($deduccion->idConcepto);

                return response()->json($deduccion, 200);
            }
            return response()->json($deduccion, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function mostrarSolicitudesNominas(Request $request){
        try {
            $solicitudes = Solicitudnomina::join('nominas', 'idNomina', '=', 'nominas.id')->
            join('usuarios', 'solicitudnominas.idUsuario', '=', 'usuarios.id')->
            join('empleados', 'usuarios.idEmpleado', '=', 'empleados.id')->
            leftjoin('formaspagos', 'solicitudnominas.idFormaPago', '=', 'formaspagos.id')->
            leftJoin('conceptospercepciones', 'idConcepto', '=', 'conceptospercepciones.id')->
            select(
                'nominas.folio as folio',
                'solicitudnominas.*',
                'empleados.nombre as empleado',
                'formaspagos.nombre as forma',
                'conceptospercepciones.nombre as concepto',
                DB::raw("IF(solicitudnominas.forma = 1, 'Agregar', 'Eliminar') as accion"),
                DB::raw("IF(solicitudnominas.tipo = 1, 'Percepcion', 'Deduccion') as tipoCambio"),
                DB::raw("(CASE 
                            WHEN(solicitudnominas.estatus = 2) THEN 'bg-verde'
                            WHEN(solicitudnominas.estatus = 3) THEN 'bg-rojo'
                            END) AS bg")
            )->
            where('solicitudnominas.eliminado', '=', 0)->get();
            return response()->json($solicitudes, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function aceptarSolicitudNomina(Request $request){
        try {
            DB::beginTransaction();
            if(intval($request['tipo']) === 1){
                $solicitud = Solicitudnomina::find($request['id']);
                if(intval($solicitud->forma) === 1){
                    $percepcion = Percepcione::create([
                        'idNomina' => $solicitud->idNomina,
                        'idFormaPago' => $solicitud->idFormaPago,
                        'monto' => (floatval($solicitud->cantidad) * floatval($solicitud->valorUnitario)),
                        'idConcepto' => $solicitud->idConcepto,
                        'cantidad' => $solicitud->cantidad,
                        'valorUnitario' => $solicitud->valorUnitario,
                        'eliminado' => 0,
                        'activo' => 1
                    ]);
                }else{
                    $percepcion = Percepcione::find($solicitud->idModificacion);
                    $percepcion->eliminado = 1;
                    $percepcion->save();
                }

                $solicitud->estatus = 2;

                $solicitud->save();
                $respuesta = calcularEgresosNomina($request['idNomina'], $request['usuario']);

                $consulta = "SELECT SUM(monto) as monto FROM percepciones where eliminado = 0 AND idNomina = ".$request['idNomina'];
                $percepciones = DB::select($consulta, array());
                $totalPercepciones = (is_null($percepciones[0]->monto)) ? 0 : $percepciones[0]->monto;

                $consulta = "SELECT SUM(monto) as monto FROM deducciones where eliminado = 0 AND idNomina = ".$request['idNomina'];
                $deducciones = DB::select($consulta, array());
                $totalDeducciones = (is_null($deducciones[0]->monto)) ? 0 : $deducciones[0]->monto;
                $totalNomina = $totalPercepciones - $totalDeducciones;

                $nomina = Nomina::find($request['idNomina']);
                $nomina->total = $totalNomina;
                $nomina->save();
                DB::commit();

                return response()->json($solicitud, 200);
            }else{
                $solicitud = Solicitudnomina::find($request['id']);
                if(intval($solicitud->forma) === 1){
                    $deduccion = Deduccione::create([
                        'idNomina' => $solicitud->idNomina,
                        'idFormaPago' => $solicitud->idFormaPago,
                        'monto' => (floatval($solicitud->cantidad) * floatval($solicitud->valorUnitario)),
                        'idConcepto' => $solicitud->idConcepto,
                        'cantidad' => $solicitud->cantidad,
                        'valorUnitario' => $solicitud->valorUnitario,
                        'eliminado' => 0,
                        'activo' => 1
                    ]);
                }else{
                    $deduccion = Deduccione::find($solicitud->idModificacion);
                    $deduccion->eliminado = 1;
                    $deduccion->save();
                }

                $solicitud->estatus = 2;
                $solicitud->save();
                $respuesta = calcularEgresosNomina($request['idNomina'], $request['usuario']);

                $consulta = "SELECT SUM(monto) as monto FROM percepciones where eliminado = 0 AND idNomina = ".$request['idNomina'];
                $percepciones = DB::select($consulta, array());
                $totalPercepciones = (is_null($percepciones[0]->monto)) ? 0 : $percepciones[0]->monto;

                $consulta = "SELECT SUM(monto) as monto FROM deducciones where eliminado = 0 AND idNomina = ".$request['idNomina'];
                $deducciones = DB::select($consulta, array());
                $totalDeducciones = (is_null($deducciones[0]->monto)) ? 0 : $deducciones[0]->monto;
                $totalNomina = $totalPercepciones - $totalDeducciones;

                $nomina = Nomina::find($request['idNomina']);
                $nomina->total = $totalNomina;
                $nomina->save();
                DB::commit();

                return response()->json($solicitud, 200);
            }
        } catch (Exception $e) {
            DB::rollback();
            return response()->json('Error en el servidor', 400);
        }
    }

    function rechazarSolicitudNomina(Request $request){
        try {
            $solicitud = Solicitudnomina::find($request['id']);
            $solicitud->estatus = 3;
            $solicitud->save();
            return response()->json($solicitud, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}