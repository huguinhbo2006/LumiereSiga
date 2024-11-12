<?php

namespace App\Http\Controllers;
use App\Clases\Fechas;
use App\Alumno;
use App\Alumnoabono;
use App\Alumnocargo;
use App\Alumnodescuento;
use App\Alumnodevolucione;
use App\Alumnoextra;
use App\Ficha;
use App\Sexo;
use App\Alumnodomicilio;
use App\Tutore;
use App\Cargodescuento;
use App\Formaspago;
use App\Grupo;
use App\Altacurso;
use App\Usuario;
use App\Curso;
use App\Horario;
use App\Calendario;
use App\Sucursale;
use App\Nivele;
use App\Subnivele;
use App\Categoria;
use App\Modalidade;
use App\Aspiracione;
use App\Centrosuniversitario;
use App\Carrera;
use App\Datosescolare;
use App\Ingreso;
use App\Banco;
use App\Conceptosabono;
use App\Rubro;
use App\Rubrosegreso;
use App\Tiposingreso;
use App\Tiposegreso;
use App\Empleado;
use App\Nomina;
use App\Departamento;
use App\Puesto;
use App\Percepcione;
use App\Deduccione;
use App\Egreso;
use App\Cupone;
use Carbon\Carbon;
use App\Clases\Balances;
use App\Clases\Generales;
use App\Clases\Ingresos;
use App\Clases\Egresos;
use App\Clases\Sucursales;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
/*include "funciones/FuncionesGenerales.php";
include "funciones/funcionesBaseDatos.php";*/

class PDFController extends BaseController
{

