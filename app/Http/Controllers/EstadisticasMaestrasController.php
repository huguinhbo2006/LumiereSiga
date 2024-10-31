<?php

namespace App\Http\Controllers;
use App\Ficha;
use Carbon\Carbon;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
include "funciones/FuncionesGenerales.php";
include "funciones/funcionesBaseDatos.php";

class EstadisticasMaestrasController extends BaseController
{
    function id(Request $request){
        try {
            $fichas = Ficha::leftjoin('sucursales', 'idSucursalInscripcion', '=', 'sucursales.id')->
            select('fichas.id',
                   'folio',
                   'semana',
                   'sucursales.nombre as idSucursalInscripcion',
                   'fichas.created_at',
                   DB::raw('ELT(MONTH(fichas.created_at), "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre") as mes'),
                   DB::raw('(SELECT e.nombre FROM empleados e, usuarios u where e.id = u.idEmpleado AND u.id = fichas.idUsuarioInformacion LIMIT 1) AS usuarioLleno'),
               )->
            where('idCalendario', '=', $request['idCalendario'])->
            where('idNivel', '=', $request['idNivel'])->
            orderBy('fichas.id', 'ASC')->get();
            return response()->json($fichas, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function datos(Request $request){
        try {
            $fichas = Ficha::leftjoin('grupos', 'idGrupo', '=', 'grupos.id')->
            leftjoin('altacursos', 'grupos.idAltaCurso', '=', 'altacursos.id')->
            leftjoin('categorias', 'altacursos.idCategoria', '=', 'categorias.id')->
            leftjoin('calendarios', 'altacursos.idCalendario', '=', 'calendarios.id')->
            leftjoin('niveles', 'altacursos.idNivel', '=', 'niveles.id')->
            leftjoin('modalidades', 'altacursos.idModalidad', '=', 'modalidades.id')->
            leftjoin('cursos', 'altacursos.idCurso', '=', 'cursos.id')->
            leftjoin('horarios', 'grupos.idHorario', '=', 'horarios.id')->
            leftjoin('sucursales', 'fichas.idSucursalImparticion', '=', 'sucursales.id')->
            select(
                'fichas.id',
                'categorias.nombre as categoria',
                'calendarios.nombre as calendario',
                'niveles.nombre as nivel',
                'modalidades.nombre as modalidad',
                'cursos.nombre as curso',
                'altacursos.inicio as inicio',
                'altacursos.fin as fin',
                DB::raw('CONCAT(horarios.inicio,"-",horarios.fin) as horario'),
                'sucursales.nombre as imparticion'
            )->where('fichas.idCalendario', '=', $request['idCalendario'])->
            where('fichas.idNivel', '=', $request['idNivel'])->
            orderBy('fichas.id', 'ASC')->get();

            return response()->json($fichas, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function alumnos(Request $request){
        try {
            $fichas = Ficha::leftjoin('alumnos', 'fichas.idAlumno', '=', 'alumnos.id')->
            leftjoin('sexos', 'alumnos.idSexo', '=', 'sexos.id')->
            leftjoin('alumnodomicilios', 'alumnos.id', '=', 'alumnodomicilios.idAlumno')->
            leftjoin('municipios', 'alumnodomicilios.idMunicipio', '=', 'municipios.id')->
            leftjoin('estados', 'alumnodomicilios.idEstado', '=', 'estados.id')->
            leftjoin('tutores', 'alumnos.id', '=', 'tutores.idAlumno')->
            select(
                'fichas.id as id',
                DB::raw('CONCAT(alumnos.apellidoPaterno, " ",alumnos.apellidoMaterno) as apellidos'),
                'alumnos.nombre as nombre',
                'sexos.nombre as sexo',
                'alumnos.fechaNacimiento',
                DB::raw("TIMESTAMPDIFF(YEAR, alumnos.fechaNacimiento, CURDATE()) as edad"),
                DB::raw("REPLACE(alumnodomicilios.calle, ',', '') as calle"),
                'alumnodomicilios.numeroExterior as numero',
                DB::raw("REPLACE(alumnodomicilios.colonia, ',', '') as colonia"),
                'alumnodomicilios.codigoPostal as codigo',
                'municipios.nombre as municipio',
                'estados.nombre as estado',
                DB::raw('CONCAT("") AS otroDomicilio'),
                'alumnos.telefono as telefono',
                'alumnos.celular as celular',
                DB::raw("REPLACE(alumnos.correo, ',', '') as email"),
                'tutores.nombre as tutor',
                'tutores.celular as tutorCelular',
                'tutores.telefono as tutorTelefono',
            )->where('fichas.idCalendario', '=', $request['idCalendario'])->
            where('fichas.idNivel', '=', $request['idNivel'])->
            orderBy('fichas.id', 'ASC')->get();
            return response()->json($fichas, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function escolares(Request $request){
        try {
            $fichas = Ficha::leftjoin('alumnos', 'fichas.idAlumno', '=', 'alumnos.id')->
            leftjoin('datosescolares', 'alumnos.id', '=', 'datosescolares.idAlumno')->
            leftjoin('tipoescuelas', 'datosescolares.idTipoEscuela', '=', 'tipoescuelas.id')->
            leftjoin('escuelas', 'datosescolares.idEscuela', '=', 'escuelas.id')->
            leftjoin('aspiraciones', 'fichas.id', '=', 'aspiraciones.idFicha')->
            leftjoin('centrosuniversitarios', 'aspiraciones.idCentroUniversitario', '=', 'centrosuniversitarios.id')->
            leftjoin('universidades', 'aspiraciones.idUniversidad', '=', 'universidades.id')->
            leftjoin('carreras', 'aspiraciones.idCarrera', '=', 'carreras.id')->
            select(
                'fichas.id as id',
                'tipoescuelas.nombre as tipoEscuela',
                DB::raw('REPLACE(escuelas.nombre, ",", "") as escuela'),
                DB::raw('CONCAT("") AS otraEscuela'),
                DB::raw('CONCAT("") AS ciudadEscuela'),
                DB::raw('CONCAT("") AS otra3'),
                'datosescolares.promedio as promedio',
                'centrosuniversitarios.nombre as universidad',
                DB::raw('CONCAT("") AS otra4'),
                'carreras.nombre as carrera',
                DB::raw('CONCAT("") AS otra5'),
                'carreras.puntaje as puntaje',
                DB::raw('(carreras.puntaje - datosescolares.promedio) as promedioNecesario'),
                'fichas.intentos as intentos',
            )->where('fichas.idCalendario', '=', $request['idCalendario'])->
            where('fichas.idNivel', '=', $request['idNivel'])->
            orderBy('fichas.id', 'ASC')->get();
            return response()->json($fichas, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function publicitarios(Request $request){
        try {
            $fichas = Ficha::leftjoin('publicitarios', 'fichas.id', '=', 'publicitarios.idFicha')->
            leftjoin('empresascursos', 'publicitarios.idEmpresaCurso', '=', 'empresascursos.id')->
            leftjoin('mediospublicitarios', 'publicitarios.idMedioPublicitario', '=', 'mediospublicitarios.id')->
            leftjoin('viaspublicitarias', 'publicitarios.idViaPublicitaria', '=', 'viaspublicitarias.id')->
            leftjoin('campanias', 'publicitarios.idCampania', '=', 'campanias.id')->
            leftjoin('medioscontactos', 'publicitarios.idMedioContacto', '=', 'medioscontactos.id')->
            leftjoin('motivosinscripciones', 'publicitarios.idMotivoInscripcion', '=', 'motivosinscripciones.id')->
            leftjoin('motivosbachilleratos', 'publicitarios.idMotivoBachillerato', '=', 'motivosbachilleratos.id')->
            leftjoin('grupos', 'idGrupo', '=', 'grupos.id')->
            leftjoin('altacursos', 'grupos.idAltaCurso', '=', 'altacursos.id')->
            select(
                'fichas.id',
                DB::raw('IF(publicitarios.tomoCurso = 1, "SI", "NO") as tomoCurso'),
                'empresascursos.nombre as empresaCurso',
                DB::raw('CONCAT("") AS otra6'),
                'mediospublicitarios.nombre as medioPublicitario',
                'viaspublicitarias.nombre as via',
                DB::raw('CONCAT("") AS otra7'),
                'campanias.nombre as campania',
                'medioscontactos.nombre as medioContacto',
                DB::raw("IF(altacursos.idSubnivel = 1, motivosinscripciones.nombre, motivosbachilleratos.nombre) as razon"),
                DB::raw('CONCAT("") AS numeroRegistro'),
                DB::raw('CONCAT("") AS password'),
                DB::raw('CONCAT("") AS asesorias'),
                DB::raw('CONCAT("") AS admitido'),
                DB::raw('CONCAT("") AS puntajePAA'),
                DB::raw('CONCAT("") AS profesorMate'),
                DB::raw('CONCAT("") AS profesorEspanol'),
            )->where('fichas.idCalendario', '=', $request['idCalendario'])->
            where('fichas.idNivel', '=', $request['idNivel'])->
            orderBy('fichas.id', 'ASC')->get();
            return response()->json($fichas, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function cuenta(Request $request){
        try {
            $fichas = Ficha::leftjoin('grupos', 'idGrupo', '=', 'grupos.id')->
            leftjoin('altacursos', 'grupos.idAltaCurso', '=', 'altacursos.id')->
            select(
                'fichas.id',
                'altacursos.precio as costo',
                DB::raw("(SELECT SUM(cantidad) FROM alumnodescuentos WHERE idFicha = fichas.id AND eliminado = 0 LIMIT 1) as descuento"),
                DB::raw('CONCAT("") AS descuentoAdicional'),
                DB::raw('CONCAT("") AS tipoDescuento'),
                DB::raw("altacursos.precio - (SELECT SUM(monto) FROM alumnodescuentos WHERE idFicha = fichas.id AND eliminado = 0 LIMIT 1) as saldoActual"),
                DB::raw("(
                    IF((SELECT SUM(monto) FROM alumnocargos WHERE idFicha = fichas.id AND eliminado = 0 LIMIT 1) IS NULL, 0, (SELECT SUM(monto) FROM alumnocargos where idFicha = fichas.id AND eliminado = 0 LIMIT 1)) -
                    IF((SELECT SUM(monto) FROM alumnoabonos WHERE idFicha = fichas.id AND eliminado = 0 LIMIT 1) IS NULL, 0, (SELECT SUM(monto) FROM alumnoabonos WHERE idFicha = fichas.id AND eliminado = 0 LIMIT 1)) -
                    IF((SELECT SUM(monto) FROM alumnodescuentos WHERE idFicha = fichas.id AND eliminado = 0 LIMIT 1) IS NULL, 0, (SELECT SUM(monto) FROM alumnodescuentos WHERE idFicha = fichas.id AND eliminado = 0 LIMIT 1)) +
                    IF((SELECT SUM(monto) FROM alumnodevoluciones WHERE idFicha = fichas.id AND eliminado = 0 LIMIT 1) IS NULL, 0, (SELECT SUM(monto) FROM alumnodevoluciones WHERE idFicha = fichas.id AND eliminado = 0 LIMIT 1)) -
                    IF((SELECT SUM(monto) FROM alumnoextras WHERE idFicha = fichas.id AND eliminado = 0 LIMIT 1) IS NULL, 0, (SELECT SUM(monto) FROM alumnoextras WHERE idFicha = fichas.id AND eliminado = 0 LIMIT 1))
                ) as final"),
                DB::raw('CONCAT("") AS numeroAbonos'),
                DB::raw("CASE 
                    WHEN fichas.estatus = 1 THEN 'Activo'
                    WHEN fichas.estatus = 2 THEN 'Inasistencia'
                    WHEN fichas.estatus = 3 THEN 'Congelado'
                    END as estatus"
                ),
                'altacursos.limitePago as fechaLimite',
                DB::raw('CONCAT("") AS fechaConvenio'),
            )->where('fichas.idCalendario', '=', $request['idCalendario'])->
            where('fichas.idNivel', '=', $request['idNivel'])->
            orderBy('fichas.id', 'ASC')->get();
            return response()->json($fichas, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}