<?php

namespace App\Http\Controllers;
use App\Alumno;
use App\Ficha;
use App\Grupo;
use App\Altacurso;
use App\Examenpermiso;
use App\Examenporcentaje;
use App\Examene;
use App\Seccione;
use App\Pregunta;
use App\Lectura;
use App\Respuesta;
use App\Calificacione;
use App\Datosescolare;
use App\Aspiracione;
use App\Carrera;
use App\Seccionesporcentaje;
use Carbon\Carbon;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
include "funciones/FuncionesGenerales.php";
include "funciones/funcionesCalificador.php";
include "funciones/funcionesBaseDatos.php";

class LumieresocialController extends BaseController
{
    function existeAlumno(Request $request) {
        try{
            $alumno = Alumno::where('codigo', '=', $request['codigo'])->get();
            if(count($alumno) > 0){
                return response()->json($alumno[0], 200);
            }else{
                return response()->json('No existe un alumno con ese codigo', 400);
            }
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function traerAlumno(Request $request) {
        try{
            $alumno = Alumno::find($request['idAlumno']);
            return response()->json($alumno, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function traerFichas(Request $request) {
        try {
            $fichas = Ficha::where('idAlumno', '=', $request['idAlumno'])->get();
            $grupos = array();
            foreach ($fichas as $ficha) {
                $res = array();
                $consulta = "SELECT ac.inicio, ac.fin, ac.limitePago, ac.precio, c.nombre as calendario, t.nombre as turno, n.nombre as nivel, s.nombre as subnivel, m.nombre as modalidad, cat.nombre as categoria, CONCAT(h.inicio,'-',h.fin) as horario, cr.nombre as curso, cr.icono as icono
                FROM altacursos ac, grupos g, calendarios c, turnos t, niveles n, subniveles s, modalidades m, categorias cat, cursos cr, horarios h
                WHERE ac.idCalendario = c.id AND ac.idCurso = cr.id AND ac.idNivel = n.id AND ac.idSubnivel = s.id AND ac.idModalidad = m.id AND ac.idCategoria = cat.id AND g.idHorario = h.id AND g.idTurno = t.id AND g.idAltaCurso = ac.id AND g.eliminado = 0 AND g.id = ".$ficha->idGrupo;
                $registros = DB::select($consulta, array());
                $grupo = $registros[0];
                $ficha->grupo = $grupo;
                $grupo->idFicha = $ficha->id;
                $grupo->numeroRegistro = $ficha->numeroRegistro;

                $grupos[] = $grupo;
            }
            return response()->json($grupos, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function traerExamenes(Request $request){
        try {
            $ficha = Ficha::find($request['idFicha']);
            $grupo = Grupo::find($ficha->idGrupo);
            $alta = Altacurso::find($grupo->idAltaCurso);
            $examenes = Examenpermiso::where('idNivel', '=', $alta->idNivel)->
                                       where('idSubnivel', '=', $alta->idSubnivel)->
                                       where('idCategoria', '=', $alta->idCategoria)->
                                       where('eliminado', '=', 0)->get();
            $resultado = array();
            foreach ($examenes as $examen) {
                $registro = Examene::find($examen->idExamen);
                if((mayor($registro->inicio, $alta->inicio) || igual($alta->inicio, $registro->inicio)) && 
                    intval($alta->idCalendario) === intval($registro->idCalendario) && $registro->eliminado === 0){
                    $registro->inicioF = formatearFecha($registro->inicio);
                    $registro->finF = formatearFecha($registro->fin);
                    $resultado[] = $registro;
                }
            }
            return response()->json($resultado, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function traerSecciones(Request $request){
        try {
            $secciones = Seccione::where('idExamen', '=', $request['idExamen'])->where('eliminado', '=', 0)->get();
            $respuesta = array();
            foreach ($secciones as $seccion) {
                $existe = Calificacione::where('idFicha', '=', $request['idFicha'])->
                                         where('idExamen', '=', $request['idExamen'])->
                                         where('idSeccion', '=', $seccion->id)->get();
                if(count($existe) > 0){
                    $calificacion = $existe[0];
                    $seccion->calificado = true;
                    $seccion->calificacion = $calificacion;
                }else{
                    $seccion->calificado = false;    
                }
                $respuesta[] = $seccion;
            }
            return response()->json($secciones, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function traerPreguntas(Request $request){
        try {
            $preguntas = Pregunta::select('id', 'respuestaA', 'respuestaB', 'respuestaC', 'respuestaD',
                                          'pregunta', 'idLectura', 'tipo', 'indice')->
                                   where('preguntas.idSeccion', '=', $request['idSeccion'])->
                                   where('preguntas.eliminado', '=', 0)->get();
            $resultado = array();
            foreach ($preguntas as $pregunta) {
                if(intval($pregunta->idLectura) !== 0){
                    $lectura = Lectura::find($pregunta->idLectura)->contenido;
                    if(str_contains($lectura, 'data:image')){
                        $pregunta->lectura = $lectura;
                        $pregunta->esImagen = true;
                    }else if(str_contains($lectura, '(2)')){
                        $pregunta->lectura = $lectura;
                        $pregunta->esImagen = false;
                    }else{
                        $pregunta->esImagen = false;
                        $separada = explode('<br>', Lectura::find($pregunta->idLectura)->contenido);
                        $pregunta->lectura = '';
                        $i = 1;
                        foreach ($separada as $porcion) {
                            $pregunta->lectura = $pregunta->lectura. '('. $i. ')'. $porcion.'<br>';
                            $i++;
                        }
                        $pregunta->lectura = str_replace('\t', '<br>', $pregunta->lectura);
                    }
                }
                $resultado[] = $pregunta;
            }
            /*$preguntas = Pregunta::select('id', 'eliminado as seleccionda', 'indice')->where('preguntas.idSeccion', '=', $request['idSeccion'])->where('preguntas.eliminado', '=', 0)->get();*/
            return response()->json($preguntas, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function traerInstrucciones(Request $request){
        try {
            $seccion = Seccione::find($request['idSeccion']);
            $respuesta['instrucciones'] = $seccion->instrucciones;
            $respuesta['tiempo'] = $seccion->tiempo;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function guardarSeccion(Request $request){
        try {
            $preguntas = $request['preguntas'];
            $idSeccion = $request['idSeccion'];
            $seccion = Seccione::find($idSeccion);
            $examen = Examene::find($seccion->idExamen);

            //Guardar Respuestas
            $aciertos = 0;
            $errores = 0;
            $ausentes = 0;
            foreach ($preguntas as $pregunta) {
                $existe = Respuesta::where('idFicha', '=', $request['idFicha'])->where('idExamen', '=', $examen->id)->where('idSeccion', '=', $request['idSeccion'])->where('idPregunta', '=', $pregunta['id'])->get();
                if(count($existe) < 1){
                    Respuesta::create([
                        'idFicha' => $request['idFicha'],
                        'idExamen' => $examen->id,
                        'idSeccion' => $request['idSeccion'],
                        'idPregunta' => $pregunta['id'],
                        'respuesta' => (isset($pregunta['respuesta'])) ? $pregunta['respuesta'] : '',
                        'activo' => 1,
                        'eliminado' => 0
                    ]);
                    if(isset($pregunta['respuesta'])){
                        if($pregunta['respuesta'] === Pregunta::find($pregunta['id'])->respuesta){
                            $aciertos++;
                        }else{
                            $errores++;
                        }
                    }else{
                        $ausentes++;
                    }
                }
            }
            $respuesta['errores'] = $errores;
            $respuesta['aciertos'] = $aciertos;
            $respuesta['ausentes'] = $ausentes;

            if(intval($examen->forma) === 1){
                $promedio = (($aciertos/count($preguntas)) * 600) + 200;
            }else{
                $promedio = ($aciertos / count($preguntas)) * 100;
            }

            $existeCalificacion = Calificacione::where('idFicha', '=', $request['idFicha'])->where('idExamen', '=', $examen->id)->where('idSeccion', '=', $request['idSeccion'])->get();
            if(count($existeCalificacion) < 1){
                $calificacion = Calificacione::create([
                    'idFicha' => $request['idFicha'],
                    'idExamen' => $examen->id,
                    'idSeccion' => $request['idSeccion'],
                    'aciertos' => $aciertos,
                    'errores' => $errores,
                    'ausentes' => $ausentes,
                    'promedio' => $promedio,
                    'eliminado' => 0,
                    'activo' => 1
                ]);    
            }
            
            return response()->json('Todo Correcto', 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function traerCalificaciones(Request $request){
        try {

            $alumno = alumno($request['idFicha']);
            $examenes = traerExamenesFicha($request['idFicha']);
            $respuesta = array();
            foreach ($examenes as $examen) {
                if(examenCompletado($examen->id, $request['idFicha'])){
                    $examen->completado = true;
                    $examen->promedio = number_format(traerCalificacionExamen($examen->id, $request['idFicha']), 2, '.');
                }else{
                    $examen->completado = false;
                }
                $respuesta[] = $examen;
            }
            return response()->json($examenes, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function traerEstadisticas(Request $request){
        try {
            $colores = [
                'rgba(255, 99, 132, 0.2)',
                'rgba(201, 203, 207, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(255, 205, 86, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(201, 203, 207, 0.2)',
                'rgba(255, 22, 114, 0.2)'
            ];
            $bordes = [
                'rgb(255, 99, 132)',
                'rgb(201, 203, 207)',
                'rgb(75, 192, 192)',
                'rgb(255, 205, 86)',
                'rgb(54, 162, 235)',
                'rgb(153, 102, 255)',
                'rgb(255, 1, 115)'
                
            ];
            $examenes = traerExamenesFicha($request['idFicha']);
            $listaExamenes = array();
            $listaCalificaciones = array();

            $listaExamenes[] = '';
            $listaCalificaciones[] = 0;
            $listaSecciones = array();
            $respuesta = array();
            $maximo = 0;
            $seccionMuestra = array();
            foreach ($examenes as $examen) {
                $listaExamenes[] = $examen->nombre;
                $listaCalificaciones[] = traerCalificacionExamen($examen->id, $request['idFicha']);

                $secciones = traerCalificacionSeccionesPorcentajes($request['idFicha'], $examen->id);
                $invalidas = traerCalificacionSecciones($request['idFicha'], $examen->id);
                $totalSecciones = array_merge($secciones, $invalidas);
                $listaSecciones[] = $totalSecciones;
                if(count($totalSecciones) > $maximo){
                    $maximo = count($listaSecciones);
                    $seccionMuestra = $totalSecciones;
                }
            }

            $listaGeneralSecciones = array();
            $color = 0;
            foreach ($seccionMuestra as $seccion) {
                $registro = array();
                $registro['label'] = $seccion->nombre;
                $registro['borderColor'] = $bordes[$color];
                $registro['backgroundColor'] = $colores[$color];
                $registro['yAxisID'] = 'y';
                $registro['data'][] = 200;
                $color++;
                $listaGeneralSecciones[] = $registro;
            }

            $listaFinalSecciones = array();
            foreach ($listaGeneralSecciones as $seccion) {
                foreach ($listaSecciones as $secciones) {
                    $existe = false;
                    foreach ($secciones as $section) {
                        if($section->nombre === $seccion['label']){
                            $seccion['data'][] = intval($section->promedio);
                            $existe = true;
                        }
                    }
                    if(!$existe){
                        $seccion['data'][] = 200;
                    }
                }
                $listaFinalSecciones[] = $seccion;
            }

            $respuesta['listaSecciones'] = $listaFinalSecciones;
            $respuesta['examenes'] = $listaExamenes;
            $respuesta['calificacionesExamenes'] = $listaCalificaciones;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function respuestasSeccion(Request $request){
        try {
            $examen = Examene::find($request['idExamen']);
            $preguntas = traerRespuestasSeccion($request['idFicha'], $request['idExamen'], $request['idSeccion']);
            $archivo = "";
            if(strlen($examen->pdf) > 0 && file_exists($examen->pdf)){
                $archivo = file_get_contents($examen->pdf);
            }
            $respuesta['examen'] = $archivo;
            $respuesta['respuestas'] = $preguntas;
            $respuesta['hay'] = (count($preguntas) > 0) ? true : false;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function verificarFechaExamen(Request $request){
        try {
            $fecha = Carbon::now();
            $fecha = $fecha->subHours(5);
            $fecha = $fecha->format('d-m-Y');
            $inicio = Carbon::parse($request['inicio']);
            $inicio = $inicio->format('d-m-Y');
            $fin = Carbon::parse($request['fin']);
            $fin = $fin->format('d-m-Y');

            if((mayor($fecha, $inicio) || igual($fecha, $inicio)) && (menor($fecha, $fin) || igual($fecha, $fin))){
                return response()->json('Correcto', 200);
            }else{
                if(mayor($fecha, $fin)){
                    return response()->json('La fecha del examen ya paso', 400);
                }

                if(menor($fecha, $inicio)){
                    return response()->json('Aun no puedes contestar este examen', 400);
                }
            }
            return response()->json($fecha, 400);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function traerPregunta(Request $request){
        try {
            $pregunta = Pregunta::join('lecturas', 'idLectura', '=', 'lecturas.id')->
            where('preguntas.id', '=', $request['id'])[0];
            if(intval($pregunta->idLectura) !== 0){
                $lectura = Lectura::find($pregunta->idLectura)->contenido;
                if(str_contains($lectura, 'data:image')){
                    $pregunta->lectura = $lectura;
                    $pregunta->esImagen = true;
                }else if(str_contains($lectura, '(2)')){
                    $pregunta->lectura = $lectura;
                    $pregunta->esImagen = false;
                }else{
                    $pregunta->esImagen = false;
                    $separada = explode('<br>', Lectura::find($pregunta->idLectura)->contenido);
                    $pregunta->lectura = '';
                    $i = 1;
                    foreach ($separada as $porcion) {
                        $pregunta->lectura = $pregunta->lectura. '('. $i. ')'. $porcion.'<br>';
                        $i++;
                    }
                    $pregunta->lectura = str_replace('\t', '<br>', $pregunta->lectura);
                }
            }
            $pregunta->respuesta = '';
            return response()->json($pregunta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}