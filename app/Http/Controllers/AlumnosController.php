<?php

namespace App\Http\Controllers;
use App\Alumno;
use App\Alumnodomicilio;
use App\Alumnoabono;
use App\Alumnocargo;
use App\Alumnodescuento;
use App\Alumnodevolucione;
use App\Alumnofiscale;
use App\Altacurso;
use App\Ficha;
use App\Tutore;
use App\Cupone;
use App\Publicitario;
use App\Aspiracione;
use App\Ingreso;
use App\Fichadocumento;
use App\Datosescolare;
use App\Prospecto;
use App\Seguimiento;
use App\Seguimientodescripcione;
use App\Estado;
use App\Municipio;
use App\Tipoescuela;
use App\Escuela;
use App\Universidade;
use App\Carrera;
use App\Centrosuniversitario;
use App\Medioscontacto;
use App\Mediospublicitario;
use App\Viaspublicitaria;
use App\Motivosinscripcione;
use App\Campania;
use App\Motivosbachillerato;
use App\Empresascurso;
use App\Calendario;
use App\Nivele;
use App\Subnivele;
use App\Categoria;
use App\Modalidade;
use App\Curso;
use App\Turno;
use App\Grupo;
use App\Horario;
use App\Sedesucursale;
use App\Sucursale;
use App\Sede;
use App\Metodospago;
use App\Formaspago;
use App\Banco;
use App\Cuenta;
use App\Conceptosabono;
use App\Conceptoscargo;
use App\Conceptosdescuento;
use App\Tipopago;
use App\Usuario;
use App\Empleado;
use App\Empresaconvenio;
use Carbon\Carbon;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
include "funciones/FuncionesGenerales.php";
include "funciones/FuncionesInscripciones.php";
include "funciones/funcionesBaseDatos.php";
include "funciones/telegram.php";

