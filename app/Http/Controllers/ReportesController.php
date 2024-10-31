<?php

namespace App\Http\Controllers;
use App\Calendario;
use App\Ficha;
use App\Usuario;
use App\Empleado;
use App\Nivele;
use App\Subnivele;
use App\Categoria;
use App\Grupo;
use App\Altacurso;
use App\Modalidade;
use App\Curso;
use App\Horario;
use App\Sucursale;
use App\Alumno;
use App\Sexo;
use App\Alumnodomicilio;
use App\Municipio;
use App\Estado;
use App\Tutore;
use App\Datosescolare;
use App\Tipoescuela;
use App\Escuela;
use App\Aspiracione;
use App\Centrosuniversitario;
use App\Carrera;
use App\Publicitario;
use App\Empresascurso;
use App\Campania;
use App\Mediospublicitario;
use App\Viaspublicitaria;
use App\Medioscontacto;
use App\Motivosinscripcione;
use App\Motivosbachillerato;
use App\Alumnodescuento;
use App\Alumnoabono;
use App\Ingreso;
use App\Egreso;
use App\Rubrosegreso;
use App\Nominaegreso;
use App\Nomina;
use App\Departamento;
use App\Metasme;
use App\Metascategoria;
use App\Metascurso;
use App\Prospecto;
use Carbon\Carbon;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
include "funciones/FuncionesGenerales.php";
include "funciones/funcionesBaseDatos.php";