    function fichaInscripcion(Request $request){
        try{
            $date = Carbon::now();
            $date = $date->format('d-m-Y');

            $consulta = "";

            $respuesta = array();
            $ficha = Ficha::find($request['ficha']);
            $alumno = Alumno::find($ficha->idAlumno);
            $grupo = Grupo::find($ficha->idGrupo);
            $alta = Altacurso::find($grupo->idAltaCurso);
            $usuario = Usuario::find($ficha->idUsuario);
            $aspiraciones = Aspiracione::where('idFicha', '=', $ficha->id)->get();
            $aspiraciones = (count($aspiraciones) > 0) ? $aspiraciones[0] : null;
            $carrera = (is_null($aspiraciones)) ? null :  Carrera::find($aspiraciones->idCarrera);
            $escolares = Datosescolare::where('idAlumno', '=', $alumno->id)->get();
            $escolares = (count($escolares) > 0) ? $escolares[0] : null;
            $horario = Horario::find($grupo->idHorario);

            $respuesta['fecha'] = strtoupper($date);
            $respuesta['socio'] = strtoupper($usuario->usuario);
            $respuesta['plantel'] = strtoupper(Sucursale::find($ficha->idSucursalImparticion)->nombre);
            $respuesta['telefonoPlantel'] = strtoupper(Sucursale::find($ficha->idSucursalImparticion)->telefono);
            $respuesta['nivel'] = strtoupper(Nivele::find($alta->idNivel)->nombre);
            $respuesta['subnivel'] = strtoupper(Subnivele::find($alta->idSubnivel)->nombre);
            $respuesta['modalidad'] = strtoupper(Categoria::find($alta->idCategoria)->nombre);
            $respuesta['categoria'] = strtoupper(Modalidade::find($alta->idModalidad)->nombre);
            $respuesta['curso'] = strtoupper(Curso::find($alta->idCurso)->nombre);
            $respuesta['horario'] = strtoupper($horario->inicio. ' - '. $horario->fin);
            $respuesta['calendario'] = strtoupper(Calendario::find($alta->idCalendario)->nombre);
            $respuesta['alumno'] = strtoupper($alumno->nombre.' '.$alumno->apellidoPaterno.' '.$alumno->apellidoMaterno);
            $respuesta['celular'] = strtoupper($alumno->celular);
            $respuesta['centro'] = (is_null($aspiraciones)) ? '' : strtoupper(Centrosuniversitario::find($aspiraciones->idCentroUniversitario)->nombre);
            $respuesta['carrera'] = (is_null($carrera)) ? '' : strtoupper($carrera->nombre);
            $respuesta['puntaje'] = (is_null($carrera)) ? '' : strtoupper($carrera->puntaje);
            $respuesta['promedio'] = (is_null($escolares)) ? '' : strtoupper($escolares->promedio);
            $respuesta['faltante'] = (is_null($carrera)) ? '' : strtoupper(floatval($carrera->puntaje) - floatval($escolares->promedio));
            $respuesta['inicio'] = strtoupper(Carbon::parse($alta->inicio)->format('d-m-Y'));
            $respuesta['fin'] = strtoupper(Carbon::parse($alta->fin)->format('d-m-Y'));
            $respuesta['limite'] = strtoupper(Carbon::parse($alta->limitePago)->format('d-m-Y'));
            $respuesta['folio'] = strtoupper($ficha->folio);

            if(intval($ficha->estatus) === 3){
                $respuesta['congelado'] = true;
                $cupon = Cupone::where('idFicha', '=', $ficha->id)->get();
                $respuesta['montoCongelado'] = (count($cupon) > 0) ? $cupon[0]->monto : 0;
                $respuesta['cuponCongelado'] = (count($cupon) > 0) ? $cupon[0]->cupon : 'N/A';
            }else
                $respuesta['congelado'] = false;

            $cargos = Alumnocargo::where('idFicha', '=', $request['ficha'])->where('eliminado', '=', 0)->get();
            $respuesta['estadoCuenta']['cargos'] = $cargos;

            $abonos = Alumnoabono::join('formaspagos', 'idFormaPago', '=', 'formaspagos.id')->join('metodospagos', 'idMetodoPago', '=', 'metodospagos.id')->select('alumnoabonos.*', 'formaspagos.nombre as forma', 'metodospagos.nombre as metodo')->where('idFicha', '=', $request['ficha'])->where('alumnoabonos.eliminado', '=', 0)->get();
            $respuesta['estadoCuenta']['abonos'] = $abonos;

            $descuentos = Alumnodescuento::where('idFicha', '=', $request['ficha'])->where('eliminado', '=', 0)->get();
            $respuesta['estadoCuenta']['descuentos'] = $descuentos;

            $devoluciones = Alumnodevolucione::join('formaspagos', 'idFormaPago', '=', 'formaspagos.id')->select('alumnodevoluciones.*', 'formaspagos.nombre as forma')->where('idFicha', '=', $request['ficha'])->where('alumnodevoluciones.eliminado', '=', 0)->get();
            $respuesta['estadoCuenta']['devoluciones'] = $devoluciones;

            $extras = Alumnoextra::where('idFicha', '=', $request['ficha'])->where('eliminado', '=', 0)->get();
            $respuesta['estadoCuenta']['extras'] = $extras;

            $total = 0;
            $totalCargos = 0;
            $totalAbonos = 0;
            $totalDescuentos = 0;
            $totalDevoluciones = 0;
            $totalExtras = 0;
            foreach ($cargos as $cargo) {
                $total = floatval($total) - floatval($cargo->monto);
                $totalCargos = floatval($totalCargos) + floatval($cargo->monto);
            }
            foreach ($extras as $extra) {
                $total = floatval($total) + floatval($extra->monto);
                $totalExtras = floatval($totalExtras) + floatval($extra->monto);
            }
            foreach ($abonos as $abono) {
                $total = floatval($total) + floatval($abono->monto);
                $totalAbonos = floatval($totalAbonos) + floatval($abono->monto);
            }
            foreach ($descuentos as $descuento) {
                $total = floatval($total) + floatval($descuento->monto);
                $totalDescuentos = floatval($totalDescuentos) + floatval($descuento->monto);
            }
            foreach ($devoluciones as $devolucion) {
                $total = floatval($total) - floatval($devolucion->monto);
                $totalDevoluciones = floatval($totalDevoluciones) - floatval($devolucion->monto);
            }

            $respuesta['estadoCuenta']['total'] = $total;
            $respuesta['estadoCuenta']['totalCargos'] = $totalCargos;
            $respuesta['estadoCuenta']['totalAbonos'] = $totalAbonos;
            $respuesta['estadoCuenta']['totalExtras'] = $totalExtras;
            $respuesta['estadoCuenta']['totalDevoluciones'] = $totalDevoluciones;
            $respuesta['estadoCuenta']['totalDescuentos'] = $totalDescuentos;

            return response()->json($respuesta, 200);
        }catch(Exception $e){
            return response()->json('Error de servidor', 400);
        }
    }

