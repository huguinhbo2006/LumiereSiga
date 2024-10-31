<?php

namespace App\Http\Controllers;
use App\Examene;
use App\Calendario;
use App\Nivele;
use App\Subnivele;
use App\Seccione;
use App\Seccionpregunta;
use App\Lectura;
use App\Pregunta;
include "funciones/FuncionesGenerales.php";

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamenesController extends BaseController
{
    
    function nuevo(Request $request){
        try{
            $calendario = Calendario::find($request['idCalendario']);
            $inicio = $request['inicio'];
            $fin = $request['fin'];
            if(mayor($inicio, $fin)){
                return response()->json('La fecha de inicio del examen es posterior a la fecha de fin', 400);
            }
            if(menor($inicio, $calendario->inicio)){
                return response()->json('La fecha de inicio del examen debe ser posterior o igual a la del inicio del calendario', 400);
            }
            if(mayor($inicio, $calendario->fin) || igual($inicio, $calendario->fin)){
                return response()->json('La fecha de inicio del examen debe de ser anterior a la fecha de fin del examen');
            }
            if(!menor($fin, $calendario->fin)){
                return response()->json('La fecha de fin del examen debe de ser anterior o igual a la fecha de fin del calendario', 400);
            }

            $file = strtotime("now").'.txt';
            file_put_contents($file, $request['pdf']);
            $cadena = file_get_contents($file);
            $final = substr($cadena, 1);
            file_put_contents($file, $final);
            //copy($file, "examenes/".$file);

            $examen = Examene::create([
                'nombre' => $request['nombre'],
                'inicio' => $request['inicio'],
                'fin' => $request['fin'],
                'activo' => 1,
                'eliminado' => 0,
                'idCalendario' => $request['idCalendario'],
                'forma' => $request['forma'],
                'pdf' => $file
            ]);
            return response()->json($examen, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function mostrar(Request $request){
        try{
            $examenes = Examene::where('eliminado', '=', 0)->
                                 where('idCalendario', '=', $request['idCalendario'])->get();
            return response()->json($examenes, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            //return response()->json($request, 400);
            $calendario = Calendario::find($request['idCalendario']);
            $inicio = $request['inicio'];
            $fin = $request['fin'];
            if(mayor($inicio, $fin)){
                return response()->json('La fecha de inicio del examen es posterior a la fecha de fin', 400);
            }
            if(menor($inicio, $calendario->inicio)){
                return response()->json('La fecha de inicio del examen debe ser posterior o igual a la del inicio del calendario', 400);
            }
            if(mayor($inicio, $calendario->fin) || igual($inicio, $calendario->fin)){
                return response()->json('La fecha de inicio del examen debe de ser anterior a la fecha de fin del calendario', 400);
            }
            if(!menor($fin, $calendario->fin)){
                return response()->json('La fecha de fin del examen debe de ser anterior o igual a la fecha de fin del calendario', 400);
            }

            $file = strtotime("now").'.txt';
            file_put_contents($file, $request['pdf']);
            $cadena = file_get_contents($file);
            $final = substr($cadena, 1);
            file_put_contents($file, $final);
            //copy($file, "examenes/".$file);

            $examen = Examene::find($request['id']);
            $examen->nombre = $request['nombre'];
            $examen->inicio = $request['inicio'];
            $examen->fin = $request['fin'];
            $examen->idCalendario = $request['idCalendario'];
            $examen->forma = $request['forma'];
            $examen->bloqueado = $request['bloqueado'];
            $examen->pdf = (strlen($final) > 0) ? $file : $examen->pdf;
            $examen->save();
            return response()->json($examen, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $examen = Examene::find($request['id']);
            $examen->eliminado = 1;
            $examen->save();
            return response()->json($examen, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function selectores() {
        try {
            $calendarios = Calendario::where('eliminado', '=', 0)->get();
            $respuesta = array();
            $respuesta['calendarios'] = $calendarios;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function copiar(Request $request){
        try {
            DB::beginTransaction();
            $examenAntiguo = Examene::find($request['idExamen']);
            $examenActual = Examene::find($request['idCopia']);
            $examenActual->pdf = $examenAntiguo->pdf;
            $examenActual->save();
            $seccionesActuales = Seccione::where('idExamen', '=', $request['idCopia'])->get();
            if(count($seccionesActuales) > 0){
                foreach ($seccionesActuales as $section) {
                    DB::table('lecturas')->where('idExamen', '=', $request['idCopia'])->delete();
                    DB::table('examenpermisos')->where('idExamen', '=', $request['idCopia'])->delete();
                    DB::table('examenporcentajes')->where('idExamen', '=', $request['idCopia'])->delete();
                    DB::table('preguntas')->where('idSeccion', '=', $section->id)->delete();
                    DB::table('seccionesporcentajes')->where('idSeccion', '=', $section->id)->delete();
                    $section->delete();
                }
            }
            $secciones = Seccione::where('idExamen', '=', $request['idExamen'])->get();
            foreach ($secciones as $seccion) {
                $nuevaSeccion = Seccione::create([
                    'idExamen' => $request['idCopia'],
                    'nombre' => $seccion->nombre,
                    'tiempo' => $seccion->tiempo,
                    'eliminado' => 0,
                    'activo' => 1,
                    'valido' => $seccion->valido,
                    'instrucciones' => $seccion->instrucciones
                ]);
                $lecturas = Lectura::where('idSeccion', '=', $seccion->id)->get();
                $listaLecturas = array();
                foreach ($lecturas as $lectura) {
                    $nuevaLectura = Lectura::create([
                        'idExamen' => $request['idCopia'],
                        'idSeccion' => $nuevaSeccion->id,
                        'nombre' => $lectura->nombre,
                        'contenido' => $lectura->contenido,
                        'tipo' => $lectura->tipo,
                        'activo' => 1,
                        'eliminado' => 0
                    ]);
                    $nuevaLectura->anterior = $lectura->id;
                    $listaLecturas[] = $nuevaLectura;
                }
                $preguntas = Pregunta::where('idSeccion', '=', $seccion->id)->get();
                foreach ($preguntas as $pregunta) {
                    $lecturaSeleccionada = 0;
                    foreach ($listaLecturas as $lectura) {
                        if(intval($lectura->anterior) === intval($pregunta->idLectura)){
                            $lecturaSeleccionada = $lectura->id;
                        }
                    }
                    $preguntaNueva = Pregunta::create([
                        'idSeccion' => $nuevaSeccion->id,
                        'idLectura' => $lecturaSeleccionada,
                        'indice' => $pregunta->indice,
                        'pregunta' => $pregunta->pregunta,
                        'respuesta' => $pregunta->respuesta,
                        'respuestaA' => $pregunta->respuestaA,
                        'respuestaB' => $pregunta->respuestaB,
                        'respuestaC' => $pregunta->respuestaC,
                        'respuestaD' => $pregunta->respuestaD,
                        'tipo' => $pregunta->tipo,
                        'eliminado' => 0,
                        'activo' => 1
                    ]);
                }
            }
            DB::commit();
            return response()->json($secciones, 200);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json('Error en el servidor', 400);
        }
    }
}