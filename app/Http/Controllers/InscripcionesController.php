<?php

namespace App\Http\Controllers;
use App\Alumno;
use App\Alumnodomicilio;
use App\Alumnoabono;
use App\Alumnocargo;
use App\Tutore;
use App\Datosescolare;
use App\Aspiracione;
use App\Ficha;
use App\Ingreso;
use App\Altacurso;
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
use App\Cursosparidade;
use App\Reservacionesaula;
use App\Aula;
use App\Alumnodescuento;
use App\Alumnofiscale;
use App\Centrosuniversitario;
use App\Sede;
use App\Medioscontacto;
use App\Mediospublicitario;
use App\Viaspublicitaria;
use App\Motivosinscripcione;
use App\Campania;
use App\Motivosbachillerato;
use App\Empresascurso;
use App\Publicitario;
use App\Cupone;
use App\Empleado;
use App\Usuario;
use App\Prospecto;
use App\Seguimiento;
use App\Seguimientodescripcione;
use App\Sexo;
use App\Estado;
use App\Municipio;
use App\Tipoescuela;
use App\Escuela;
use App\Universidade;
use App\Carrera;
use App\Metodospago;
use App\Formaspago;
use App\Banco;
use App\Cuenta;
use App\Conceptosabono;
use App\Conceptoscargo;
use App\Conceptosdescuento;
use App\Tipopago;
use App\Bloqueohorario;
use App\Empresaconvenio;
use Carbon\Carbon;
include "funciones/FuncionesInscripciones.php";
include "funciones/FuncionesGrupo.php";
include "funciones/FuncionesGenerales.php";
include "funciones/telegram.php";
include "funciones/funcionesBaseDatos.php";