    function cartaCongelacion(Request $request){
        try {
            $ficha = Ficha::find($request['ficha']);
            $grupo = Grupo::find($ficha->idGrupo);
            $alta = Altacurso:: find($grupo->idAltaCurso);
            $alumno = Alumno::find($ficha->idAlumno);
            $horario = Horario::find($grupo->idHorario);
            $pagos = Alumnoabono::where('idFicha', '=', $ficha->id)->get();
            $totalPagos = 0;
            foreach ($pagos as $pago) {
                $totalPagos = $totalPagos + $pago->monto;
            }

            $respuesta['alumno'] = $alumno->nombre.' '.$alumno->apellidoPaterno.' '.$alumno->apellidoMaterno;
            $respuesta['sucursal'] = Sucursale::find($ficha->idSucursalImparticion)->nombre;
            $respuesta['calendario'] = Calendario::find($ficha->idCalendario)->nombre;
            $respuesta['fecha'] = formatearFecha(Carbon::now());
            $respuesta['curso'] = Curso::find($alta->idCurso)->nombre;
            $respuesta['categoria'] = Categoria::find($alta->idCategoria)->nombre;
            $respuesta['modalidad'] = Modalidade::find($alta->idModalidad)->nombre;
            $respuesta['horario'] = $horario->inicio.' A '.$horario->fin;
            $respuesta['pago'] = number_format($totalPagos, 2, '.', ',');
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function reciboPago(Request $request){
        try {
            $recibo = Alumnoabono::find($request['recibo']);
            $ficha = Ficha::find($recibo->idFicha);
            $grupo = Grupo::find($ficha->idGrupo);
            $alumno = Alumno::find($ficha->idAlumno);
            $ingreso = Ingreso::find($recibo->idIngreso);
            $usuario = Usuario::find($recibo->idUsuario);
            $sucursal = Sucursale::find($ficha->idSucursalInscripcion);
            $alta = Altacurso::find($grupo->idAltaCurso);
            $horario = Horario::find($grupo->idHorario);
            $respuesta = array();

            $respuesta['folio'] = $ingreso->folio;
            $respuesta['fecha1'] = formatearFecha($recibo->created_at);
            $respuesta['hora'] = formatearHora($recibo->created_at);
            $respuesta['fecha'] = $recibo->created_at;
            $respuesta['asesor'] = strtoupper($usuario->usuario);
            $respuesta['plantel'] = strtoupper($sucursal->nombre);
            $respuesta['telefonoSucursal'] = $sucursal->telefono;
            $respuesta['nivel'] = strtoupper(Nivele::find($alta->idNivel)->nombre);
            $respuesta['modalidad'] = strtoupper(Categoria::find($alta->idCategoria)->nombre);
            $respuesta['categoria'] = strtoupper(Modalidade::find($alta->idModalidad)->nombre);
            $respuesta['curso'] = strtoupper(Curso::find($alta->idCurso)->nombre);
            $respuesta['horario'] = strtoupper($horario->inicio. ' - '. $horario->fin);
            $respuesta['alumno'] = strtoupper($alumno->nombre.' '.$alumno->apellidoPaterno.' '.$alumno->apellidoMaterno);
            $respuesta['precio'] = $alta->precio;
            $respuesta['inicio'] = strtoupper(Carbon::parse($alta->inicio)->format('d-m-Y'));
            $respuesta['fin'] = strtoupper(Carbon::parse($alta->fin)->format('d-m-Y'));
            $respuesta['limite'] = strtoupper($alta->limitePago);
            $respuesta['monto'] = $recibo->monto;
            $respuesta['concepto'] = Conceptosabono::find($recibo->idConcepto)->nombre;
            $respuesta['fechaPago'] = Carbon::parse($ingreso->fecha)->format('d-m-Y');
            $respuesta['ficha'] = $ficha->folio;

            $cargos = Alumnocargo::where('idFicha', '=', $ficha->id)->where('eliminado', '=', 0)->get();

            $abonos = Alumnoabono::join('formaspagos', 'idFormaPago', '=', 'formaspagos.id')->join('metodospagos', 'idMetodoPago', '=', 'metodospagos.id')->select('alumnoabonos.*', 'formaspagos.nombre as forma', 'metodospagos.nombre as metodo')->where('idFicha', '=', $ficha->id)->where('alumnoabonos.eliminado', '=', 0)->get();

            $descuentos = Alumnodescuento::where('idFicha', '=', $ficha->id)->where('eliminado', '=', 0)->get();

            $devoluciones = Alumnodevolucione::join('formaspagos', 'idFormaPago', '=', 'formaspagos.id')->select('alumnodevoluciones.*', 'formaspagos.nombre as forma')->where('idFicha', '=', $ficha->id)->where('alumnodevoluciones.eliminado', '=', 0)->get();

            $extras = Alumnoextra::where('idFicha', '=', $ficha->id)->where('eliminado', '=', 0)->get();

            $total = 0;
            $totalCargos = 0;
            $totalAbonos = 0;
            $totalDescuentos = 0;
            $totalDevoluciones = 0;
            $totalExtras = 0;
            foreach ($cargos as $cargo) {
                $total = floatval($total) - floatval($cargo->monto);
                $totalCargos = floatval($totalCargos) + floatval($cargo->monto);
            }
            foreach ($extras as $extra) {
                $total = floatval($total) + floatval($extra->monto);
                $totalExtras = floatval($totalExtras) + floatval($extra->monto);
            }
            foreach ($abonos as $abono) {
                $total = floatval($total) + floatval($abono->monto);
                $totalAbonos = floatval($totalAbonos) + floatval($abono->monto);
            }
            foreach ($descuentos as $descuento) {
                $total = floatval($total) + floatval($descuento->monto);
                $totalDescuentos = floatval($totalDescuentos) + floatval($descuento->monto);
            }
            foreach ($devoluciones as $devolucion) {
                $total = floatval($total) - floatval($devolucion->monto);
                $totalDevoluciones = floatval($totalDevoluciones) - floatval($devolucion->monto);
            }

            $forma = Formaspago::find($ingreso->idFormaPago);

            $respuesta['restante'] = $total;
            $respuesta['anterior'] = floatval($total) - floatval($recibo->monto);
            $respuesta['forma'] = Formaspago::find($recibo->idFormaPago)->nombre;
            $respuesta['banco'] = (intval($forma->id) === 1) ? 'N/A' : $forma->nombre;
            $respuesta['autorizacion'] = (intval($forma->id) === 1) ? 'N/A' : $ingreso->numeroReferencia;


            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error al traer recibo de pago', 400);
        }
    }

    function ingreso(Request $request){
        try {
            $fechas = new Fechas();

            $ingreso = Ingreso::leftjoin('usuarios', 'ingresos.idUsuario', '=', 'usuarios.id')->
            leftjoin('sucursales', 'ingresos.idSucursal', '=', 'sucursales.id')->
            leftjoin('formaspagos', 'ingresos.idFormaPago', '=', 'formaspagos.id')->
            leftjoin('rubros', 'ingresos.idRubro', '=', 'rubros.id')->
            leftjoin('tiposingresos', 'ingresos.idTipo', '=', 'tiposingresos.id')->
            leftjoin('empleados', 'usuarios.idEmpleado', '=', 'empleados.id')->
            select(
                'ingresos.folio',
                'ingresos.activo',
                'sucursales.nombre as plantel',
                'ingresos.created_at',
                DB::raw('TIME(ingresos.created_at) as hora'),
                DB::raw('UPPER(usuarios.usuario) as usuario'),
                DB::raw('UPPER(empleados.nombre) as empleado'),
                'ingresos.concepto',
                'ingresos.monto',
                'formaspagos.nombre as forma',
                'rubros.nombre as rubro',
                'tiposingresos.nombre as tipo',
                'ingresos.observaciones'
            )->where('ingresos.id', '=', $request['ingreso'])->get()[0];
            return response()->json($ingreso, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function egreso(Request $request){
        try {
            $egreso = Egreso::join('usuarios', 'egresos.idUsuario', '=', 'usuarios.id')->
            join('sucursales', 'egresos.idSucursal', '=', 'sucursales.id')->
            join('empleados', 'usuarios.idEmpleado', '=', 'empleados.id')->
            join('formaspagos', 'egresos.idFormaPago', '=', 'formaspagos.id')->
            join('rubrosegresos', 'egresos.idRubro', '=', 'rubrosegresos.id')->
            join('tiposegresos', 'egresos.idTipo', '=', 'tiposegresos.id')->
            select(
                'egresos.folio',
                'egresos.activo',
                'sucursales.nombre as plantel',
                'egresos.created_at as fecha',
                DB::raw('TIME(egresos.created_at) as hora'),
                DB::raw('UPPER(usuarios.usuario) as usuario'),
                DB::raw('UPPER(empleados.nombre) as empleado'),
                'egresos.concepto',
                'egresos.monto',
                'formaspagos.nombre as forma',
                'rubrosegresos.nombre as rubro',
                'tiposegresos.nombre as tipo',
                'egresos.observaciones'
            )->where('egresos.id', '=', $request['egreso'])->get()[0];
            return response()->json($egreso, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function nomina(Request $request){
        try {
            $respuesta = array();
            $nomina = Nomina::find($request['nomina']);
            $usuario = Empleado::find($nomina->idEmpleado);
            $respuesta['folio'] = $nomina->folio;
            $respuesta['fechaInicio'] = $nomina->fechaInicio;
            $respuesta['fechaFin'] = $nomina->fechaFin;
            $respuesta['fechaExpedicion'] = $nomina->fechaExpedicion;
            $respuesta['observaciones'] = $nomina->observaciones;
            $respuesta['colaborador'] = $usuario->nombre;
            $respuesta['departamento'] = Departamento::find($nomina->idDepartamento)->nombre;
            $respuesta['puesto'] = Puesto::find($nomina->idPuesto)->nombre;
            $respuesta['sucursal'] = Sucursale::find($nomina->idSucursal)->nombre;

            $percepciones = Percepcione::join('conceptospercepciones', 'idConcepto', '=', 'conceptospercepciones.id')->
                                         join('formaspagos', 'idFormaPago', '=', 'formaspagos.id')->
                                         select('percepciones.*', 'conceptospercepciones.nombre as concepto', 'formaspagos.nombre as forma')->
                                         where('idNomina', '=', $nomina->id)->
                                         where('percepciones.eliminado', '=', 0)->
                                         orderBy('percepciones.idConcepto', 'DESC')->get();

            $deducciones = Deduccione::join('conceptosdeducciones', 'idConcepto', '=', 'conceptosdeducciones.id')->
                                         join('formaspagos', 'idFormaPago', '=', 'formaspagos.id')->
                                         select('deducciones.*', 'conceptosdeducciones.nombre as concepto', 'formaspagos.nombre as forma')->
                                         where('idNomina', '=', $nomina->id)->
                                         where('deducciones.eliminado', '=', 0)->
                                         orderBy('deducciones.idConcepto', 'DESC')->get();
            $respuesta['deducciones'] = $deducciones;
            $respuesta['percepciones'] = $percepciones;
            $respuesta['hoy'] = Carbon::now()->format('d-m-Y');
            $respuesta['tipo'] = $usuario->idDepartamento;

            $totalPercepciones = 0;
            $totalDeducciones = 0;
            foreach ($percepciones as $percepcion) {
                $totalPercepciones = floatval($percepcion->monto) + floatval($totalPercepciones);
            }
            $respuesta['totalPercepciones'] = $totalPercepciones;

            foreach ($deducciones as $deduccion) {
                $totalDeducciones = floatval($deduccion->monto) + floatval($totalDeducciones);
            }
            $respuesta['totalDeducciones'] = $totalDeducciones;

            $respuesta['total'] = $totalPercepciones - $totalDeducciones;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function corte(Request $request){
        try {
            $funciones = new Balances();
            $ingresos = new Ingresos();
            $generales = new Generales();
            $egresos = new Egresos();
            $sucursales = new Sucursales();

            $usuario = Usuario::find($request['usuarioID']);
            $respuesta = array();
            $respuesta['fechaCorte'] = Carbon::now()->format('Y-m-d H:i:s');
            $respuesta['usuario'] = Empleado::find($usuario->idEmpleado)->nombre;

            $ingresosDiarios = $ingresos->ingresosDiariosUsuario($request['usuarioID'], $request['sucursalID']);
            $respuesta['ingresos'] = $generales->listaObjetosArray($ingresosDiarios);


            $egresosDiarios = $egresos->egresosDiariosUsuario($request['usuarioID'], $request['sucursalID']);
            $respuesta['egresos'] = $generales->listaObjetosArray($egresosDiarios);

            $formasIngresos = $funciones->ingresosFormaPago($request['sucursalID']);
            $respuesta['totalIngresos'] = $generales->listaObjetosArray($formasIngresos);

            $formasEgresos = $funciones->egresosFormaPago($request['sucursalID']);
            $respuesta['totalEgresos'] = $generales->listaObjetosArray($formasEgresos);

            $fichas = $funciones->fichas($request['usuarioID'], $request['sucursalID']);
            $respuesta['inscripciones'] = $generales->listaObjetosArray($fichas);

            $respuesta['vale'] = $funciones->valeAdministrativo($request['sucursalID']);

            $respuesta['saldoTotal'] = $sucursales->saldo($request['sucursalID']) - floatval($funciones->valeAdministrativo($request['sucursalID']));

            $respuesta['montoIngresos'] = $ingresos->totalEfectivoUsuarioDia($request['sucursalID'], $request['usuarioID']);

            $respuesta['montoEgresos'] = $egresos->totalEfectivoUsuarioDia($request['sucursalID'], $request['usuarioID']);

            $respuesta['vale'] = $funciones->valeAdministrativo($request['sucursalID']);

            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
    
    function boletaAlumno(Request $request){
        try {
            $respuesta = array();
            $datosFicha = datosFichaAlumno($request['idAlumno'], $request['id']);
            $respuesta['ficha'] = $datosFicha;
            $examenes = traerExamenesFicha($request['id']);
            $listaExamenes = array();
            $maximo = 0;
            $totalSecciones = 0;
            $encabezados = array();
            foreach ($examenes as $examen) {
                if(examenCompletado($examen->id, $request['id'])){
                    $examen->promedio = number_format(traerCalificacionExamen($examen->id, $request['id']), 2, '.');
                    $examen->diferencia = number_format(floatval(($examen->promedio + $datosFicha->promedio) - $datosFicha->puntaje), 2, '.');
                    $examen->total = number_format(floatval($examen->promedio + $datosFicha->promedio), 2, '.');
                    $examen->secciones = traerCalificacionSeccionesPorcentajes($request['id'], $examen->id);
                    $examen->invalidas = traerCalificacionSecciones($request['id'], $examen->id);
                    $examen->secciones = array_merge($examen->secciones, $examen->invalidas);
                    $totalSecciones = count($examen->secciones);
                    if($maximo < $totalSecciones)
                        $maximo = $totalSecciones;
                    $listaExamenes[] = $examen;
                }
            }
            $encabezados[] = 'Nombre Examen';
            $entre = false;
            foreach ($listaExamenes as $examen) {
                if(count($examen->secciones) === $maximo && !$entre){
                    foreach ($examen->secciones as $seccion) {
                        $encabezados[] = $seccion->nombre;
                    }
                    $entre = true;
                }
                if(count($examen->secciones) < $maximo){
                    $promedio = $maximo - count($examen->secciones);
                    $nuevas = array();
                    for ($i=0; $i < $promedio; $i++) { 
                        $nuevaSeccion['nombre'] = '-';
                        $nuevaSeccion['promedio'] = '-';
                        $nuevas[] = $nuevaSeccion;
                    }
                    $examen->secciones = array_merge($examen->secciones, $nuevas);
                }
                $listaDefinitiva[] = $examen;
            }
            $encabezados[] = 'Promedio Examen';
            $encabezados[] = 'Promedio Total';
            $encabezados[] = 'Puntaje Carrera';
            $encabezados[] = 'Diferencia';
            $encabezados[] = 'Estatus';
            $respuesta['examenes'] = $listaExamenes;
            $respuesta['encabezados'] = $encabezados;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function boletaGrupo(Request $request){
        try {
            $alumnos = $request['alumnos'];
            $respuesta = array();
            $examenes = traerExamenesGrupo($request['idGrupo']);
            $encabezados = array();
            $encabezados[] = "Alumno";
            $entre = false;
            foreach ($alumnos as $alumno) {
                $registro = array();
                $registro[] = $alumno['alumno'];
                foreach ($examenes as $examen) {
                    if(!$entre){
                        $encabezados[] = $examen->nombre;
                    }
                    if(examenCalificado($examen->id, $alumno['id'])){
                        $calificacion = traerCalificacionExamen($examen->id, $alumno['id']);
                        $registro[] = number_format($calificacion, 2, '.', ',');
                    }else{
                        $registro[] = "N/A";
                    }
                }
                if(!$entre)
                    $respuesta[] = $encabezados;
                $entre = true;
                $respuesta[] = $registro;
            }
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}