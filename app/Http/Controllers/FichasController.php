<?php

namespace App\Http\Controllers;
use App\Ficha;
use App\Grupo;
use App\Alumno;
use App\Altacurso;
use App\Horario;
use App\Modalidade;
use App\Modalidadescurso;
use App\Curso;
use App\Nivele;
use App\Subnivele;
use App\Alumnocargo;
use App\Alumnoabono;
use App\Alumnodescuento;
use App\Alumnodevolucione;
use App\Alumnoextra;
use App\Metodospago;
use App\Formaspago;
use App\Cuenta;
use App\Banco;
use App\Conceptoscargo;
use App\Conceptosabono;
use App\Conceptosdescuento;
use App\Conceptosextra;
use App\Conceptosdevolucione;
use App\Egreso;
use App\Ingreso;
use App\Sucursale;
use App\Calendario;
use App\Cupone;
use App\Fichacupone;
use App\Alumnofiscale;
use App\Aspiracione;
use App\Tipopago;
use App\Publicitario;
use App\Categoria;
use App\Sede;
use App\Turno;
use Carbon\Carbon;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
include "funciones/FuncionesFichas.php";
include "funciones/FuncionesGenerales.php";
include "funciones/telegram.php";

class FichasController extends BaseController
{
    function traerFicha(Request $request){
        try{
            $hoy = Carbon::now();
            $respuesta = array();
            $ficha = Ficha::find($request['idFicha']);
            $respuesta['folio'] = $ficha->folio;
            $respuesta['estatus'] = $ficha->estatus;
            $respuesta['idTipoPago'] = $ficha->idTipoPago;
            
            $alumno = Alumno::find($ficha->idAlumno);
            $respuesta['alumno'] = $alumno->nombre.' '.$alumno->apellidoPaterno.' '.$alumno->apellidoMaterno;

            $grupo = Grupo::find($ficha->idGrupo);
            $alta = Altacurso::find($grupo->idAltaCurso);
            $alta->idHorario = $grupo->idHorario;
            $alta->idTurno = $grupo->idTurno;
            $alta->idAltaCurso = $alta->id;
            $alta->idGrupo = $grupo->id;
            $alta->idSucursal = $ficha->idSucursalImparticion;
            $alta->observaciones = $ficha->observaciones;
            $alta->fecha = $ficha->fecha;
            $respuesta['alta'] = $alta;

            $niveles = Nivele::where('eliminado', '=', 0)->get();
            $subniveles = Subnivele::where('eliminado', '=', 0)->get();
            $categorias = Categoria::where('eliminado', '=', 0)->get();
            $modalidades = Modalidade::where('eliminado', '=', 0)->get();;
            $cursos = Curso::where('eliminado', '=', 0)->get();
            $sedes = Sede::where('eliminado', '=', 0)->get();;
            $turnos = Turno::where('eliminado', '=', 0)->get();;
            $selectHorarios = "SELECT CONCAT(inicio, ' - ', fin) as nombre, id FROM horarios WHERE eliminado = 0";
            $horarios = DB::select($selectHorarios, array());
            $sucursales = Sucursale::where('eliminado', '=', 0)->get();
            $consulta = "SELECT * FROM calendarios WHERE fin > NOW() AND eliminado = 0 AND activo = 1";
            $calendarios = DB::select($consulta, array());
            $grupos = Grupo::join('altacursos', 'idAltaCurso', '=', 'altacursos.id')->
            join('calendarios', 'altacursos.idCalendario', '=', 'calendarios.id')->
            join('cursos', 'altacursos.idCurso', '=', 'cursos.id')->
            select(
                'grupos.id as id',
                'altacursos.idCalendario',
                'altacursos.idNivel',
                'altacursos.idSubnivel',
                'altacursos.idCategoria',
                'altacursos.idModalidad',
                'altacursos.idSede',
                'altacursos.idCurso',
                'altacursos.inicio',
                'altacursos.fin',
                'altacursos.limitePago',
                'altacursos.precio',
                'cursos.nombre as curso',
                'grupos.idHorario', 
                'grupos.idTurno')->
            where('calendarios.fin', '>', $hoy)->get();
            
            
            $listas['niveles'] = $niveles;
            $listas['subniveles'] = $subniveles;
            $listas['categorias'] = $categorias;
            $listas['modalidades'] = $modalidades;
            $listas['cursos'] = $cursos;
            $listas['turnos'] = $turnos;
            $listas['horarios'] = $horarios;
            $listas['sucursales'] = $sucursales;
            $listas['sedes'] = $sedes;
            $listas['calendarios'] = $calendarios;
            $listas['grupos'] = $grupos;
            $respuesta['listas'] = $listas;


            return response()->json($respuesta, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function catalogos(){
        try {
            $respuesta = array();
            $metodos = Metodospago::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
            $respuesta['metodos'] = $metodos;
            $formas = Formaspago::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
            $respuesta['formas'] = $formas;
            $cuentas = Cuenta::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
            $respuesta['cuentas'] = $cuentas;
            $bancos = Banco::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
            $respuesta['bancos'] = $bancos;
            $tiposPago = Tipopago::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
            $respuesta['tiposPago'] = $tiposPago;

            $cg = Conceptoscargo::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
            $respuesta['cg'] = $cg;
            $ca = Conceptosabono::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
            $respuesta['ca'] = $ca;
            $cd = Conceptosdescuento::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
            $respuesta['cd'] = $cd;
            $ce = Conceptosextra::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
            $respuesta['ce'] = $ce;
            $cs = Conceptosdevolucione::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
            $respuesta['cs'] = $cs;

            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function estadoCuenta(Request $request){
        try {
            $respuesta = array();
            $elementos = array();
            
            $cargos = Alumnocargo::where('idFicha', '=', $request['idFicha'])->
            where('eliminado', '=', 0)->get();
            foreach ($cargos as $cargo) {
                $cargo->existe = true;
                $elementos[] = $cargo;
            }
            $cargos = $elementos;
            $elementos = [];

            $abonos = Alumnoabono::where('idFicha' , '=', $request['idFicha'])->
            where('eliminado', '=', 0)->get();
            foreach ($abonos as $abono) {
                $abono->existe = true;
                $elementos[] = $abono;
            }
            $abonos = $elementos;
            $elementos = [];

            $descuentos = Alumnodescuento::where('idFicha', '=', $request['idFicha'])->
            where('eliminado', '=', 0)->get();
            foreach ($descuentos as $descuento) {
                $descuento->existe = true;
                $elementos[] = $descuento;
            }
            $descuentos = $elementos;
            $elementos = [];

            $devoluciones = Alumnodevolucione::where('idFicha', '=', $request['idFicha'])->
            where('eliminado', '=', 0)->get();
            foreach ($devoluciones as $devolucion) {
                $devolucion->existe = true;
                $elementos[] = $devolucion;
            }

            $extras = Alumnoextra::where('idFicha', '=', $request['idFicha'])->
            where('eliminado', '=', 0)->get();
            foreach ($extras as $extra) {
                $extra->existe = true;
                $elementos[] = $extra;
            }

            $fichaa = Ficha::find($request['idFicha']);

            $respuesta['estatus'] = $fichaa->estatus;
            $respuesta['cargos'] = $cargos;
            $respuesta['abonos'] = $abonos;
            $respuesta['descuentos'] = $descuentos;
            $respuesta['devoluciones'] = $devoluciones;
            $respuesta['extras'] = $extras;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function agregarCargo(Request $request){
        try {
            //return response()->json($request, 400);
            $ficha = Ficha::find($request['idFicha']);
            if(intval($ficha->idSucursalImparticion) !== intval($request['sucursal']) && intval($ficha->idSucursalInscripcion) !== intval($request['sucursal']) && intval($ficha->idUsuario) !== $request['idUsuario']){
                return response()->json('No cuentas con permisos para modificar esta ficha', 400);
            }
            $cargo = Alumnocargo::create([
                'idFicha' => $request['idFicha'],
                'monto' => $request['monto'],
                'concepto' => $request['concepto'],
                'idUsuario' => $request['idUsuario'],
                'eliminado' => 0,
                'activo' => 1,
                'idConcepto' => $request['idConcepto']
            ]);

            return response()->json($cargo, 200);
        } catch (Exception $e) {    
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminarCargo(Request $request){
        try {
            $ficha = Ficha::find($request['idFicha']);
            if(intval($ficha->idSucursalImparticion) !== intval($request['sucursal']) && intval($ficha->idSucursalInscripcion) !== intval($request['sucursal']) && intval($ficha->idUsuario) !== $request['idUsuario']){
                return response()->json('No cuentas con permisos para modificar esta ficha', 400);
            }
            $cargo = Alumnocargo::find($request['id']);
            $cargo->eliminado = 1;
            $cargo->save();

            return response()->json($cargo);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function agregarAbono(Request $request){
        try{
            $abono = $request['pago'];
            $cargos = $request['cargos'];
            $ficha = Ficha::find($request['idFicha']);

            $cantidad = Ingreso::where('idNivel', '=', $ficha->idNivel)->where('idCalendario', '=', $ficha->idCalendario)->where('idSucursal', '=', $request['idSucursal'])->get();
            $sucursal = Sucursale::find($request['idSucursal']);
            $calendario = Calendario::find($ficha->idCalendario);
            $nivel = Nivele::find($ficha->idNivel);
            $separados = explode("-", $calendario->nombre);
            $folio = substr($separados[0], -2).$separados[1].substr($nivel->nombre, 0, 1).$sucursal->abreviatura.'-'.(count($cantidad) + 1);


            foreach ($cargos as $registro) {
                $cargo = Alumnocargo::create([
                    'idFicha' => $ficha->id,
                    'monto' => $registro['monto'],
                    'concepto' => $registro['concepto'],
                    'idUsuario' => $request['idUsuario'],
                    'eliminado' => 0,
                    'activo' => 1,
                    'idConcepto' => $registro['idConcepto']
                ]);
            }

            $ingreso = Ingreso::create([
                'concepto' => $abono['concepto'],
                'monto' => $abono['monto'],
                'observaciones' => 'Abono',
                'idRubro' => 1,
                'idTipo' => 2,
                'idSucursal' => $request['idSucursal'],
                'idCalendario' => $ficha->idCalendario,
                'idFormaPago' => $abono['forma'],
                'idMetodoPago' => $abono['metodo'],
                'idUsuario' => $request['idUsuario'],
                'referencia' => 3,
                'idNivel' => $ficha->idNivel,
                'folio' => $folio,
                'imagen' => $abono['imagen'],
                'idBanco' => $abono['idBanco'],
                'nombreCuenta' => $abono['nombreCuenta'],
                'numeroReferencia' => $abono['numeroReferencia'],
                'idCuenta' => $abono['idCuenta'],
                'fecha' => (isset($abono['fecha']) && strlen($abono['fecha']) > 0) ? $abono['fecha'] : Carbon::now(),
                'activo' => 1,
                'eliminado' => 0,
            ]);

            $pago = Alumnoabono::create([
                'idFicha' => $ficha->id,
                'idIngreso' => $ingreso->id,
                'idUsuario' => $request['idUsuario'],
                'monto' => $abono['monto'],
                'concepto' => $abono['concepto'],
                'idMetodoPago' => $abono['metodo'],
                'idFormaPago' => $abono['forma'],
                'activo' => 1, 
                'eliminado' => 0,
                'idConcepto' => $abono['idConcepto']
            ]);

            if($abono['iva']){
                $fiscales = $request['facturacion'];
                $facturacion = Alumnofiscale::create([
                    'idFicha' => $ficha->id,
                    'razonSocial' => $fiscales['razonSocial'],
                    'RFC' => $fiscales['RFC'],
                    'domicilio' => $fiscales['domicilio'],
                    'colonia' => $fiscales['colonia'],
                    'codigoPostal' => $fiscales['codigoPostal'],
                    'telefono' => $fiscales['telefono'],
                    'usoCFDI' => $fiscales['usoCFDI'],
                    'correo' => $fiscales['correo'],
                    'activo' => 1,
                    'eliminado' => 0
                ]); 
                facturacion($abono, $facturacion);
            }

            return response()->json($pago, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminarAbono(Request $request){
        try {
            $ficha = Ficha::find($request['idFicha']);
            if(intval($ficha->idSucursalImparticion) !== intval($request['sucursal']) && intval($ficha->idSucursalInscripcion) !== intval($request['sucursal']) && intval($ficha->idUsuario) !== $request['idUsuario']){
                return response()->json('No cuentas con permisos para modificar esta ficha', 400);
            }
            $abono = Alumnoabono::find($request['id']);
            $abono->eliminado = 1;
            $abono->save();
            $ingreso = Ingreso::find($abono->idIngreso);
            $ingreso->activo = 0;
            $ingreso->save();

            return response()->json($abono, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function agregarDescuento(Request $request){
        try {
            $ficha = Ficha::find($request['idFicha']);
            if(intval($ficha->idSucursalImparticion) !== intval($request['sucursal']) && 
               intval($ficha->idSucursalInscripcion) !== intval($request['sucursal']) && 
               intval($ficha->idUsuario) !== intval($request['idUsuario'])){
                return response()->json('No cuentas con permisos para modificar esta ficha', 400);
            }
            $descuento = Alumnodescuento::create([
                'idFicha' => $request['idFicha'],
                'monto' => $request['monto'],
                'concepto' => $request['concepto'],
                'idUsuario' => $request['idUsuario'],
                'eliminado' => 0,
                'activo' => 1,
                'idConcepto' => $request['idConcepto'],
                'tipo' => $request['tipo'],
                'cantidad' => 55
            ]);

            return response()->json($descuento, 200);
        } catch (Exception $e) {    
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminarDescuento(Request $request){
        try {
            $ficha = Ficha::find($request['idFicha']);
            if(intval($ficha->idSucursalImparticion) !== intval($request['sucursal']) && intval($ficha->idSucursalInscripcion) !== intval($request['sucursal']) && intval($ficha->idUsuario) !== $request['idUsuario']){
                return response()->json('No cuentas con permisos para modificar esta ficha', 400);
            }
            $descuento = Alumnodescuento::find($request['id']);
            $descuento->eliminado = 1;
            $descuento->save();

            return response()->json($descuento);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function agregarDevolucion(Request $request){
        try {
            $ficha = Ficha::find($request['idFicha']);
            if(intval($ficha->idSucursalImparticion) !== intval($request['sucursal']) && intval($ficha->idSucursalInscripcion) !== intval($request['sucursal']) && intval($ficha->idUsuario) !== $request['idUsuario']){
                return response()->json('No cuentas con permisos para modificar esta ficha', 400);
            }
            $ficha = Ficha::find($request['idFicha']);
            //return response()->json($request, 400);
            $saldoTotalSucursal = saldoTotalSucursal($request['idSucursal']);
            if($request['monto'] > $saldoTotalSucursal && intval($request['idFormaPago']) === 1){
                return response()->json("No cuentas con suficiente saldo para realizar la devolucion", 400);
            }
            $cantidad = Egreso::where('idNivel', '=', $ficha->idNivel)->where('idCalendario', '=', $ficha->idCalendario)->where('idSucursal', '=', $request['idSucursal'])->get();
            $sucursal = Sucursale::find($request['idSucursal']);
            $calendario = Calendario::find($ficha->idCalendario);
            $nivel = Nivele::find($ficha->idNivel);
            $separados = explode("-", $calendario->nombre);
            $folio = substr($separados[0], -2).$separados[1].substr($nivel->nombre, 0, 1).$sucursal->abreviatura.'-'.(count($cantidad) + 1);

            
            $egreso = Egreso:: create([
                'concepto' => $request['concepto'],
                'monto' => $request['monto'],
                'observaciones' => 'Devolucion',
                'idRubro' => 1,
                'idTipo' => 1,
                'idSucursal' => $request['idSucursal'],
                'idCalendario' => $ficha->idCalendario,
                'idFormaPago' => $request['idFormaPago'],
                'idUsuario' => $request['idUsuario'],
                'idNivel' => $ficha->idNivel,
                'referencia' => 2,
                'folio' => $folio,
                'idCuenta' => $request['idCuenta'],
                'eliminado' => 0,
                'activo' => 1
            ]);

            $devolucion = Alumnodevolucione::create([
                'idUsuario' => $request['idUsuario'], 
                'monto' => $request['monto'],
                'idConcepto' => $request['idConcepto'],
                'concepto' => $request['concepto'],
                'idFicha' => $request['idFicha'],
                'idEgreso' => $egreso->id,
                'idFormaPago' => $request['idFormaPago'],
                'eliminado' => 0,
                'activo' => 1
            ]);

            return response()->json($devolucion, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminarDevolucion(Request $request){
        try {
            $ficha = Ficha::find($request['idFicha']);
            if(intval($ficha->idSucursalImparticion) !== intval($request['sucursal']) && intval($ficha->idSucursalInscripcion) !== intval($request['sucursal']) && intval($ficha->idUsuario) !== $request['idUsuario']){
                return response()->json('No cuentas con permisos para modificar esta ficha', 400);
            }
            $devolucion = Alumnodevolucione::find($request['id']);
            $devolucion->eliminado = 1;
            $egreso = Egreso::find($devolucion->idEgreso);
            $egreso->activo = 0;
            $egreso->save();
            $devolucion->save();

            return response()->json($devolucion, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    } 

    function agregarExtra(Request $request){
        try {
            $ficha = Ficha::find($request['idFicha']);
            if(intval($ficha->idSucursalImparticion) !== intval($request['sucursal']) && intval($ficha->idSucursalInscripcion) !== intval($request['sucursal']) && intval($ficha->idUsuario) !== $request['idUsuario']){
                return response()->json('No cuentas con permisos para modificar esta ficha', 400);
            }
            $extra = Alumnoextra::create([
                'idConcepto' => $request['idConcepto'],
                'monto' => $request['monto'],
                'concepto' => $request['concepto'],
                'idUsuario' => $request['idUsuario'],
                'idFicha' => $request['idFicha'],
                'eliminado' => 0,
                'activo' => 1
            ]);

            return response()->json($extra, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminarExtra(Request $request){
        try {
            $ficha = Ficha::find($request['idFicha']);
            if(intval($ficha->idSucursalImparticion) !== intval($request['sucursal']) && intval($ficha->idSucursalInscripcion) !== intval($request['sucursal']) && intval($ficha->idUsuario) !== $request['idUsuario']){
                return response()->json('No cuentas con permisos para modificar esta ficha', 400);
            }
            $extra = Alumnoextra::find($request['id']);
            $extra->eliminado = 1;
            $extra->save();

            return response()->json($extra, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    } 

    function agregarCupon(Request $request){
        try {
            $ficha = Ficha::find($request['idFicha']);
            /*if(intval($ficha->idSucursalImparticion) !== intval($request['sucursal']) && intval($ficha->idSucursalInscripcion) !== intval($request['sucursal']) && intval($ficha->idUsuario) !== $request['idUsuario']){
                return response()->json('No cuentas con permisos para modificar esta ficha', 400);
            }*/
            $total = $request['total'];
            $posible = strtoupper($request['cupon']);
            $cupones = Cupone::where('cupon', '=', $posible)->
                            where('eliminado', '=', 0)->
                            where('activo', '=', 1)->
                            where('cantidad', '>', 0)->get();
            if(count($cupones) > 0){
                $cupon = $cupones[0];
                $anteriormente = Fichacupone::where('idFicha', '=', $request['idFicha'])->where('idCupon', '=', $cupon->id)->get();
                if(count($anteriormente) > 0){
                    return response()->json('Ya has canjeado este cupon anteriormente', 400);
                }
                if(floatval($cupon->monto) > floatval($total)){
                    return response()->json('No se puede canjear el cupon ya que el monto del cupon es mayor a el total de la deuda de el estado de cuenta', 400);
                }
                $descuento = Alumnodescuento::create([
                    'idFicha' => $request['idFicha'],
                    'monto' => $cupon->monto,
                    'concepto' => 'Cupon de descuento '.$cupon->cupon,
                    'idUsuario' => $request['idUsuario'],
                    'eliminado' => 0,
                    'activo' => 1,
                    'idConcepto' => 0,
                    'idCupon' => $cupon->id,
                    'cantidad' => 0,
                    'tipo' => 0,
                    'canitdad' => $cupon->monto
                ]);
                $reducir = Cupone::find($cupon->id);
                $reducir->cantidad = $reducir->cantidad-1;
                $reducir->save();
                $nuevo = Fichacupone::create([
                    'idFicha' => $request['idFicha'],
                    'idCupon' => $cupon->id,
                    'eliminado' => 0,
                    'activo' => 1
                ]);
                return response()->json($descuento, 200);
            }else{
                return response()->json('Cupon no existente', 400);
            }
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificarFicha(Request $request){
        try {
            /*$folioFicha = proximoFolioFicha($datosInscripcion['idCalendario'], $datosInscripcion['idNivel'], $request['idSucursalInscripcion']);*/
            $ficha = Ficha::find($request['idFicha']);
            if(intval($ficha->idSucursalImparticion) !== intval($request['sucursal']) && 
               intval($ficha->idSucursalInscripcion) !== intval($request['sucursal']) && 
               intval($ficha->idUsuario) !== intval($request['idUsuario'])){
                return response()->json('No cuentas con permisos para modificar esta ficha', 400);
            }
            $ficha->idSucursalImparticion = $request['idSucursal'];
            $ficha->observaciones = $request['observaciones'];
            $ficha->idGrupo = $request['idGrupo'];
            $ficha->idCalendario = $request['idCalendario'];
            $ficha->folio = $folioFicha;
            if(isset($request['fecha'])){
                $ficha->fecha = $request['fecha'];
            }
            $ficha->save();

            return response()->json($ficha, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificarDatosAspiracion(Request $request){
        try {
            if(intval($request['id']) === 0){
                $aspiraciones = Aspiracione::create([
                    'idFicha' => $request['idFicha'],
                    'idUniversidad' => $request['idUniversidad'],
                    'idCentroUniversitario' => $request['idCentroUniversitario'],
                    'idCarrera' => $request['idCarrera'],
                    'eliminado' => 0,
                    'activo' => 1
                ]);

                return response()->json($aspiraciones, 200);
            }else{
                $aspiraciones = Aspiracione::find($request['id']);
                $aspiraciones->idUniversidad = $request['idUniversidad'];
                $aspiraciones->idCentroUniversitario = $request['idCentroUniversitario'];
                $aspiraciones->idCarrera = $request['idCarrera'];
                $aspiraciones->save();

                return response()->json($aspiraciones, 200);
            }
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificarTipoPago(Request $request){
        try {
            $ficha = Ficha::find($request['idFicha']);
            $ficha->idTipoPago = $request['idTipoPago'];
            $ficha->save();
            return response()->json($ficha, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificarDatosPublicitarios(Request $request){
        try {
            $datos = Publicitario::find($request['id']);
            $datos->idMedioContacto = $request['idMedioContacto'];
            $datos->idMedioPublicitario = $request['idMedioPublicitario'];
            $datos->idViaPublicitaria = $request['idViaPublicitaria'];
            $datos->idMotivoInscripcion = $request['idMotivoInscripcion'];
            $datos->idMotivoBachillerato = $request['idMotivoBachillerato'];
            $datos->idCampania = $request['idCampania'];
            $datos->tomoCurso = $request['tomoCurso'];
            if(intval($request['tomoCurso']) === 0)
                $datos->idEmpresaCurso = 0;
            else    
                $datos->idEmpresaCurso = $request['idEmpresaCurso'];
            $datos->save();

            return response()->json($datos, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}