class AlumnosController extends BaseController
{
    function externos(Request $request){
        try {
            $respuesta = array();
            $sucursal = $request['idSucursal'];
            $calendario = $request['idCalendario'];
            $fichas = alumnosExternos($sucursal, $calendario, '');

            $respuesta = array();
            foreach ($fichas as $ficha) {
                $ficha->bg = (floatval($ficha->saldo) > 0 && compararFechas(Carbon::now(), $ficha->fecha)) ? 'bg-rojo' : 'bg-verde';
                $respuesta[] = $ficha;
            }
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function inscritos(Request $request){
        try {
            $alumnos = array();
            $sucursal = $request['idSucursal'];
            $calendario = $request['idCalendario'];
            $fichas = alumnosInternos($sucursal, $calendario, '');

            $respuesta = array();
            foreach ($fichas as $ficha) {
                $ficha->bg = (floatval($ficha->saldo) > 0 && compararFechas(Carbon::now(), $ficha->fecha)) ? 'bg-rojo' : 'bg-verde';
                $alumnos[] = $ficha;
            }

            $calendarios = Calendario::where('eliminado', '=', 0)->get();
            $respuesta['calendarios'] = $calendarios;
            $respuesta['alumnos'] = $alumnos;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function busquedaExternos(Request $request){
        try {
            $busqueda = strtoupper($request['nombre']);
            $sucursal = $request['idSucursal'];
            $calendario = $request['idCalendario'];
            $fichas = alumnosExternos($sucursal, $calendario, $busqueda);

            $respuesta = array();
            foreach ($fichas as $ficha) {
                $ficha->bg = (floatval($ficha->saldo) > 0 && compararFechas(Carbon::now(), $ficha->fecha)) ? 'bg-rojo' : 'bg-verde';
                $respuesta[] = $ficha;
            }
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function busquedaInscritos(Request $request){
        try {
            $alumnos = array();
            $sucursal = $request['idSucursal'];
            $calendario = $request['idCalendario'];
            $busqueda = strtoupper($request['nombre']);
            $fichas = alumnosInternos($sucursal, $calendario, $busqueda);
            
            $respuesta = array();
            foreach ($fichas as $ficha) {
                $ficha->bg = (floatval($ficha->saldo) > 0 && compararFechas(Carbon::now(), $ficha->fecha)) ? 'bg-rojo' : 'bg-verde';
                $respuesta[] = $ficha;
            }
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function ventas(Request $request){
        try {
            $respuesta = array();
            $usuario = $request['idUsuario'];
            $calendario = $request['idCalendario'];
            $fichas = Ficha::select('idAlumno')->
                             where('idUsuario', '=', $usuario)->
                             groupBy('idAlumno')->get();

            foreach ($fichas as $ficha) {
                $alumno = Alumno::find($ficha->idAlumno);
                $alumno->alumno = $alumno->nombre.' '.$alumno->apellidoPaterno.' '.$alumno->apellidoMaterno;
                $adeudos = adeudosAlumno($alumno->id);
                foreach ($adeudos as $adeudo) {
                    if($adeudo['adeudo']){
                        $alumno->bg = 'bg-rojo';
                    }else{
                        $alumno->bg = 'bg-verde';
                    }
                }
                if(count($adeudos) < 1){
                    $alumno->bg = 'bg-verde';
                }
                $respuesta[] = $alumno;
            }
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function busquedaVentas(Request $request){
        try {
            $respuesta = array();
            $usuario = $request['idUsuario'];
            $calendario = $request['idCalendario'];
            $busqueda = $request['nombre'];
            $fichas = Ficha::select('idAlumno')->
                             where('idUsuario', '=', $usuario)->
                             groupBy('idAlumno')->get();
            foreach ($fichas as $ficha) {
                $id = $ficha->idAlumno;
                $consulta = "SELECT * FROM alumnos WHERE id = $id AND (nombre LIKE '%$busqueda%' OR apellidoPaterno LIKE '%$busqueda%' OR apellidoMaterno LIKE '%$busqueda%' OR celular LIKE '%$busqueda%')";
                $estudiante = DB::select($consulta, array());
                if(count($estudiante)){
                    $alumno = $estudiante[0];
                    $alumno->alumno = $alumno->nombre.' '.$alumno->apellidoPaterno.' '.$alumno->apellidoMaterno;
                    $adeudos = adeudosAlumno($alumno->id);
                    foreach ($adeudos as $adeudo) {
                        if($adeudo['adeudo']){
                            $alumno->bg = 'bg-rojo';
                        }else{
                            $alumno->bg = 'bg-verde';
                        }
                    }
                    if(count($adeudos) < 1){
                        $alumno->bg = 'bg-verde';
                    }
                    $respuesta[] = $alumno;    
                }   
            }
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function traerAlumno(Request $request){
        try {
            $hoy = Carbon::now();
            $respuesta = array();
            $alumno = Alumno::find($request['idAlumno']);
            $tutor = Tutore::where('idAlumno', '=', $request['idAlumno'])->get();
            $domicilio = Alumnodomicilio::where('idAlumno', '=', $request['idAlumno'])->get();
            $escolares = Datosescolare::where('idAlumno', '=', $request['idAlumno'])->get();
            $FICHAS = Ficha::join('grupos', 'idGrupo', '=', 'grupos.id')->
                join('altacursos', 'grupos.idAltaCurso', '=', 'altacursos.id')->
                join('niveles', 'altacursos.idNivel', '=', 'niveles.id')->
                join('subniveles', 'altacursos.idSubnivel', '=', 'subniveles.id')->
                join('modalidades', 'altacursos.idModalidad', '=', 'modalidades.id')->
                join('categorias', 'altacursos.idCategoria', '=', 'categorias.id')->
                join('cursos', 'altacursos.idCurso', '=', 'cursos.id')->
                join('turnos', 'grupos.idTurno', '=', 'turnos.id')->
                join('horarios', 'grupos.idHorario', '=', 'horarios.id')->
                leftjoin('cupones', 'cupones.idFicha', '=', 'fichas.id')->
                select(
                    'fichas.folio as folio',
                    'cursos.icono',
                    'cursos.nombre as curso',
                    'niveles.nombre as nivel',
                    'subniveles.nombre as subnivel',
                    'modalidades.nombre as modalidad',
                    'categorias.nombre as categoria',
                    'turnos.nombre as turno',
                    DB::raw('CONCAT(horarios.inicio, " - ", horarios.fin) as horario'),
                    'altacursos.inicio',
                    'altacursos.fin',
                    'altacursos.limitePago',
                    'altacursos.precio',
                    'fichas.id',
                    'fichas.numeroRegistro',
                    DB::raw('IF(fichas.estatus <> 1 AND fichas.estatus <> 0 AND NOW() > altacursos.fin, 0, fichas.estatus) as estatus'),
                    'altacursos.idCalendario',
                    'altacursos.idNivel',
                    'grupos.id as idGrupo',
                    DB::raw("(CASE 
                        WHEN(fichas.estatus = 1) THEN 'bg-verde'
                        WHEN(fichas.estatus = 2) THEN 'bg-amarillo'
                        WHEN(fichas.estatus = 3) THEN 'bg-rojo'
                        WHEN(fichas.estatus <> 1 AND fichas.estatus <> 0 AND NOW() > altacursos.fin) THEN 'bg-blue'
                        END) AS fondo"),
                    DB::raw("(CASE 
                        WHEN(fichas.estatus = 1) THEN 'Activa'
                        WHEN(fichas.estatus = 2) THEN 'Inasistencia'
                        WHEN(fichas.estatus = 3) THEN 'Congelada'
                        WHEN(fichas.estatus <> 1 AND fichas.estatus <> 0 AND NOW() > altacursos.fin) THEN 'Finalizado'
                        END) AS estatusActual"),
                    DB::raw('IF(fichas.estatus = 3, true, false) as congelado'),
                    'cupones.monto as montoCongelado'
                )->where('fichas.idAlumno', '=', $request['idAlumno'])->get();
            $fichas = array();
            foreach ($FICHAS as $ficha) {
                $archivos = Fichadocumento::where('idFicha', '=', $ficha->id)->where('eliminado', '=', 0)->get();
                $ficha->archivos = $archivos;
                $datos = Aspiracione::where('idFicha', '=', $ficha->id)->get();
                $ficha->aspiracion = count($datos) > 0 ? $datos[0] : [];
                $datosPublicitarios = Publicitario::where('idFicha', '=', $ficha->id)->get()[0];
                $ficha->publicitarios = $datosPublicitarios;
                $fichas[] = $ficha;
            }
            $estados = Estado::where('eliminado', '=', 0)->get();
            $municipios = Municipio::where('eliminado', '=', 0)->get();
            $tiposEscuela = Tipoescuela::where('eliminado', '=', 0)->get();
            $escuelas = Escuela::where('eliminado', '=', 0)->get();
            $universidades = Universidade::where('eliminado', '=', 0)->get();
            $centros = Centrosuniversitario::where('eliminado', '=', 0)->get();
            $carreras = Carrera::where('eliminado', '=', 0)->get();
            $mediosContacto = Medioscontacto::where('eliminado', '=', 0)->get();
            $mediosPublicitarios = Mediospublicitario::where('eliminado', '=', 0)->get();
            $viasPublicitarias = Viaspublicitaria::where('eliminado', '=', 0)->get();
            $motivosInscripcion = Motivosinscripcione::where('eliminado', '=', 0)->get();
            $motivosBachillerato = Motivosbachillerato::where('eliminado', '=', 0)->get();
            $campanias = Campania::where('eliminado', '=', 0)->get();
            $empresasCursos = Empresascurso::where('eliminado', '=', 0)->get();
            $consulta = "SELECT * FROM calendarios WHERE fin > NOW() AND eliminado = 0 AND activo = 1";
            $calendarios = DB::select($consulta, array());
            $niveles = Nivele::where('eliminado', '=', 0)->get();
            $subniveles = Subnivele::where('eliminado', '=', 0)->get();
            $categorias = Categoria::where('eliminado', '=', 0)->get();
            $modalidades = Modalidade::where('eliminado', '=', 0)->get();
            $cursos = Curso::where('eliminado', '=', 0)->get();
            $sedes = Sede::where('eliminado', '=', 0)->get();
            $turnos = Turno::where('eliminado', '=', 0)->get();
            $selectHorarios = "SELECT CONCAT(inicio, ' - ', fin) as nombre, id FROM horarios WHERE eliminado = 0";
            $horarios = DB::select($selectHorarios, array());
            $sucursales = Sucursale::where('eliminado', '=', 0)->get();
            $sedessucursales = Sedesucursale::where('eliminado', '=', 0)->get();
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
            $empresasCursos = Empresascurso::where('eliminado', '=', 0)->get();
            $metodosPago = Metodospago::where('eliminado', '=', 0)->get();
            $formasPago = Formaspago::where('eliminado', '=', 0)->get();
            $bancos = Banco::where('eliminado', '=', 0)->get();
            $cuentas = Cuenta::where('eliminado', '=', 0)->get();
            $conceptosAbonos = Conceptosabono::where('eliminado', '=', 0)->get();
            $conceptosCargos = Conceptoscargo::where('eliminado', '=', 0)->get();
            $conceptosDescuentos = Conceptosdescuento::where('eliminado', '=', 0)->get();
            $tiposPago = Tipopago::where('eliminado', '=', 0)->get();

            $listas['estados'] = $estados;
            $listas['municipios'] = $municipios;
            $listas['tipoEscuelas'] = $tiposEscuela;
            $listas['escuelas'] = $escuelas;
            $listas['universidades'] = $universidades;
            $listas['centros'] = $centros;
            $listas['centrosUniversitarios'] = $centros;
            $listas['carreras'] = $carreras;
            $listas['mediosContacto'] = $mediosContacto;
            $listas['mediosPublicitarios'] = $mediosPublicitarios;
            $listas['viasPublicitarias'] = $viasPublicitarias;
            $listas['motivosInscripcion'] = $motivosInscripcion;
            $listas['motivosBachillerato'] = $motivosBachillerato;
            $listas['campanias'] = $campanias;
            $listas['empresasCursos'] = $empresasCursos;
            $listas['calendarios'] = $calendarios;
            $listas['niveles'] = $niveles;
            $listas['subniveles'] = $subniveles;
            $listas['categorias'] = $categorias;
            $listas['modalidades'] = $modalidades;
            $listas['cursos'] = $cursos;
            $listas['sedes'] = $sedes;
            $listas['turnos'] = $turnos;
            $listas['horarios'] = $horarios;
            $listas['sucursales'] = $sucursales;
            $listas['sedesucursales'] = $sedessucursales;
            $listas['grupos'] = $grupos;
            $listas['empresasCursos'] = $empresasCursos;
            $listas['metodosPago'] = $metodosPago;
            $listas['formasPago'] = $formasPago;
            $listas['bancos'] = $bancos;
            $listas['cuentas'] = $cuentas;
            $listas['conceptosAbonos'] = $conceptosAbonos;
            $listas['conceptosCargos'] = $conceptosCargos;
            $listas['conceptosDescuentos'] = $conceptosDescuentos;
            $listas['tiposPago'] = $tiposPago;
            $listas['convenios'] = Empresaconvenio::where('eliminado', '=', 0)->where('activo', '=', 1)->get();


            $respuesta['alumno'] = $alumno;
            $respuesta['tutor'] = (count($tutor) > 0) ? $tutor[0] : null;
            $respuesta['domicilio'] = (count($domicilio) > 0) ? $domicilio[0] : null;
            $respuesta['escolares'] = (count($escolares) > 0) ? $escolares[0] : null;
            $respuesta['fichas'] = $fichas;
            $respuesta['listas'] = $listas;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function actualizarTutor(Request $request){
        try {
            $tutor = Tutore::find($request['id']);
            $tutor->nombre = $request['nombre'];
            $tutor->telefono = $request['telefono'];
            $tutor->celular = $request['celular'];
            $tutor->save();

            return response()->json($tutor, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function actualizarAlumno(Request $request){
        try {
            $alumno = Alumno::find($request['id']);
            $alumno->telefono = $request['telefono'];
            $alumno->celular = $request['celular'];
            $alumno->correo = $request['correo'];
            $alumno->save();

            return response()->json($request, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function actualizarDomicilio(Request $request){
        try {
            $domicilio = Alumnodomicilio::find($request['id']);
            $domicilio->calle = $request['calle'];
            $domicilio->numeroExterior = $request['numeroExterior'];
            $domicilio->numeroInterior = $request['numeroInterior'];
            $domicilio->colonia = $request['colonia'];
            $domicilio->codigoPostal = $request['codigoPostal'];
            $domicilio->idEstado = $request['idEstado'];
            $domicilio->idMunicipio = $request['idMunicipio'];
            $domicilio->save();

            return response()->json($domicilio, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function actualizarEstatusFicha(Request $request){
        try {
            DB::beginTransaction();
            $ficha = Ficha::find($request['id']);
            $ficha->estatus = $request['estatus'];
            $ficha->save();

            if($request['hayCongelado']){
                $todos = Cupone::all();
                $cupon = 'AD'.(count($todos) + 1);
                $cupon = Cupone::create([
                    'monto' => $request['monto'],
                    'idUsuario' => $request['idUsuario'],
                    'cantidad' => 1,
                    'idFicha' => $request['id'],
                    'cupon' => $cupon,
                    'eliminado' => 0,
                    'activo' => 1
                ]);
                
            }
            if(intval($request['estatus']) !== 1){
                if(!desactivarEstadoCuenta($ficha->id)){
                    DB::rollback();
                    return response('Error al congelar estado de cuenta', 400);
                }
            }else if(intval($request['estatus']) === 1){
                if(!activarEstadoCuenta($ficha->id)){
                    DB::rollback();
                    return response('Error al activar estado de cuenta', 400);
                }
            }
            DB::commit();
            if($ficha->estatus === 1){
                //Log
                $mensaje = 'Cambio estatus de ficha '.$ficha->folio.' a Activo';
                agregarLog($mensaje, $request['log']);
            }else if($ficha->estatus === 2){
                //Log
                $mensaje = 'Cambio estatus de ficha '.$ficha->folio.' a Inasistencia';
                agregarLog($mensaje, $request['log']);
            }else if($ficha->estatus === 3){
                //Log
                $mensaje = 'Cambio estatus de ficha '.$ficha->folio.' a Congelado';
                agregarLog($mensaje, $request['log']);
            }
            return response()->json($ficha, 200);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json('Error en el servidor', 400);
        }
    }

    function actualizarDatosEscolares(Request $request){
        try {
            $escolares = Datosescolare::find($request['id']);
            $escolares->idTipoEscuela = $request['idTipoEscuela'];
            $escolares->idSubnivel = $request['idSubnivel'];
            $escolares->idEscuela = $request['idEscuela'];
            $escolares->idEstado = $request['idEstado'];
            $escolares->idMunicipio = $request['idMunicipio'];
            $escolares->promedio = $request['promedio'];
            $escolares->save();

            return response()->json($escolares, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function nuevaInscripcion(Request $request){
        try {
            DB::beginTransaction();
            $usuario = $request['usuario'];
            $usuarioInformacion = $usuario;
            $sucursal = $request['sucursal'];
            $escolares = $request['escolares'];
            $datosInscripcion = $request['inscripcion'];
            $numeroSemana = date("W"); 
            $cuenta = $request['cuenta'];
            $cargos = $cuenta['cargos'];
            $descuentos = $cuenta['descuentos'];
            $abonos = $cuenta['abonos'];
            $fiscales = $request['fiscales'];
            $publicitarios = $request['publicitarios'];
            $alumno = $request['idAlumno'];
            $respuesta = array();
            $seguimiento = null;
            //return response()->json($abonos, 400);

            if(isset($datosInscripcion['idSucursalInscripcion'])){
                $sucursal = $datosInscripcion['idSucursalInscripcion'];
            }

            //VerificarSeguimientos
            $estudiante = Alumno::find($alumno);
            $prospecto = existeProspecto($estudiante->celular, $estudiante->apellidoPaterno, $estudiante->apellidoMaterno, $estudiante->nombre);
            if(count($prospecto) > 0){
                $seguimientos = existeSeguimiento($prospecto[0]->id);
                if(count($seguimientos) > 0){
                    $seguimiento = $seguimientos[0];
                    $usuarioInformacion = $seguimientos[0]->idUsuario;
                }
            }

            $folioFicha = proximoFolioFicha($datosInscripcion['idCalendario'], $datosInscripcion['idNivel'], $sucursal);

            $ficha = Ficha::create([
                'idAlumno' => $alumno,
                'idGrupo' => $datosInscripcion['idGrupo'],
                'semana' => intval($numeroSemana),
                'idSucursalImparticion' => $datosInscripcion['idSucursal'],
                'idSucursalInscripcion' => $sucursal,
                'idCalendario' => $datosInscripcion['idCalendario'],
                'idUsuario' => $usuario,
                'idUsuarioInformacion' => $usuarioInformacion,
                'idNivel' => $datosInscripcion['idNivel'],
                'folio' => $folioFicha,
                'intentos' => (intval($datosInscripcion['idNivel']) === 2) ? 0 : $escolares['intentos'],
                'observaciones' => $datosInscripcion['observaciones'],
                'fecha' => (isset($datosInscripcion['fecha'])) ? $datosInscripcion['fecha'] : Carbon::now(),
                'idTipoPago' => (count($descuentos) > 0) ? $descuentos[0]['tipo'] : 0,
                'activo' => 1,
                'eliminado' => 0
            ]);
            $respuesta['ficha'] = $ficha->id;

            $medio = Viaspublicitaria::find($publicitarios['idViaPublicitaria'])->idMedioPublicitario;
            $publicitario = Publicitario::create([
                'idFicha' => $ficha->id,
                'idMedioContacto' => (!is_null($seguimiento)) ? $seguimiento->idMedioContacto :  $publicitarios['idMedioContacto'],
                'idMedioPublicitario' => $medio,
                'idViaPublicitaria' => $publicitarios['idViaPublicitaria'],
                'idMotivoInscripcion' => $publicitarios['idMotivoInscripcion'],
                'idCampania' => $publicitarios['idCampania'],
                'idMotivoBachillerato' => $publicitarios['idMotivoBachillerato'],
                'idEmpresaCurso' => $publicitarios['idEmpresaCurso'],
                'tomoCurso' => (intval($request['estudio']) === 1) ? 1 : 0,
                'eliminado' => 0,
                'activo' => 1
            ]);

            if(intval($datosInscripcion['idNivel']) === 1){
                $aspiracion = Aspiracione::create([
                    'idFicha' => $ficha->id,
                    'idUniversidad' => $escolares['idUniversidad'],
                    'idCentroUniversitario' => $escolares['idCentroUniveristario'],
                    'idCarrera' => $escolares['idCarrera'],
                    'activo' => 1,
                    'eliminado' => 0
                ]);
            }

            if($fiscales !== null){
                $fiscales = Alumnofiscale::create([
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
            }

            foreach ($cargos as $registro) {
                $cargo = Alumnocargo::create([
                    'idFicha' => $ficha->id,
                    'monto' => $registro['monto'],
                    'concepto' => $registro['concepto'],
                    'idUsuario' => $usuario,
                    'eliminado' => 0,
                    'activo' => 1,
                    'idConcepto' => $registro['idConcepto']
                ]);
            }

            foreach ($descuentos as $desc) {
                $descuento = Alumnodescuento::create([
                    'idFicha' => $ficha->id,
                    'monto' => $desc['monto'],
                    'concepto' => $desc['concepto'],
                    'idUsuario' => $usuario,
                    'tipo' => $desc['tipo'],
                    'cantidad' => $desc['cantidad'],
                    'eliminado' => 0,
                    'activo' => 1,
                    'idConcepto' => $desc['idConcepto'],
                    'idCupon' => $desc['idCupon']
                ]);
            }
            $listaAbonos = array();
            foreach ($abonos as $registro) {
                $folio = proximoFolioIngreso($datosInscripcion['idNivel'], $datosInscripcion['idCalendario'], $sucursal);
                $ingreso = Ingreso::create([
                    'concepto' => $registro['concepto'],
                    'monto' => $registro['monto'],
                    'observaciones' => 'Pago de Inscripcion',
                    'idRubro' => 1,
                    'idTipo' => 1,
                    'idSucursal' => $sucursal,
                    'idCalendario' => $datosInscripcion['idCalendario'],
                    'idFormaPago' => $registro['idFormaPago'],
                    'idMetodoPago' => $registro['idMetodoPago'],
                    'idUsuario' => $usuario,
                    'referencia' => 2,
                    'idNivel' => $datosInscripcion['idNivel'],
                    'folio' => $folio,
                    'imagen' => $registro['imagen'],
                    'idBanco' => $registro['idBanco'],
                    'nombreCuenta' => $registro['nombrePropietario'],
                    'numeroReferencia' => $registro['numeroReferencia'],
                    'idCuenta' => $registro['idCuenta'],
                    'fecha' => (isset($registro['fecha']) && strlen($registro['fecha']) > 0) ? $registro['fecha'] : Carbon::now(),
                    'activo' => 1,
                    'eliminado' => 0
                ]);
                $abono = Alumnoabono::create([
                    'idFicha' => $ficha->id,
                    'idIngreso' => $ingreso->id,
                    'idUsuario' => $usuario,
                    'monto' => $registro['monto'],
                    'concepto' => $registro['concepto'],
                    'idMetodoPago' => $registro['idMetodoPago'],
                    'idFormaPago' => $registro['idFormaPago'],
                    'activo' => 1, 
                    'eliminado' => 0,
                    'idConcepto' => $registro['idConcepto']
                ]);
                $listaAbonos[] = $abono;

                if($registro['IVA']){
                    facturacion($registro, $fiscales);
                }
            }
            $respuesta['abonos'] = $listaAbonos;

            if(!is_null($seguimiento)){
                $modificarSeguimiento = Seguimiento::find($seguimiento->id);
                $modificarSeguimiento->estatus = 2;
                $modificarSeguimiento->idFicha = $ficha->id;
                $modificarSeguimiento->save();

                $seguimiento = Seguimientodescripcione::create([
                    'idUsuario' => $usuario,
                    'idSeguimiento' => $seguimiento->id,
                    'comentario' => 'Alumno Inscrito con la ficha '.Ficha::find($ficha->id)->folio,
                    'fecha' => Carbon::now(),
                    'tipo' => 1,
                    'medio' => 12,
                    'descuento' => 0,
                    'tipoDescuento' => 0,
                    'conceptoDescuento' => 0,
                    'caducidad' => null,
                    'estatusSeguimiento' => 2,
                    'idCita' => 0,
                    'activo' => 1,
                    'eliminado' => 0
                ]);   
            }

            $usuarioInscribio = Usuario::find($ficha->idUsuario);
            $empleadoInscribio = Empleado::find($usuarioInscribio->idEmpleado);
            $usuarioInformo = Usuario::find($ficha->idUsuarioInformacion);
            $empleadoInformo = Empleado::find($usuarioInformo->idEmpleado);
            $subnivel = Subnivele::find($datosInscripcion['idSubnivel']);
            inscripcionTelegram($empleadoInscribio->nombre, $empleadoInformo->nombre, $ficha->folio, $subnivel->nombre);
            DB::commit();
            return response()->json($respuesta, 200);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json('Error en el servidor', 400);
        }
    }

    function nuevoArchivoFicha(Request $request){
        try {
            $archivo = Fichadocumento::create([
                'idFicha' => $request['idFicha'], 
                'nombre' => $request['nombre'],
                'archivo' => $request['archivo'],
                'eliminado' => 0,
                'activo' => 1
            ]);

            return response()->json($archivo, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminarArchivoFicha(Request $request){
        try {
            $archivo = Fichadocumento::find($request['id']);
            $archivo->eliminado = 1;
            $archivo->save();

            return response()->json($archivo, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function busquedaGeneral(Request $request){
        try {
            $respuesta = array();
            $busqueda = $request['busqueda'];
            $sep = explode('|', $request['busqueda']);
            if(count($sep) > 1){
                $id = $sep[1];
                $consulta = "SELECT * FROM alumnos WHERE id = $id";
            }else{
                $consulta = "SELECT * FROM alumnos WHERE CONCAT(nombre, ' ', apellidoPaterno, ' ', apellidoMaterno) LIKE '%$busqueda%' OR celular LIKE '%$busqueda%' OR codigo LIKE '%$busqueda%'";
            }
            
            $estudiantes = DB::select($consulta, array());
            foreach ($estudiantes as $estudiante) {
                $alumno = $estudiante;
                $alumno->alumno = $alumno->nombre.' '.$alumno->apellidoPaterno.' '.$alumno->apellidoMaterno;
                $adeudos = adeudosAlumno($alumno->id);
                foreach ($adeudos as $adeudo) {
                    if($adeudo['adeudo']){
                        $alumno->bg = 'bg-rojo';
                    }else{
                        $alumno->bg = 'bg-verde';
                    }
                }
                if(count($adeudos) < 1){
                    $alumno->bg = 'bg-verde';
                }
                $respuesta[] = $alumno;
            }
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function actualizarNumeroRegistro(Request $request){
        try {
            $ficha = Ficha::find($request['id']);
            $ficha->numeroRegistro = $request['numeroRegistro'];
            $ficha->save();
            return response()->json($ficha, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}