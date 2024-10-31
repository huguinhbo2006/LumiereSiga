<?php

namespace App\Http\Controllers;

use App\Examene;
use App\Ficha;
use App\Grupo;
use App\Altacurso;
use App\Seccione;
use App\Calendario;
use App\Nivele;
use App\Subnivele;
use App\Curso;
use App\Categoria;
use App\Modalidade;
use App\Turno;
use App\Horario;
use App\Alumno;
use App\Examenpermiso;
use App\Examenporcentaje;
use App\Calificacione;
use App\Pregunta;
use App\Seccionesporcentaje;
use App\Datosescolare;
use App\Aspiracione;
use App\Carrera;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
include "funciones/funcionesBaseDatos.php";

class CalificadorController extends BaseController
{
    function selects(){
        try {
            $calendarios = Calendario::where('eliminado', '=', 0)->get();
            $niveles = Nivele::where('eliminado', '=', 0)->get();
            $subniveles = Subnivele::where('eliminado', '=', 0)->get();
            $cursos = Curso::where('eliminado', '=', 0)->get();
            $categorias = Categoria::where('eliminado', '=', 0)->get();
            $modalidades = Modalidade::where('eliminado', '=', 0)->get();
            $turnos = Turno::where('eliminado', '=', 0)->get();

            $respuesta['calendarios'] = $calendarios;
            $respuesta['niveles'] = $niveles;
            $respuesta['subniveles'] = $subniveles;
            $respuesta['cursos'] = $cursos;
            $respuesta['categorias'] = $categorias;
            $respuesta['modalidades'] = $modalidades;
            $respuesta['turnos'] = $turnos;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function mostrar(Request $request){
        try {
            $respuesta['calendarios'] = Calendario::where('eliminado', '=', 0)->get();
            $respuesta['niveles'] = Nivele::where('eliminado', '=', 0)->get();
            $respuesta['subniveles'] = Subnivele::where('eliminado', '=', 0)->get();
            $respuesta['cursos'] = Curso::where('eliminado', '=', 0)->get();
            $respuesta['categorias'] = Categoria::where('eliminado', '=', 0)->get();
            $respuesta['modalidades'] = Modalidade::where('eliminado', '=', 0)->get();
            $respuesta['turnos'] = Turno::where('eliminado', '=', 0)->get();
            $respuesta['horarios'] = Horario::select(
                '*',
                DB::raw('CONCAT(inicio," - ", fin) as nombre')
            )->where('eliminado', '=', 0)->get();
            $respuesta['grupos'] = Grupo::join('altacursos', 'idAltaCurso', '=', 'altacursos.id')->
            join('calendarios', 'altacursos.idCalendario', '=', 'calendarios.id')->
            join('niveles', 'altacursos.idNivel', '=', 'niveles.id')->
            join('subniveles', 'altacursos.idSubnivel', '=', 'subniveles.id')->
            join('categorias', 'altacursos.idCategoria', '=', 'categorias.id')->
            join('modalidades', 'altacursos.idModalidad', '=', 'modalidades.id')->
            join('turnos', 'idTurno', '=', 'turnos.id')->
            join('horarios', 'idHorario', '=', 'horarios.id')->
            join('cursos', 'altacursos.idCurso', '=', 'cursos.id')->
            join('sedes', 'altacursos.idSede', '=', 'sedes.id')->
            leftjoin('bloqueohorarios', 'grupos.id', '=', 'bloqueohorarios.idGrupo')->
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
                'calendarios.nombre as calendario',
                'niveles.nombre as nivel',
                'subniveles.nombre as subnivel',
                'categorias.nombre as categoria',
                'modalidades.nombre as modalidad',
                'sedes.nombre as sede',
                'turnos.nombre as turno',
                DB::raw("CONCAT(horarios.inicio, ' - ', horarios.fin) as horario"),
                'cursos.nombre as curso',
                'cursos.icono',
                'grupos.idHorario', 
                'grupos.idTurno',
                DB::raw('IF((SELECT COUNT(*) FROM bloqueohorarios WHERE idGrupo = grupos.id AND idSucursal = '.$request['sucursalID'].' LIMIT 1) > 0, bloqueohorarios.id, 0) as idBloqueo'),
                'bloqueohorarios.idSucursal'
            )->
            /*whereRaw('NOW() BETWEEN calendarios.inicio AND calendarios.fin')->*/get();
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function horarios(Request $request) {
        try {
            $horarios = Horario::where('eliminado', '=', 0)->
                                 where('idTurno', '=', $request['idTurno'])->get();
            $respuesta = array();
            foreach ($horarios as $horario) {
                $horario->nombre = $horario->inicio.'-'.$horario->fin;
                $respuesta[] = $horario;
            }
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function grupos(Request $request) {
        try {
            $calendario = $request['idCalendario'];
            $nivel = $request['idNivel'];
            $subnivel = $request['idSubnivel'];
            $curso = $request['idCurso'];
            $modalidad = $request['idModalidad'];
            $categoria = $request['idCategoria'];
            $turno = $request['idTurno'];
            $horario = $request['idHorario'];
            $consulta = "SELECT  g.*, ac.idCalendario, ac.idNivel, ac.idSubnivel, ac.idModalidad, ac.idCategoria, ac.idCurso, ac.inicio, ac.fin, ac.limitePago, ac.precio, c.nombre as calendario, t.nombre as turno, n.nombre as nivel, s.nombre as subnivel, m.nombre as modalidad, cat.nombre as categoria, CONCAT(h.inicio,'-',h.fin) as horario, cr.nombre as curso, cr.icono as icono, se.nombre as sede
            FROM altacursos ac, grupos g, calendarios c, turnos t, niveles n, subniveles s, modalidades m, categorias cat, cursos cr, horarios h, sedes se
            WHERE ac.idCalendario = c.id AND ac.idCurso = cr.id AND ac.idNivel = n.id AND ac.idSubnivel = s.id AND ac.idModalidad = m.id AND ac.idCategoria = cat.id AND ac.idSede = se.id AND g.idHorario = h.id AND g.idTurno = t.id AND g.idAltaCurso = ac.id AND g.eliminado = 0 ";

            $consulta.= (intval($calendario) > 0) ? ' AND c.id ='. $calendario. ' ' :  '';
            $consulta.= (intval($nivel) > 0) ? ' AND n.id ='. $nivel. ' ' :  '';
            $consulta.= (intval($subnivel) > 0) ? ' AND s.id ='. $subnivel. ' ' :  '';
            $consulta.= (intval($curso) > 0) ? ' AND cr.id ='. $curso. ' ' :  '';
            $consulta.= (intval($modalidad) > 0) ? ' AND m.id ='. $modalidad. ' ' :  '';
            $consulta.= (intval($categoria) > 0) ? ' AND cat.id ='. $categoria. ' ' :  '';
            $consulta.= (intval($turno) > 0) ? ' AND t.id ='. $turno. ' ' :  '';
            $consulta.= (intval($horario) > 0) ? ' AND h.id ='. $horario. ' ' :  '';
            $registros = DB::select($consulta, array());
            return response()->json($registros, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function alumnos(Request $request){
        try {
            $fichas = Ficha::where('idGrupo', '=', $request['idGrupo'])->
                             where('idSucursalImparticion', '=', $request['idSucursal'])->
                             where('eliminado', '=', 0)->
                             where('estatus', '=', 1)->get();
            $respuesta = array();
            foreach ($fichas as $ficha) {
                $alumno = Alumno::find($ficha->idAlumno);
                $ficha->alumno = $alumno->nombre. ' '. $alumno->apellidoPaterno. ' '. $alumno->apellidoMaterno;
                $respuesta[] = $ficha;
            }
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function traerExamenes(Request $request){
        try {
            $examenes = traerExamenesGrupo($request['idGrupo']);
            return response()->json($examenes, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function traerSecciones(Request $request){
        try {
            $porcentajes = Examenporcentaje::where('idExamen', '=', $request['idExamen'])->
                                             where('eliminado', '=', 0)->get();
            $ficha = Ficha::find($request['idFicha']);
            $alumno = Alumno::find($ficha->idAlumno);
            if(intval($ficha->idNivel) !== 2){
                $escolares = Datosescolare::where('idAlumno', '=', $alumno->id)->get()[0];
                $aspiracion = Aspiracione::where('idFicha', '=', $ficha->id)->get()[0];
                $carrera = Carrera::find($aspiracion->idCarrera);
            }else{
                $escolares = null;
                $aspiracion = null;
                $carrera = null;
            }
            
            $examen = Examene::find($request['idExamen']);


            $respuesta = array();
            $porcentajeFinal = 0;
            foreach ($porcentajes as $porcentaje) {
                $seccionesPorcentaje = array();
                $secciones = Seccionesporcentaje::where('idPorcentaje', '=', $porcentaje->id)->get();
                $total = count($secciones);
                $aciertosTotales = 0;
                $preguntasTotales = 0;
                foreach ($secciones as $seccion) {
                    $resultado = Calificacione::join('secciones', 'idSeccion', '=', 'secciones.id')->
                                                select('secciones.nombre as seccion', 'secciones.id as idSeccion', 'calificaciones.aciertos', 'calificaciones.errores', 'calificaciones.ausentes')->
                                                where('calificaciones.idFicha', '=', $request['idFicha'])->
                                                where('calificaciones.idExamen', '=', $request['idExamen'])->
                                                where('idSeccion', '=', $seccion->idSeccion)->
                                                where('secciones.eliminado', '=', 0)->get();
                    if(count($resultado) > 0){
                        $seccionesPorcentaje[] = $resultado[0];
                        $aciertosTotales = $aciertosTotales + intval($resultado[0]->aciertos);
                        $preguntasTotales = $preguntasTotales + count(Pregunta::where('idSeccion', '=', $seccion->idSeccion)->get());
                        $total--;
                    }else{
                        $res = Seccione::find($seccion->idSeccion);
                        $section['seccion'] = ($res->eliminado) ? '' : $res->nombre;
                        $section['promedio'] = '';
                        $section['aciertos'] = '';
                        $section['errores'] = '';
                        $section['ausentes'] = '';
                        $section['idSeccion'] = $seccion->idSeccion;
                        if(!$res->eliminado) 
                            $seccionesPorcentaje[] = $section;
                    }
                }
                $porcentaje->secciones = $seccionesPorcentaje;
                if($total === 0){
                    if(intval($examen->forma) === 1){
                        $porcentaje->promedio = intval((($aciertosTotales / $preguntasTotales) * 600) + 200);
                        $porcentaje->totalPromedio = ($aciertosTotales / $preguntasTotales) * intval($porcentaje->porcentaje);
                    }else{
                        $porcentaje->promedio = ($aciertosTotales / $preguntasTotales) * 100;
                        $porcentaje->totalPromedio = ($aciertosTotales / $preguntasTotales) * 100;
                    }
                    $porcentajeFinal = $porcentajeFinal + intval($porcentaje->porcentaje);
                }
                $respuesta[] = $porcentaje;
            }
            $final = array();
            if($porcentajeFinal === 100){
                $promedioExamen = 0;
                foreach ($respuesta as $res) {
                    $promedioExamen = $promedioExamen + $res->totalPromedio;
                }
                if(intval($ficha->idNivel) !== 2){
                    $final['promedioExamen'] = number_format($promedioExamen, 2, '.');
                    $final['promedioAlumno'] = $escolares->promedio;
                    $final['puntajeCarrera'] = $carrera->puntaje;
                    $total = (floatval($promedioExamen) + floatval($escolares->promedio)) - floatval($carrera->puntaje);
                    $final['diferencia'] = number_format($total, 2, '.');
                    $final['colbach'] = false;
                }else{
                    $final['promedioExamen'] = number_format($promedioExamen, 2, '.');
                    $final['colbach'] = true;
                }
                
            }else{
                $final['promedioExamen'] = number_format(0, 2, '.');;
            }
            $seccionesAusentes = Seccione::where('idExamen', '=', $request['idExamen'])->
                                           where('eliminado', '=', 0)->
                                           where('valido', '=', 0)->get();
            foreach ($seccionesAusentes as $seccion) {
                $porcentaje = array();
                $resultado = Calificacione::join('secciones', 'idSeccion', '=', 'secciones.id')->
                                                select('secciones.nombre', 'secciones.id as idSeccion', 'calificaciones.aciertos', 'calificaciones.errores', 'calificaciones.ausentes')->
                                                where('calificaciones.idFicha', '=', $request['idFicha'])->
                                                where('calificaciones.idExamen', '=', $request['idExamen'])->
                                                where('idSeccion', '=', $seccion->id)->
                                                where('secciones.eliminado', '=', 0)->get();

                if(count($resultado) > 0){
                    $porcentaje['nombre'] = $resultado[0]->nombre;
                    $porcentaje['promedio'] = ((intval($resultado[0]->aciertos) / count(Pregunta::where('idSeccion', '=', $resultado[0]->idSeccion)->get())) * 600) + 200;
                    $porcentaje['secciones'][] = $resultado[0];
                    $respuesta[] = $porcentaje;
                }else{
                    $res = Seccione::find($seccion->id);
                    $porcentaje['nombre'] = $res->nombre;
                    $porcentaje['promedio'] = '';
                    $section['seccion'] = '';
                    $section['aciertos'] = '';
                    $section['errores'] = '';
                    $section['ausentes'] = '';
                    $section['idSeccion'] = $res->id;
                    $porcentaje['secciones'][] = $section;
                    $respuesta[] = $porcentaje;
                }
            }
            $final['secciones'] = $respuesta;
            return response()->json($final, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function guardarSecciones(Request $request) {
        try {
            DB::beginTransaction();
            $examen = Examene::find($request['examen']);
            foreach ($request['secciones'] as $porcentajes) {
                foreach($porcentajes['secciones'] as $seccion){
                    $section = Calificacione::where('idFicha', '=', $request['ficha'])->
                                          where('idSeccion', '=', $seccion['idSeccion'])->
                                          where('idExamen', '=', $request['examen'])->get();
                    $preguntas = Pregunta::where('idSeccion', '=', $seccion['idSeccion'])->get();
                    $promedio = 0;
                    $seccion['aciertos'] = (strlen($seccion['aciertos']) > 0) ? $seccion['aciertos'] : 0;
                    $seccion['errores'] = (strlen($seccion['errores']) > 0) ? $seccion['errores'] : 0;
                    $seccion['ausentes'] = (strlen($seccion['ausentes']) > 0) ? $seccion['ausentes'] : 0;

                    $total = 0;
                    $total = $total + intval($seccion['aciertos']);
                    $total = $total + intval($seccion['errores']);
                    $total = $total + intval($seccion['ausentes']);

                    if($total > count($preguntas)){
                        return response()->json('La cantidad de totales en la seccion '.Seccione::find($seccion['idSeccion'])->nombre. ' es mayor a la cantidad de preguntas', 400);
                    }
                    if(count($preguntas) > $total){
                        return response()->json('La cantidad de totales en la seccion '.Seccione::find($seccion['idSeccion'])->nombre. ' es menor a la cantidad de preguntas', 400);
                    }
                    if(count($section) > 0){
                        $registro = Calificacione::find($section[0]->id);
                        $registro->aciertos = $seccion['aciertos'];
                        $registro->errores = $seccion['errores'];
                        $registro->ausentes = $seccion['ausentes'];
                        $registro->save();
                    }else{
                        $registro = Calificacione::create([
                            'idFicha' => $request['ficha'],
                            'idSeccion' => $seccion['idSeccion'],
                            'idExamen' => $request['examen'],
                            'aciertos' => $seccion['aciertos'],
                            'errores' => $seccion['errores'],
                            'ausentes' => $seccion['ausentes'],
                            'eliminado' => 0,
                            'activo' => 1
                        ]);
                    }
                }
            }
            DB::commit();
            return response()->json('Guardado correctamente', 200);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json('Error en el servidor', 400);
        }
    }
}