class ReportesController extends BaseController
{
    function reporteVentas(Request $request){
        try {
            $fichas = Ficha::join('sucursales', 'fichas.idSucursalInscripcion', '=', 'sucursales.id')->
            join('usuarios', 'fichas.idUsuarioInformacion', '=', 'usuarios.id')->
            join('empleados', 'usuarios.idEmpleado', '=', 'empleados.id')->
            join('grupos', 'fichas.idGrupo', '=', 'grupos.id')->
            join('altacursos', 'grupos.idAltaCurso', '=', 'altacursos.id')->
            join('categorias', 'altacursos.idCategoria', '=', 'categorias.id')->
            join('modalidades', 'altacursos.idModalidad', '=', 'modalidades.id')->
            join('cursos', 'altacursos.idCurso', '=', 'cursos.id')->
            join('horarios', 'grupos.idHorario', '=', 'horarios.id')->
            join('alumnos', 'fichas.idAlumno', '=', 'alumnos.id')->
            join('tutores', 'alumnos.id', '=', 'tutores.idAlumno')->
            join('publicitarios', 'fichas.id', '=', 'publicitarios.idFicha')->
            join('mediospublicitarios', 'publicitarios.idMedioPublicitario', '=', 'mediospublicitarios.id')->
            join('viaspublicitarias', 'publicitarios.idViaPublicitaria', '=', 'viaspublicitarias.id')->
            join('medioscontactos', 'publicitarios.idMedioContacto', '=', 'medioscontactos.id')->
            select([
                'fichas.folio',
                'fichas.semana',
                'sucursales.nombre',
                'fichas.created_at',
                DB::raw('ELT(MONTH(fichas.fecha), "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre")'),
                'empleados.nombre as lleno',
                'categorias.nombre as categoria',
                'modalidades.nombre as modalidad',
                'cursos.nombre as curso',
                DB::raw('CONCAT(horarios.inicio, "-", horarios.fin) as horario'),
                DB::raw("(SELECT nombre FROM sucursales WHERE id = fichas.idSucursalImparticion LIMIT 1) as imparticion"),
                DB::raw("CONCAT(alumnos.apellidoPaterno, ' ', alumnos.apellidoMaterno) as apellidos"),
                'alumnos.nombre as alumno',
                'alumnos.celular as celularAlumno',
                'alumnos.correo as emailAlumno',
                'tutores.nombre as tutor', 
                'tutores.celular as celularTutor',
                'mediospublicitarios.nombre as medio',
                'viaspublicitarias.nombre as via',
                'medioscontactos.nombre as medioContacto',
                DB::raw("CASE 
                    WHEN fichas.estatus = 1 THEN 'Activo'
                    WHEN fichas.estatus = 2 THEN 'Inasistencia'
                    WHEN fichas.estatus = 3 THEN 'Congelado'
                END as estatus"),
                'altacursos.precio',
                /*DB::raw("IF((SELECT SUM(monto) FROM alumnodescuentos WHERE idFicha = fichas.id LIMIT 1) IS NULL, 0, (SELECT SUM(monto) FROM alumnodescuentos WHERE idFicha = fichas.id AND eliminado = 0 LIMIT 1)) as descuento"),*/
                DB::raw("(SELECT SUM(cantidad) FROM alumnodescuentos WHERE idFicha = fichas.id AND eliminado = 0 AND tipo = 2 LIMIT 1) as descuentoCantidad"),
                DB::raw("altacursos.precio - (SELECT SUM(monto) FROM alumnodescuentos WHERE idFicha = fichas.id LIMIT 1)"),
                DB::raw("(
                    IF((SELECT SUM(monto) FROM alumnocargos WHERE idFicha = fichas.id AND eliminado = 0 LIMIT 1) IS NULL, 0, (SELECT SUM(monto) FROM alumnocargos where idFicha = fichas.id AND eliminado = 0 LIMIT 1)) -
                    IF((SELECT SUM(monto) FROM alumnoabonos WHERE idFicha = fichas.id AND eliminado = 0 LIMIT 1) IS NULL, 0, (SELECT SUM(monto) FROM alumnoabonos WHERE idFicha = fichas.id AND eliminado = 0 LIMIT 1)) -
                    IF((SELECT SUM(monto) FROM alumnodescuentos WHERE idFicha = fichas.id AND eliminado = 0 LIMIT 1) IS NULL, 0, (SELECT SUM(monto) FROM alumnodescuentos WHERE idFicha = fichas.id AND eliminado = 0 LIMIT 1)) +
                    IF((SELECT SUM(monto) FROM alumnodevoluciones WHERE idFicha = fichas.id AND eliminado = 0 LIMIT 1) IS NULL, 0, (SELECT SUM(monto) FROM alumnodevoluciones WHERE idFicha = fichas.id AND eliminado = 0 LIMIT 1)) -
                    IF((SELECT SUM(monto) FROM alumnoextras WHERE idFicha = fichas.id AND eliminado = 0 LIMIT 1) IS NULL, 0, (SELECT SUM(monto) FROM alumnoextras WHERE idFicha = fichas.id AND eliminado = 0 LIMIT 1))
                ) as final")
            ])->where('fichas.idCalendario', '=', $request['idCalendario'])->
            where('fichas.idNivel', '=', $request['idNivel'])->
            get();
            return response()->json($fichas, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function reporteInscritos(Request $request){
        try {
            $calendario = Calendario::find($request['idCalendario']);
            $respuesta = array();
            $resultados = array();
            $fichas = Ficha::join('sucursales', 'idSucursalInscripcion', '=', 'sucursales.id')->
                             select('fichas.*', 'sucursales.nombre as plantel')->
                             where('fichas.idCalendario', '=', $request['idCalendario'])->
                             where('fichas.idNivel', '=', $request['idNivel'])->
                             where('fichas.idSucursalInscripcion', '=', $request['sucursal'])->
                             get();
            foreach ($fichas as $ficha) {
                $usuario = Usuario::find($ficha->idUsuario);
                $empleado = Empleado::find($usuario->idEmpleado);
                $grupo = Grupo::find($ficha->idGrupo);
                $alta = Altacurso::find($grupo->idAltaCurso);
                $horario = Horario::find($grupo->idHorario);
                $alumno = Alumno::find($ficha->idAlumno);

                $domicilioPosible = Alumnodomicilio::where('idAlumno', '=', $alumno->id)->get();
                $domicilio = (count($domicilioPosible) > 0) ? $domicilioPosible[0] : null;

                $tutorPosible = Tutore::where('idAlumno', '=', $alumno->id)->get();
                $tutor = (count($tutorPosible) > 0) ? $tutorPosible[0] : null;

                $escuelaPosible = Datosescolare::where('idAlumno', '=', $alumno->id)->get();
                $escuela = (count($escuelaPosible) > 0) ? $escuelaPosible[0] : null;

                $aspiracionPosible = Aspiracione::where('idFicha', '=', $ficha->id)->get();
                $aspiracion = (count($aspiracionPosible) > 0) ? $aspiracionPosible[0] : null;

                $carrera = (is_null($aspiracion)) ? null : Carrera::find($aspiracion->idCarrera);

                $publicitariosPosibles = Publicitario::where('idFicha', '=', $ficha->id)->get();
                $publicitarios = (count($publicitariosPosibles) > 0) ? $publicitariosPosibles[0] : null;

                if(intval($alta->idSubnivel) === 1){
                    $motivo = (is_null($publicitarios)) ? null : Motivosinscripcione::find($publicitarios->idMotivoInscripcion);
                    $razon = (is_null($motivo)) ? 'Desconocido' : $motivo->nombre;
                }else{
                    $motivo = Motivosbachillerato::find($publicitarios->idMotivoBachillerato);
                    $razon = (is_null($motivo)) ? 'Desconocido' : $motivo->nombre;
                }
                $descuentos = Alumnodescuento::where('idFicha', '=', $ficha->id)->where('eliminado', '=', 0)->get();
                $abonos = Alumnoabono::where('idFicha', '=', $ficha->id)->get();

                $fechaNacimiento = Carbon::createFromDate($alumno->fechaNacimiento);
                $hoy = Carbon::now();
                $edad = $fechaNacimiento->diffInYears($hoy);
                $resultado = array();

                $resultado['No.'] = $ficha->id;
                $resultado['folio'] = $ficha->folio;
                $resultado['semana'] = $ficha->semana;
                $resultado['plantelInscripcion'] = $ficha->plantel;
                $resultado['fechaInscripcion'] = $ficha->created_at;
                $resultado['mes'] = mes($ficha->created_at);
                $resultado['usuarioLleno'] = $empleado->nombre;
                $resultado['capturadoPor'] = Categoria::find($alta->idCategoria)->nombre;
                $resultado['calendario'] = (is_null($calendario->nombre)) ? '' : $calendario->nombre;
                $resultado['nivel'] = Nivele::find($ficha->idNivel)->nombre;
                $resultado['modalidad'] = Modalidade::find($alta->idModalidad)->nombre;
                $resultado['curso'] = Curso::find($alta->idCurso)->nombre;
                $resultado['fechaInicio'] = $alta->inicio;
                $resultado['fechaFin'] = $alta->fin;
                $resultado['horario'] = $horario->inicio.' '.$horario->fin;
                $resultado['plantelImparticion'] = Sucursale::find($ficha->idSucursalImparticion)->nombre;
                $resultado['apellidos'] = $alumno->apellidoPaterno.' '.$alumno->apellidoMaterno;
                $resultado['nombre'] = $alumno->nombre;
                $resultado['sexo'] = Sexo::find($alumno->idSexo)->nombre;
                $resultado['fechaNacimiento'] = $alumno->fechaNacimiento;
                $resultado['edad'] = $edad;
                $resultado['calle'] = (is_null($domicilio)) ? '' : $domicilio->calle;
                $resultado['numero'] = (is_null($domicilio)) ? '' :  $domicilio->numeroExterior;
                $resultado['colonia'] = (is_null($domicilio)) ? '' :  $domicilio->colonia;
                $resultado['CP'] = (is_null($domicilio)) ? '' :  $domicilio->codigoPostal;
                $resultado['ciudad'] = (is_null($domicilio)) ? '' :  Municipio::find($domicilio->idMunicipio)->nombre;
                $resultado['estado'] = (is_null($domicilio)) ? '' :  Estado::find($domicilio->idEstado)->nombre;
                $resultado['otro'] = '';
                $resultado['telefono'] = $alumno->telefono;
                $resultado['celular'] = $alumno->celular;
                $resultado['email'] = $alumno->correo;
                $resultado['nombreTutor'] = $tutor->nombre;
                $resultado['telefonoTutor'] = $tutor->telefono;
                $resultado['mailTutor'] = '';
                $resultado['tipoProcedencia'] = (is_null($escuela->idTipoEscuela)) ? '' : Tipoescuela::find($escuela->idTipoEscuela)->nombre;
                $resultado['nombreProcedencia'] = (is_null($escuela)) ? '' : Escuela::find($escuela->idEscuela)->nombre;
                $resultado['otro2'] = '';
                $resultado['ciudadEscuela'] = '';
                $resultado['otro3'] = '';
                $resultado['promedio'] = (is_null($escuela)) ? '' : $escuela->promedio;
                $resultado['nombreAspiracion'] = (is_null($aspiracion)) ? 'N/A' : Centrosuniversitario::find($aspiracion->idCentroUniversitario)->siglas;
                $resultado['otro4'] = '';
                $resultado['carreraAspiracion'] = (is_null($carrera)) ? 'NA' : $carrera->nombre;
                $resultado['otro5'] = '';
                $resultado['puntaje'] = (is_null($carrera)) ? 'NA' : $carrera->puntaje;
                $puntos = (is_null($escuela)) ? 0 : $escuela->promedio;
                $resultado['puntajeNecesario'] = (is_null($carrera)) ? 'NA' : (floatval($carrera->puntaje) - floatval($puntos));
                $resultado['intentos'] = $ficha->intentos;
                $resultado['tomoCurso'] = (!is_null($publicitarios) && (intval($publicitarios->tomoCurso) === 1)) ? 'SI' : 'No';
                $donde = (is_null($publicitarios)) ? null : Empresascurso::find($publicitarios->idEmpresaCurso);
                $aqui = (is_null($donde)) ? '' : $donde->nombre;
                $resultado['donde'] = (!is_null($publicitarios) && (intval($publicitarios->tomoCurso) === 1)) ? $aqui : 'NA';
                $resultado['otro6'] = '';
                $resultado['medio'] = (is_null($publicitarios)) ? 'NA' : Mediospublicitario::find($publicitarios->idMedioPublicitario)->nombre;
                $resultado['via'] = (is_null($publicitarios)) ? 'NA' : Viaspublicitaria::find($publicitarios->idViaPublicitaria)->nombre;
                $resultado['otro7'] = '';
                $resultado['campania'] = (is_null($publicitarios)) ? 'NA' : Campania::find($publicitarios->idCampania)->nombre;
                $resultado['medioContacto'] = (is_null($publicitarios)) ? 'NA' : Medioscontacto::find($publicitarios->idMedioContacto)->nombre;
                $resultado['razon'] = $razon;
                $resultado['numeroRegistro'] = '';
                $resultado['password'] = '';
                $resultado['asesorias'] = '';
                $resultado['admitido'] = '';
                $resultado['puntajePAA'] = '';
                $resultado['profesorMate'] = '';
                $resultado['profesorEspanol'] = '';
                $resultado['costo'] = $alta->precio;
                $resultado['descuentoPorcentaje'] = (count($descuentos) > 0) ? $descuentos[0]->cantidad : '';
                $resultado['descuentoAdicional'] = (count($descuentos) > 1) ? $descuentos[1]->cantidad : '';
                $descuentoMonto = 0;
                foreach ($descuentos as $descuento) {
                    $descuentoMonto = $descuentoMonto + $descuento->monto;
                }
                $resultado['tipoDescuento'] = '';
                $resultado['costoFinal'] = ($alta->precio - $descuentoMonto);
                $resultado['saldoActual'] = saldoFicha($ficha->id);
                $resultado['numeroAbonos'] = count($abonos);
                $activo = '';
                if(intval($ficha->estatus) === 1)
                    $activo = 'Activo';
                else if(intval($ficha->estatus) === 2)
                    $activo = 'Inasistencia';
                else if(intval($ficha->estatus) === 3)
                    $activo = 'Congelado';
                $resultado['activo'] = $activo;
                $resultado['fechaLimite'] = $alta->limitePago;
                $resultado['fechaConvenio'] = "";
                $i = 0;
                foreach ($abonos as $abono) {
                    $resultado['folioAbono'.$i] = Ingreso::find($abono->idIngreso)->folio;
                    $resultado['abono'.$i] = $abono->monto;
                    $i++;
                }
                $respuesta[] = $resultado;
            }
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function reporteImpartidos(Request $request){
        try {
            $calendario = Calendario::find($request['idCalendario']);
            $respuesta = array();
            $resultados = array();
            $fichas = Ficha::join('sucursales', 'idSucursalInscripcion', '=', 'sucursales.id')->
                             select('fichas.*', 'sucursales.nombre as plantel')->
                             where('fichas.idCalendario', '=', $request['idCalendario'])->
                             where('fichas.idNivel', '=', $request['idNivel'])->
                             where('fichas.idSucursalImparticion', '=', $request['sucursal'])->
                             get();
            foreach ($fichas as $ficha) {
                $usuario = Usuario::find($ficha->idUsuario);
                $empleado = Empleado::find($usuario->idEmpleado);
                $grupo = Grupo::find($ficha->idGrupo);
                $alta = Altacurso::find($grupo->idAltaCurso);
                $horario = Horario::find($grupo->idHorario);
                $alumno = Alumno::find($ficha->idAlumno);

                $domicilioPosible = Alumnodomicilio::where('idAlumno', '=', $alumno->id)->get();
                $domicilio = (count($domicilioPosible) > 0) ? $domicilioPosible[0] : null;

                $tutorPosible = Tutore::where('idAlumno', '=', $alumno->id)->get();
                $tutor = (count($tutorPosible) > 0) ? $tutorPosible[0] : null;

                $escuelaPosible = Datosescolare::where('idAlumno', '=', $alumno->id)->get();
                $escuela = (count($escuelaPosible) > 0) ? $escuelaPosible[0] : null;

                $aspiracionPosible = Aspiracione::where('idFicha', '=', $ficha->id)->get();
                $aspiracion = (count($aspiracionPosible) > 0) ? $aspiracionPosible[0] : null;

                $carrera = (is_null($aspiracion)) ? null : Carrera::find($aspiracion->idCarrera);

                $publicitariosPosibles = Publicitario::where('idFicha', '=', $ficha->id)->get();
                $publicitarios = (count($publicitariosPosibles) > 0) ? $publicitariosPosibles[0] : null;

                if(intval($alta->idSubnivel) === 1){
                    $motivo = (is_null($publicitarios)) ? null : Motivosinscripcione::find($publicitarios->idMotivoInscripcion);
                    $razon = (is_null($motivo)) ? 'Desconocido' : $motivo->nombre;
                }else{
                    $motivo = Motivosbachillerato::find($publicitarios->idMotivoBachillerato);
                    $razon = (is_null($motivo)) ? 'Desconocido' : $motivo->nombre;
                }
                $descuentos = Alumnodescuento::where('idFicha', '=', $ficha->id)->get();
                $abonos = Alumnoabono::where('idFicha', '=', $ficha->id)->get();

                $fechaNacimiento = Carbon::createFromDate($alumno->fechaNacimiento);
                $hoy = Carbon::now();
                $edad = $fechaNacimiento->diffInYears($hoy);
                $resultado = array();


                $resultado['No.'] = $ficha->id;
                $resultado['folio'] = $ficha->folio;
                $resultado['semana'] = $ficha->semana;
                $resultado['plantelInscripcion'] = $ficha->plantel;
                $resultado['fechaInscripcion'] = $ficha->created_at;
                $resultado['mes'] = mes($ficha->created_at);
                $resultado['usuarioLleno'] = $empleado->nombre;
                $resultado['capturadoPor'] = Categoria::find($alta->idCategoria)->nombre;
                $resultado['calendario'] = (is_null($calendario->nombre)) ? '' : $calendario->nombre;
                $resultado['nivel'] = Nivele::find($ficha->idNivel)->nombre;
                $resultado['modalidad'] = Modalidade::find($alta->idModalidad)->nombre;
                $resultado['curso'] = Curso::find($alta->idCurso)->nombre;
                $resultado['fechaInicio'] = $alta->inicio;
                $resultado['fechaFin'] = $alta->fin;
                $resultado['horario'] = $horario->inicio.' '.$horario->fin;
                $resultado['plantelImparticion'] = Sucursale::find($ficha->idSucursalImparticion)->nombre;
                $resultado['apellidos'] = $alumno->apellidoPaterno.' '.$alumno->apellidoMaterno;
                $resultado['nombre'] = $alumno->nombre;
                $resultado['sexo'] = Sexo::find($alumno->idSexo)->nombre;
                $resultado['fechaNacimiento'] = $alumno->fechaNacimiento;
                $resultado['edad'] = $edad;
                $resultado['calle'] = (is_null($domicilio)) ? '' : $domicilio->calle;
                $resultado['numero'] = (is_null($domicilio)) ? '' :  $domicilio->numeroExterior;
                $resultado['colonia'] = (is_null($domicilio)) ? '' :  $domicilio->colonia;
                $resultado['CP'] = (is_null($domicilio)) ? '' :  $domicilio->codigoPostal;
                $resultado['ciudad'] = (is_null($domicilio)) ? '' :  Municipio::find($domicilio->idMunicipio)->nombre;
                $resultado['estado'] = (is_null($domicilio)) ? '' :  Estado::find($domicilio->idEstado)->nombre;
                $resultado['otro'] = '';
                $resultado['telefono'] = $alumno->telefono;
                $resultado['celular'] = $alumno->celular;
                $resultado['email'] = $alumno->correo;
                $resultado['nombreTutor'] = $tutor->nombre;
                $resultado['telefonoTutor'] = $tutor->telefono;
                $resultado['mailTutor'] = '';
                $resultado['tipoProcedencia'] = (is_null($escuela)) ? '' : Tipoescuela::find($escuela->idTipoEscuela)->nombre;
                $resultado['nombreProcedencia'] = (is_null($escuela)) ? '' : Escuela::find($escuela->idEscuela)->nombre;
                $resultado['otro2'] = '';
                $resultado['ciudadEscuela'] = '';
                $resultado['otro3'] = '';
                $resultado['promedio'] = (is_null($escuela)) ? '' : $escuela->promedio;
                $resultado['nombreAspiracion'] = (is_null($aspiracion)) ? 'N/A' : Centrosuniversitario::find($aspiracion->idCentroUniversitario)->siglas;
                $resultado['otro4'] = '';
                $resultado['carreraAspiracion'] = (is_null($carrera)) ? 'NA' : $carrera->nombre;
                $resultado['otro5'] = '';
                $resultado['puntaje'] = (is_null($carrera)) ? 'NA' : $carrera->puntaje;
                $puntos = (is_null($escuela)) ? 0 : $escuela->promedio;
                $resultado['puntajeNecesario'] = (is_null($carrera)) ? 'NA' : (floatval($carrera->puntaje) - floatval($puntos));
                $resultado['intentos'] = $ficha->intentos;
                $resultado['tomoCurso'] = (!is_null($publicitarios) && (intval($publicitarios->tomoCurso) === 1)) ? 'SI' : 'No';
                $donde = (is_null($publicitarios)) ? null : Empresascurso::find($publicitarios->idEmpresaCurso);
                $aqui = (is_null($donde)) ? '' : $donde->nombre;
                $resultado['donde'] = (!is_null($publicitarios) && (intval($publicitarios->tomoCurso) === 1)) ? $aqui : 'NA';
                $resultado['otro6'] = '';
                $resultado['medio'] = (is_null($publicitarios)) ? 'NA' : Mediospublicitario::find($publicitarios->idMedioPublicitario)->nombre;
                $resultado['via'] = (is_null($publicitarios)) ? 'NA' : Viaspublicitaria::find($publicitarios->idViaPublicitaria)->nombre;
                $resultado['otro7'] = '';
                $resultado['campania'] = (is_null($publicitarios)) ? 'NA' : Campania::find($publicitarios->idCampania)->nombre;
                $resultado['medioContacto'] = (is_null($publicitarios)) ? 'NA' : Medioscontacto::find($publicitarios->idMedioContacto)->nombre;
                $resultado['razon'] = $razon;
                $resultado['numeroRegistro'] = '';
                $resultado['password'] = '';
                $resultado['asesorias'] = '';
                $resultado['admitido'] = '';
                $resultado['puntajePAA'] = '';
                $resultado['profesorMate'] = '';
                $resultado['profesorEspanol'] = '';
                $resultado['costo'] = $alta->precio;
                $resultado['descuentoPorcentaje'] = (count($descuentos) > 0) ? $descuentos[0]->cantidad : '';
                $resultado['descuentoAdicional'] = (count($descuentos) > 1) ? $descuentos[1]->cantidad : '';
                $descuentoMonto = 0;
                foreach ($descuentos as $descuento) {
                    $descuentoMonto = $descuentoMonto + $descuento->monto;
                }
                $resultado['tipoDescuento'] = '';
                $resultado['costoFinal'] = ($alta->precio - $descuentoMonto);
                $resultado['saldoActual'] = saldoFicha($ficha->id);
                $resultado['numeroAbonos'] = count($abonos);
                $activo = '';
                if(intval($ficha->estatus) === 1)
                    $activo = 'Activo';
                else if(intval($ficha->estatus) === 2)
                    $activo = 'Inasistencia';
                else if(intval($ficha->estatus) === 3)
                    $activo = 'Congelado';
                $resultado['activo'] = $activo;
                $resultado['fechaLimite'] = $alta->limitePago;
                $resultado['fechaConvenio'] = "";
                $i = 0;
                foreach ($abonos as $abono) {
                    $resultado['folioAbono'.$i] = Ingreso::find($abono->idIngreso)->folio;
                    $resultado['abono'.$i] = $abono->monto;
                    $i++;
                }
                $respuesta[] = $resultado;
            }
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function ingresosGenerales(Request $request){
        try {
            $ingresos = ingresosGenerales($request['idCalendario'], $request['idNivel']);
            return response()->json($ingresos, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function egresosGenerales(Request $request){
        try {
            $egresos = egresosGenerales($request['idCalendario'], $request['idNivel']);
            $respuesta = array();
            foreach ($egresos as $egreso) {
                if($egreso->tipo === "Nominas"){
                    $departamento = departamentoEgreso($egreso->id);
                    $empleado = empleadoEgreso($egreso->id);
                    $egreso->tipo = (count($departamento) > 0) ? $departamento[0]->departamento : 'Nomina'.$egreso->id;
                    $egreso->concepto = (count($empleado) > 0) ? $empleado[0]->empleado : 'Nomina'.$egreso->id;
                }
                $respuesta[] = $egreso;
            }
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function selects() {
        try {
            $calendarios = Calendario::where('eliminado', '=', 0)->get();
            $niveles = Nivele::where('eliminado', '=', 0)->get();
            $subniveles = Subnivele::where('eliminado', '=', 0)->get();
            $categorias = Categoria::where('eliminado', '=', 0)->get();

            $respuesta['calendarios'] = $calendarios;
            $respuesta['niveles'] = $niveles;
            $respuesta['subniveles'] = $subniveles;
            $respuesta['categorias'] = $categorias;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function inscripciones(Request $request){
        try {
            DB::connection()->enableQueryLog();
            $calendario = 21;
            //Grafica
            $datos = Sucursale::select(
                'sucursales.nombre AS sucursal',
                DB::raw(
                    "(SELECT COUNT(*) FROM fichas INNER JOIN calendarios ON fichas.idCalendario = calendarios.id WHERE calendarios.id = $calendario AND fichas.idSucursalInscripcion = sucursales.id LIMIT 1) AS inscritos"
                )
            )->where('sucursales.id', '<>', 1)->
            orderBy('inscritos', 'DESC')->get();
            foreach ($datos as $dato) {
                $respuesta['sucursales'][] = $dato->sucursal;
                $respuesta['datos'][] = $dato->inscritos;
            }

            //Listas
            $respuesta['selects']['calendarios'] = Calendario::where('eliminado', '=', 0)->get();
            $respuesta['selects']['niveles'] = Nivele::where('eliminado', '=', 0)->get();
            $respuesta['selects']['subniveles'] = Subnivele::where('eliminado', '=', 0)->get();
            $respuesta['selects']['categorias'] = Categoria::where('eliminado', '=', 0)->get();
            $respuesta['selects']['sucursales'] = Sucursale::where('eliminado', '=', 0)->where('id', '<>', 1)->get();

            //Meta Mes
            $opciones = Metasme::select('idCalendario', 'idNivel', 'idSubnivel')->groupBy('idNivel', 'idSubnivel', 'idCalendario')->whereRaw('mes = MONTH(NOW())')->where('idCalendario', '=', $calendario)->get();
            $metasMes = array();
            foreach ($opciones as $key => $opcion) {
                $idNivel = $opcion->idNivel;
                $idSubnivel = $opcion->idSubnivel;
                $idCalendario = $opcion->idCalendario;
                $metas = Metasme::join('calendarios', 'idCalendario', '=', 'calendarios.id')->
                join('niveles', 'idNivel', '=', 'niveles.id')->
                join('sucursales', 'idSucursal', '=', 'sucursales.id')->
                join('subniveles', 'idSubnivel', '=', 'subniveles.id')->
                select(
                    'metasmes.meta',
                    'metasmes.idCalendario',
                    'metasmes.mes',
                    'sucursales.id as id',
                    'sucursales.nombre as sucursal',
                    'niveles.nombre as nivel',
                    'subniveles.nombre as subnivel',
                    'calendarios.nombre as calendario',
                    DB::raw("(SELECT COUNT(*) 
                            FROM fichas f, altacursos ac, grupos g
                            WHERE f.idGrupo = g.id AND g.idAltaCurso = ac.id AND ac.idNivel = $idNivel AND ac.idSubnivel = $idSubnivel
                            AND ac.idCalendario = $idCalendario AND MONTH(f.fecha) = MONTH(NOW()) AND f.idSucursalInscripcion = sucursales.id AND
                            f.estatus <> 3 AND
                            ac.precio > 200
                        ) AS inscritos"),
                    DB::raw("(SELECT COUNT(*) 
                            FROM fichas f, altacursos ac, grupos g
                            WHERE f.idGrupo = g.id AND g.idAltaCurso = ac.id AND ac.idNivel = $idNivel AND ac.idSubnivel = $idSubnivel
                            AND ac.idCalendario = $idCalendario AND MONTH(f.fecha) = MONTH(NOW()) AND
                            f.estatus <> 3 AND
                            ac.precio > 200
                        ) AS totalInscritos"),
                    DB::raw("(SELECT SUM(meta) 
                        FROM metasmes 
                        WHERE idCalendario = $idCalendario AND idNivel = $idNivel AND idSubnivel = $idSubnivel 
                        AND mes = MONTH(NOW())) AS totalGeneral")
                )->
                whereRaw('metasmes.mes = MONTH(NOW())')->
                where('idCalendario', '=', $opcion->idCalendario)->
                where('idSubnivel', '=', $opcion->idSubnivel)->
                where('idNivel', '=', $opcion->idNivel)->
                get();
                $metasMes[] = $metas;
            }
            $respuesta['metasMes'] = $metasMes;

            
            $opciones = Metascategoria::select('idCalendario', 'idCategoria')->groupBy('idCategoria', 'idCalendario')->where('idCalendario', '=', $calendario)->get();
            $metasCategoria = array();
            foreach ($opciones as $opcion) {
                $idCategoria = $opcion->idCategoria;
                $idCalendario = $opcion->idCalendario;
                $metas = Metascategoria::join('categorias', 'idCategoria', '=', 'categorias.id')->
                join('calendarios', 'idCalendario', '=', 'calendarios.id')->
                join('sucursales', 'idSucursal', '=', 'sucursales.id')->
                select([
                    'metascategorias.meta',
                    'sucursales.id as id',
                    'categorias.nombre as categoria',
                    DB::raw("(SELECT COUNT(*) 
                            FROM fichas f, altacursos ac, grupos g
                            WHERE f.idGrupo = g.id AND g.idAltaCurso = ac.id AND ac.idCalendario = $idCalendario 
                            AND ac.idCategoria = $idCategoria AND f.idSucursalInscripcion = sucursales.id AND 
                            f.estatus <> 3 AND ac.precio > 200
                        ) AS inscritos"),
                    DB::raw("(SELECT COUNT(*) 
                            FROM fichas f, altacursos ac, grupos g
                            WHERE f.idGrupo = g.id AND g.idAltaCurso = ac.id AND ac.idCalendario = $idCalendario 
                            AND ac.idCategoria = $idCategoria AND f.estatus <> 3 AND ac.precio > 200
                        ) AS totalInscritos"),
                    DB::raw("(SELECT SUM(meta) FROM metascategorias WHERE idCalendario = $idCalendario AND idCategoria = $idCategoria) AS totalGeneral")
                ])->
                where('idCalendario', '=', $opcion->idCalendario)->
                where('idCategoria', '=', $opcion->idCategoria)->
                get();
                $metasCategoria[] = $metas;
            }
            $respuesta['metasCategorias'] = $metasCategoria;

            $opciones = Metascurso::select('idCalendario', 'idNivel', 'idSubnivel', 'idModalidad', 'idCurso')->
            groupBy('idCalendario', 'idNivel', 'idSubnivel', 'idModalidad', 'idCurso')->
            where('idCalendario', '=', $calendario)->get();
            $metasCursos = array();
            foreach ($opciones as $opcion) {
                $idCalendario = $opcion->idCalendario;
                $idNivel = $opcion->idNivel;
                $idSubnivel = $opcion->idSubnivel;
                $idModalidad = $opcion->idModalidad;
                $idCurso = $opcion->idCurso;
                $metas = Metascurso::join('niveles', 'idNivel', '=', 'niveles.id')->
                    join('subniveles', 'idSubnivel', '=', 'subniveles.id')->
                    join('modalidades', 'idModalidad', '=', 'modalidades.id')->
                    join('cursos', 'idCurso', '=', 'cursos.id')->
                    join('calendarios', 'idCalendario', '=', 'calendarios.id')->
                    join('sucursales', 'idSucursal', '=', 'sucursales.id')->
                    select([
                        'niveles.nombre as nivel',
                        'subniveles.nombre as subnivel',
                        'modalidades.nombre as modalidad',
                        'cursos.nombre as curso',
                        'cursos.icono as imagen',
                        'sucursales.id as id',
                        'metascursos.meta',
                        DB::raw("(SELECT COUNT(*) 
                                FROM fichas f, altacursos ac, grupos g
                                WHERE f.idGrupo = g.id AND g.idAltaCurso = ac.id AND ac.idCalendario = $idCalendario 
                                AND ac.idNivel = $idNivel AND ac.idSubnivel = $idSubnivel AND ac.idModalidad = $idModalidad AND ac.idCurso = $idCurso AND f.idSucursalInscripcion = sucursales.id AND f.estatus <> 3 AND ac.precio > 200
                            ) AS inscritos"),
                        DB::raw("(SELECT COUNT(*) 
                                FROM fichas f, altacursos ac, grupos g
                                WHERE f.idGrupo = g.id AND g.idAltaCurso = ac.id AND ac.idCalendario = $idCalendario 
                                AND ac.idNivel = $idNivel AND ac.idSubnivel = $idSubnivel AND ac.idModalidad = $idModalidad AND ac.idCurso = $idCurso AND f.estatus <> 3 AND ac.precio > 200
                            ) AS totalInscritos"),
                        DB::raw("(SELECT SUM(meta) 
                                FROM metascursos 
                                WHERE idCalendario = $idCalendario AND idNivel = $idNivel AND idSubnivel = $idSubnivel AND idModalidad = $idModalidad AND idCurso = $idCurso) AS totalGeneral")
                    ])->
                    where('idCalendario', '=', $calendario)->
                    where('idNivel', '=', $idNivel)->
                    where('idSubnivel', '=', $idSubnivel)->
                    where('idModalidad', '=', $idModalidad)->
                    where('idCurso', '=', $idCurso)->
                    get();
                    $key = Nivele::find($idNivel)->nombre.' '.Subnivele::find($idSubnivel)->nombre.' '.Modalidade::find($idModalidad)->nombre;

                    $metasCursos[$key]['key'] = $key;
                    foreach ($metas as $meta) {
                        $metasCursos[$key]['datos'][] = $meta;   
                    }
            }
            $keys = array_keys($metasCursos);
            $final = array();
            
            foreach ($keys as $key) {
                $final[] = $metasCursos[$key];
            }
            $respuesta['metasCursos'] = $final;

            $metas = Metasme::select(
                    DB::raw("(SELECT COUNT(*) FROM fichas f, altacursos ac, grupos g WHERE f.idGrupo = g.id AND g.idAltaCurso = ac.id AND ac.idNivel = 1 AND ac.idSubnivel = 1 AND ac.idCalendario = $calendario AND f.estatus <> 3 AND ac.precio > 200) AS inscritos"),
                    DB::raw("(SELECT SUM(meta) 
                            FROM metasmes WHERE idCalendario = $calendario AND idNivel = 1 AND idSubnivel = 1 ) AS meta"),
                    DB::raw('CONCAT("GENERAL LICENCIATURA") AS sucursal')
                )->
                where('idCalendario', '=', $calendario)->limit(1)->
                get();
            $respuesta['metaGeneralLicenciatura'] = $metas[0];

            $metas = Metasme::select(
                    DB::raw("(SELECT COUNT(*) FROM fichas f, altacursos ac, grupos g WHERE f.idGrupo = g.id AND g.idAltaCurso = ac.id AND ac.idNivel = 1 AND ac.idSubnivel = 2 AND ac.idCalendario = $calendario AND f.estatus <> 3 AND ac.precio > 200) AS inscritos"),
                    DB::raw("(SELECT SUM(meta) 
                            FROM metasmes WHERE idCalendario = $calendario AND idNivel = 1 AND idSubnivel = 2 ) AS meta"),
                    DB::raw('CONCAT("GENERAL LICENCIATURA") AS sucursal')
                )->
                where('idCalendario', '=', $calendario)->limit(1)->
                get();
            $respuesta['metaGeneralPrepa'] = $metas[0];

            $queries = DB::getQueryLog();

            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function buscar(Request $request){
        try {
            $calendario = $request['idCalendario'];
            $nivel = $request['idNivel'];
            $subnivel = $request['idSubnivel'];
            $categoria = $request['idCategoria'];
            $datos = Sucursale::select(
                'sucursales.nombre AS sucursal',
                DB::raw(
                    "(SELECT COUNT(*) FROM fichas
                    INNER JOIN calendarios ON fichas.idCalendario = calendarios.id
                    INNER JOIN grupos ON fichas.idGrupo = grupos.id
                    INNER JOIN altacursos ON grupos.idAltaCurso = altacursos.id
                    WHERE fichas.idSucursalInscripcion = sucursales.id 
                    AND fichas.idCalendario = $calendario AND altacursos.idNivel = $nivel AND altacursos.idSubnivel = $subnivel AND altacursos.idCategoria = $categoria LIMIT 1) AS inscritos"
                )
            )->where('sucursales.id', '<>', 1)->
            orderBy('inscritos', 'DESC')->get();
            foreach ($datos as $dato) {
                $respuesta['sucursales'][] = $dato->sucursal;
                $respuesta['datos'][] = $dato->inscritos;
            }
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function empleadosVentas(){
        try {
            $empleados = Usuario::join('empleados', 'idEmpleado', '=', 'empleados.id')->
                                  select('usuarios.id as id', 'empleados.nombre as nombre')->
                                  where('empleados.idDepartamento', '=', 6)->get();
            $actual = date("Y");
            $principal = 2021;
            $anios = array();
            for ($i=2021; $i < $actual+1; $i++) { 
                $res['nombre'] = $i;
                $res['id'] = $i;
                $anios[] = $res;
            }
            $niveles = Nivele::where('activo', '=', 1)->get();
            $respuesta['empleados'] = $empleados;
            $respuesta['years'] = $anios;
            $respuesta['niveles'] = $niveles;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function comisiones(Request $request){
        try {
            $COLBACH = (intval($request['idNivel']) === 1) ? true : false;
            $fichas = comisionesUsuario($request['mes'], $request['year'], $request['idEmpleado'], $COLBACH);
            $comisionTotal = 0;
            $finales = array();
            foreach ($fichas as $ficha) {
                $descuento = (is_null($ficha->descuentos)) ? 0 : floatval($ficha->descuentos);
                $extras = (is_null($ficha->extras)) ? 0 : floatval($ficha->extras);
                $ficha->final = floatval($ficha->precio) - $descuento + $extras;
                if(intval($ficha->idNivel) === 2){
                    $ficha->comision = round((floatval($ficha->final) * floatval($ficha->porcentaje)) / 100, 2);
                    $comisionTotal = $comisionTotal + ($ficha->comision);
                }else{
                    $ficha->comision = (intval($ficha->tipo) === 1) ? 
                        round(floatval($ficha->final) * floatval(floatval($ficha->porcentaje)/100), 2) : 
                        round(floatval($ficha->porcentaje), 2);
                    $comisionTotal = $comisionTotal + (floatval($ficha->final) * .015);
                    $ficha->porcentaje = ($ficha->tipo === 1) ? $ficha->porcentaje : '-';
                }
                $finales[] = $ficha;
            }
            $respuesta['fichas'] = $finales;
            $respuesta['comision'] = $comisionTotal;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function prospectos(Request $request){
        try {
            $prospectos = Prospecto::leftJoin('seguimientos', 'seguimientos.idProspecto', '=', 'prospectos.id')->
            select(
                DB::raw("CONCAT(prospectos.nombre,' ', prospectos.apellidoPaterno, ' ', prospectos.apellidoMaterno)"),
                DB::raw("(CASE seguimientos.estatus 
                    WHEN 2 THEN 'INSCRITO'
                    ELSE 'NO INSCRITO'
                     END)  as estatus"),
                DB::raw('(SELECT COUNT(*) FROM seguimientodescripciones WHERE seguimientodescripciones.idSeguimiento = seguimientos.id AND seguimientodescripciones.medio = 8 LIMIT 1) AS citas' ),
                'seguimientos.idFicha',
                DB::raw('IF(seguimientos.idFicha > 0, (SELECT folio FROM fichas WHERE fichas.id = seguimientos.idFicha LIMIT 1), "-") as ficha')
            )->get();

            return response()->json($prospectos, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}