use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InscripcionesController extends BaseController
{

    function nuevo(Request $request){
        try{
            /*DB::rollback();
            return response()->json($request, 400);*/
            //return response()->json($request, 400);
            DB::beginTransaction();
            $existeIVA = false;
            $usuario = $request['usuario'];
            $usuarioInformacion = $usuario;
            $sucursal = $request['sucursal'];
            $datosAlumno = $request['alumno'];
            $datosDomicilio = $request['domicilio'];
            $datosTutor = $request['tutor'];
            $escolares = $request['escolares'];
            $datosInscripcion = $request['inscripcion'];
            $numeroSemana = date("W"); 
            $cuenta = $request['cuenta'];
            $cargos = $cuenta['cargos'];
            $descuentos = $cuenta['descuentos'];
            $abonos = $cuenta['abonos'];
            $fiscales = $request['fiscales'];
            $publicitarios = $request['publicitarios'];
            $respuesta = array();
            $seguimiento = null;
            

            if(isset($datosInscripcion['idSucursalInscripcion']) && intval($datosInscripcion['idSucursalInscripcion']) > 0){
                $sucursal = $datosInscripcion['idSucursalInscripcion'];
            }
            $folioFicha = proximoFolioFicha($datosInscripcion['idCalendario'], $datosInscripcion['idNivel'], $sucursal);

            $codigoAlumno = substr($datosAlumno['nombre'], 0, 2).substr($datosAlumno['apellidoPaterno'],0 ,2).substr($datosAlumno['apellidoMaterno'],0 ,2).$datosAlumno['fechaNacimiento'];
            $codigoAlumno = str_replace('-', '', $codigoAlumno);
            $codigoAlumno = strtoupper($codigoAlumno);
            $existe = Alumno::where('codigo', 'LIKE', '%'.$codigoAlumno."%")->get();
            $cantidadAlumnos = count($existe);

            //VerificarSeguimientos
            $prospecto = existeProspecto($datosAlumno['celular'], $datosAlumno['apellidoPaterno'], $datosAlumno['apellidoMaterno'], $datosAlumno['nombre']);
            if(count($prospecto) > 0){
                $seguimientos = existeSeguimiento($prospecto[0]->id);
                if(count($seguimientos) > 0){
                    $seguimiento = $seguimientos[0];
                    $usuarioInformacion = $seguimientos[0]->idUsuario;
                }
            }else if(isset($request['idSeguimiento'])){
                $seguimiento = Seguimiento::find($request['idSeguimiento']);
                $usuarioInformacion = $seguimiento->idUsuario;
            }
            
            $homoclave = ($cantidadAlumnos > 9) ? $cantidadAlumnos+1 : '0'.($cantidadAlumnos+1);
            //
            $alumno = Alumno::create([
                'nombre' => $datosAlumno['nombre'],
                'apellidoPaterno' => $datosAlumno['apellidoPaterno'],
                'apellidoMaterno' => $datosAlumno['apellidoMaterno'],
                'telefono' => $datosAlumno['telefono'],
                'celular' => $datosAlumno['celular'],
                'correo' => $datosAlumno['correo'],
                'idSexo' => $datosAlumno['idSexo'],
                'fechaNacimiento' => $datosAlumno['fechaNacimiento'],
                'codigo' => $codigoAlumno.'LUM'.$homoclave,
                'activo' => 1,
                'eliminado' => 0
            ]);
            $respuesta['alumno'] = $alumno->id;

            if(strlen($datosDomicilio['calle']) > 0){
                $domicilio = Alumnodomicilio::create([
                    'idAlumno' => $alumno->id,
                    'calle' => $datosDomicilio['calle'],
                    'numeroExterior' => $datosDomicilio['numeroExterior'],
                    'numeroInterior' => $datosDomicilio['numeroInterior'],
                    'colonia' => $datosDomicilio['colonia'],
                    'codigoPostal' => $datosDomicilio['codigoPostal'],
                    'idEstado' => $datosDomicilio['idEstado'],
                    'idMunicipio' => $datosDomicilio['idMunicipio'],
                    'activo' => 1,
                    'eliminado' => 0
                ]);
            }

            $tutor = Tutore::create([
                'idAlumno' => $alumno->id,
                'nombre' => $datosTutor['nombre'],
                'telefono' => $datosTutor['telefono'],
                'celular' => $datosTutor['celular'],
                'activo' => 1,
                'eliminado' => 0
            ]);

            $ficha = Ficha::create([
                'idAlumno' => $alumno->id,
                'idGrupo' => $datosInscripcion['idGrupo'],
                'semana' => intval($numeroSemana),
                'idSucursalImparticion' => $datosInscripcion['idSucursal'],
                'idSucursalInscripcion' => $sucursal,
                'idCalendario' => $datosInscripcion['idCalendario'],
                'idUsuario' => $usuario,
                'idUsuarioInformacion' => $usuarioInformacion,
                'idTipoPago' => (intval($datosInscripcion['idNivel']) === 2) ? $cuenta['tipoPago'] : 0,
                'idNivel' => $datosInscripcion['idNivel'],
                'folio' => $folioFicha,
                'intentos' => $escolares['intentos'],
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
                'tomoCurso' => $publicitarios['estudio'],
                'eliminado' => 0,
                'activo' => 1
            ]);
            if(intval($datosInscripcion['idNivel']) !== 2){
                $aspiracion = Aspiracione::create([
                    'idFicha' => $ficha->id,
                    'idUniversidad' => $escolares['idUniversidad'],
                    'idCentroUniversitario' => $escolares['idCentroUniveristario'],
                    'idCarrera' => $escolares['idCarrera'],
                    'activo' => 1,
                    'eliminado' => 0
                ]);
            }

            $centro = Grupo::find($datosInscripcion['idGrupo']);
            $alta = Altacurso::find($centro->idAltaCurso);
            $datosEscolares = Datosescolare::create([
                'idAlumno' => $alumno->id,
                'idTipoEscuela' => $escolares['idTipoEscuela'],
                'idEscuela' => $escolares['idEscuela'],
                'idEstado' => $escolares['idEstado'],
                'idMunicipio' => $escolares['idMunicipio'],
                'promedio' => $escolares['promedio'],
                'idSubnivel' => $alta->idSubnivel,
                'activo' => 1,
                'eliminado' => 0
            ]);
            
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
                    'eliminado' => 0,
                    'activo' => 1,
                    'idConcepto' => $desc['idConcepto'],
                    'idCupon' => $desc['idCupon'],
                    'tipo' => $desc['tipo'],
                    'cantidad' => $desc['cantidad']
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
                    /*DB::rollback();
                    return response()->json($registro, 400);*/
                    //facturacion($registro, $fiscales);
                    $existeIVA = true;
                }
            }
            $respuesta['abonos'] = $listaAbonos;

            if($existeIVA){
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
                $mensaje = "Finalizo seguimiento desde inscripcion con ficha ".$ficha->folio;
                agregarLog($mensaje, $request['log']);   
            }

            $usuarioInscribio = Usuario::find($ficha->idUsuario);
            $empleadoInscribio = Empleado::find($usuarioInscribio->idEmpleado);
            $usuarioInformo = Usuario::find($ficha->idUsuarioInformacion);
            $empleadoInformo = Empleado::find($usuarioInformo->idEmpleado);
            $subnivel = Subnivele::find($datosInscripcion['idSubnivel']);
            inscripcionTelegram($empleadoInscribio->nombre, $empleadoInformo->nombre, $ficha->folio, $subnivel->nombre);
            DB::commit();
            //Log
            $mensaje = "Dio de alta ficha ".$ficha->folio;
            agregarLog($mensaje, $request['log']);
            return response()->json($respuesta, 200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json('Error de servidor', 400);
        }
    }

    function existeAlumno(Request $request){
        try {
            $codigoAlumno = substr($request['nombre'], 0, 2).substr($request['apellidoPaterno'],0 ,2).substr($request['apellidoMaterno'],0 ,2).$request['fechaNacimiento'];
            $codigoAlumno = str_replace('-', '', $codigoAlumno);
            $codigoAlumno = strtoupper($codigoAlumno);
            $consulta = "SELECT * FROM alumnos WHERE codigo LIKE '%$codigoAlumno%' AND eliminado = 0";
            $existe = DB::select($consulta, array());
            $resultado = array();
            $respuesta = array();
            foreach ($existe as $alumno) {
                $resultado['id'] = $alumno->id;
                $resultado['nombre'] = $alumno->nombre.' '.$alumno->apellidoPaterno.' '.$alumno->apellidoMaterno;
                $respuesta[] = $resultado;
            }
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function mostrar(Request $request){
        try{
            $hoy = Carbon::now();
            $fichas = Ficha::leftjoin('grupos', 'idGrupo', '=', 'grupos.id')->
            leftjoin('altacursos', 'grupos.idAltaCurso', '=', 'altacursos.id')->
            leftjoin('calendarios', 'altacursos.idCalendario', '=', 'calendarios.id')->
            leftjoin('niveles', 'altacursos.idNivel', '=', 'niveles.id')->
            leftjoin('subniveles', 'altacursos.idSubnivel', '=', 'subniveles.id')->
            leftjoin('cursos', 'altacursos.idCurso', '=', 'cursos.id')->
            leftjoin('categorias', 'altacursos.idCategoria', '=', 'categorias.id')->
            leftjoin('usuarios', 'idUsuario', '=', 'usuarios.id')->
            leftJoin('alumnos', 'fichas.idAlumno', '=', 'alumnos.id')->
            leftjoin('empleados', 'usuarios.idEmpleado', '=', 'empleados.id')->
            select(
                'fichas.id', 
                'fichas.id as ficha',
                'fichas.folio', 
                'calendarios.nombre as calendario', 
                'niveles.nombre as nivel',
                'subniveles.nombre as subnivel',
                'cursos.nombre as curso',
                'categorias.nombre as categoria',
                DB::raw("CONCAT(alumnos.nombre, ' ', alumnos.apellidoPaterno, ' ', alumnos.apellidoMaterno) as alumno"),
                DB::raw("(CASE 
                    WHEN(empleados.idDepartamento = 6) THEN 'bg-verde'
                    END) AS bg"),
                DB::raw("(CASE 
                    WHEN(empleados.idDepartamento = 6) THEN empleados.nombre
                    END) AS title")
               )->
            where('fichas.idCalendario', '=', $request['idCalendario'])->
            where('fichas.idSucursalInscripcion', '=', $request['idSucursal'])->get();
            $respuesta['fichas'] = $fichas;

            $sexos = Sexo::where('eliminado', '=', 0)->get();
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
            $estados = Estado::where('eliminado', '=', 0)->get();
            $municipios = Municipio::where('eliminado', '=', 0)->get();
            $tipoEscuelas = Tipoescuela::where('eliminado', '=', 0)->get();
            $escuelas = Escuela::where('eliminado', '=', 0)->get();
            $universidades = Universidade::where('eliminado', '=', 0)->get();
            $centrosUniversitarios = Centrosuniversitario::where('eliminado', '=', 0)->get();
            $carreras = Carrera::join('calendarios', 'idCalendario', '=', 'calendarios.id')->
            select('carreras.*')->
            where('calendarios.fin', '>', $hoy)->
            where('carreras.eliminado', '=', 0)->get();
            $mediosContacto = Medioscontacto::where('eliminado', '=', 0)->get();
            $mediosPublicitarios = Mediospublicitario::where('eliminado', '=', 0)->get();
            $viasPublicitarias = Viaspublicitaria::where('eliminado', '=', 0)->get();
            $motivosInscripcion = Motivosinscripcione::where('eliminado', '=', 0)->get();
            $motivosBachillerato = Motivosbachillerato::where('eliminado', '=', 0)->get();
            $campanias = Campania::where('eliminado', '=', 0)->get();
            $empresasCursos = Empresascurso::where('eliminado', '=', 0)->get();
            $metodosPago = Metodospago::where('eliminado', '=', 0)->get();
            $formasPago = Formaspago::where('eliminado', '=', 0)->get();
            $bancos = Banco::where('eliminado', '=', 0)->get();
            $cuentas = Cuenta::where('eliminado', '=', 0)->get();
            $conceptosAbonos = Conceptosabono::where('eliminado', '=', 0)->get();
            $conceptosCargos = Conceptoscargo::where('eliminado', '=', 0)->get();
            $conceptosDescuentos = Conceptosdescuento::where('eliminado', '=', 0)->get();
            $tiposPago = Tipopago::where('eliminado', '=', 0)->get();

                        
            $listas['sexos'] = $sexos;
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
            $listas['estados'] = $estados;
            $listas['municipios'] = $municipios;
            $listas['escuelas'] = $escuelas;
            $listas['tipoEscuelas'] = $tipoEscuelas;
            $listas['universidades'] = $universidades;
            $listas['centrosUniversitarios'] = $centrosUniversitarios;
            $listas['carreras'] = $carreras;
            $listas['mediosContacto'] = $mediosContacto;
            $listas['mediosPublicitarios'] = $mediosPublicitarios;
            $listas['viasPublicitarias'] = $viasPublicitarias;
            $listas['motivosInscripcion'] = $motivosInscripcion;
            $listas['motivosBachillerato'] = $motivosBachillerato;
            $listas['campanias'] = $campanias;
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

            $respuesta['listas'] = $listas;
            return response()->json($respuesta, 200);
        }catch(Exception $e){
            return response()->json('Error de servidor', 400);
        }
    }

    function cupo(Request $request){
        try {
            $paridad = Cursosparidade::where('idCurso', '=', $request['idCurso'])->get();
            $cupo = 0;
            $inscritos = 0;
            $lugares = 0;
            $reservaciones = Reservacionesaula::where('idGrupo', '=', $request['idGrupo'])->
                                                where('idSucursal', '=', $request['idSucursal'])->get();
            if(count($reservaciones) < 1){
                return response()->json('No se ha reservado un aula para este grupo', 400);
            }
            
            foreach ($reservaciones as $reservacion) {
                $aula = Aula::find($reservacion->idAula);
                $cupo = intval($cupo) + intval($aula->cupo);
            }

            $paridadesGrupo = traerGruposParidad($request['idGrupo']);
            foreach ($paridadesGrupo as $grupo) {
                $fichas = Ficha::where('idSucursalImparticion', '=', $request['idSucursal'])->
                                 where('idGrupo', '=', $grupo)->
                                 where('estatus', '=', 1)->get();
                $inscritos = intval($inscritos) + intval(count($fichas));
            }
            $respuesta['cupo'] = $cupo;
            $respuesta['inscritos'] = $inscritos;
            $respuesta['lugares'] = (intval($cupo) - intval($inscritos));
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function horarioBloqueado(Request $request){
        try {
            $bloqueo = Bloqueohorario::where('idGrupo', '=', $request['idGrupo'])->where('idSucursal', '=', $request['idSucursal'])->get();
            if(count($bloqueo) > 0){
                return response()->json('Horario bloqueado', 400);
            }else{
                return response()->json($bloqueo, 200);
            }
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function canjearCupon(Request $request){
        try {
            $total = $request['total'];
            $posible = strtoupper($request['cupon']);
            $cupones = Cupone::where('cupon', '=', $posible)->
                            where('eliminado', '=', 0)->
                            where('activo', '=', 1)->
                            where('cantidad', '>', 0)->get();
            if(count($cupones) > 0){
                $descuento = Cupone::find($cupones[0]->id);
                $descuento->cantidad = intval($descuento->cantidad) - 1;
                $descuento->save();
                return response()->json($cupones[0], 200);
            }else{
                return response()->json('Cupon agotado', 400);
            }
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    //Listas Componentes

    function listasInscripcion(){
        try {
            $hoy = Carbon::now();
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
                        'grupos.idHorario', 
                        'grupos.idTurno')->
                      where('calendarios.fin', '>', $hoy)->get();
                        
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
            return response()->json($listas, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function listasConceptos(){
        $metodosPago = Metodospago::where('eliminado', '=', 0)->get();
        $formasPago = Formaspago::where('eliminado', '=', 0)->get();
        $bancos = Banco::where('eliminado', '=', 0)->get();
        $cuentas = Cuenta::where('eliminado', '=', 0)->get();
        $conceptosAbonos = Conceptosabono::where('eliminado', '=', 0)->get();
        $conceptosCargos = Conceptoscargo::where('eliminado', '=', 0)->get();
        $conceptosDescuentos = Conceptosdescuento::where('eliminado', '=', 0)->get();
        $tiposPago = Tipopago::where('eliminado', '=', 0)->get();

        $listas['metodosPago'] = $metodosPago;
        $listas['formasPago'] = $formasPago;
        $listas['bancos'] = $bancos;
        $listas['cuentas'] = $cuentas;
        $listas['conceptosAbonos'] = $conceptosAbonos;
        $listas['conceptosCargos'] = $conceptosCargos;
        $listas['conceptosDescuentos'] = $conceptosDescuentos;
        $listas['tiposPago'] = $tiposPago;

        return response()->json($listas, 200);
    }

    function listasComponenteAlumno(){
        try {
            $sexos = Sexo::where('eliminado', '=', 0)->get();
            $listas['sexos'] = $sexos;
            return response()->json($listas, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function listasComponenteDomicilio(){
        try {
            $estados = Estado::where('eliminado', '=', 0)->get();
            $municipios = Municipio::where('eliminado', '=', 0)->get();

            $listas['estados'] = $estados;
            $listas['municipios'] = $municipios;
            return response()->json($listas, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function listasComponenteEscolares(){
        try {
            $hoy = Carbon::now();
            $tipoEscuelas = Tipoescuela::where('eliminado', '=', 0)->get();
            $escuelas = Escuela::where('eliminado', '=', 0)->get();
            $universidades = Universidade::where('eliminado', '=', 0)->get();
            $centrosUniversitarios = Centrosuniversitario::where('eliminado', '=', 0)->get();
            $carreras = Carrera::join('calendarios', 'idCalendario', '=', 'calendarios.id')->
            select('carreras.*')->
            where('calendarios.fin', '>', $hoy)->
            where('carreras.eliminado', '=', 0)->get();
            $estados = Estado::where('eliminado', '=', 0)->get();
            $municipios = Municipio::where('eliminado', '=', 0)->get();

            $listas['tipoEscuelas'] = $tipoEscuelas;
            $listas['escuelas'] = $escuelas;
            $listas['universidades'] = $universidades;
            $listas['centrosUniversitarios'] = $centrosUniversitarios;
            $listas['carreras'] = $carreras;
            $listas['estados'] = $estados;
            $listas['municipios'] = $municipios;
            return response()->json($listas, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function listasComponentePublicitarios(){
        try {
            $mediosContacto = Medioscontacto::where('eliminado', '=', 0)->get();
            $mediosPublicitarios = Mediospublicitario::where('eliminado', '=', 0)->get();
            $viasPublicitarias = Viaspublicitaria::where('eliminado', '=', 0)->get();
            $motivosInscripcion = Motivosinscripcione::where('eliminado', '=', 0)->get();
            $motivosBachillerato = Motivosbachillerato::where('eliminado', '=', 0)->get();
            $campanias = Campania::where('eliminado', '=', 0)->get();
            $empresasCursos = Empresascurso::where('eliminado', '=', 0)->get();

            $listas['mediosContacto'] = $mediosContacto;
            $listas['mediosPublicitarios'] = $mediosPublicitarios;
            $listas['viasPublicitarias'] = $viasPublicitarias;
            $listas['motivosInscripcion'] = $motivosInscripcion;
            $listas['motivosBachillerato'] = $motivosBachillerato;
            $listas['campanias'] = $campanias;
            $listas['empresasCursos'] = $empresasCursos;
            return response()->json($listas, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}