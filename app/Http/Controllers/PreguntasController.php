<?php

namespace App\Http\Controllers;
use App\Lectura;
use App\Seccione;
use App\Examene;
use App\Pregunta;
use Carbon\Carbon;
include "funciones/FuncionesGenerales.php";

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class PreguntasController extends BaseController
{
    function nuevo(Request $request){
    	try{
            //return response()->json($request, 400);
            $preguntas = $request['preguntas'];
            foreach ($preguntas as $pregunta) {
                $question = Pregunta::create([
                    'idSeccion' => (strlen($request['idSeccion']) > 0) ? $request['idSeccion'] : 0,
                    'idLectura' => (strlen($pregunta['idLectura']) > 0) ? $pregunta['idLectura'] : 0,
                    'pregunta' => (strlen($pregunta['pregunta']) > 0) ? $pregunta['pregunta'] : '',
                    'respuesta' => (strlen($pregunta['respuesta']) > 0) ? $pregunta['respuesta'] : '',
                    'tipo' => (strlen($pregunta['tipo']) > 0) ? $pregunta['tipo'] : '',
                    'respuestaA' => (strlen($pregunta['respuestaA']) > 0) ? $pregunta['respuestaA'] : '',
                    'respuestaB' => (strlen($pregunta['respuestaB']) > 0) ? $pregunta['respuestaB'] : '',
                    'respuestaC' => (strlen($pregunta['respuestaC']) > 0) ? $pregunta['respuestaC'] : '',
                    'respuestaD' => (strlen($pregunta['respuestaD']) > 0) ? $pregunta['respuestaD'] : '',
                    'indice' => (strlen($pregunta['indice']) > 0) ? $pregunta['indice'] : '',
                    'activo' => 1,
                    'eliminado' => 0
                ]);
            }
    		return response()->json($preguntas, 200);
    	}catch(Exception $e){
    		return response()->json("Error en el servidor", 400);
    	}
    }

    function imagen(Request $request){
    	try{
            $imagen = Lectura::find($request['id']);
            return response()->json($imagen);
    	}catch(Exception $e){
    		return response()->json("Error en el servidor", 400);
    	}
    }

    function mostrar(Request $request){
        try{
            $examen = Examene::find($request['idExamen']);
            $respuesta['tiempo'] = !mayor(Carbon::now(), $examen->inicio);
            $preguntas = Pregunta::where('idSeccion', '=', $request['idSeccion'])->get();
            $respuesta['preguntas'] = $preguntas;
            return response()->json($respuesta, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            //return response()->json($request, 400);
            $preguntas = $request['preguntas'];
            $si = array();
            $no = array();
            foreach ($preguntas as $pregunta) {
                if(isset($pregunta['id'])){
                    $question = Pregunta::find($pregunta['id']);
                    $question->pregunta = $pregunta['pregunta'];
                    $question->tipo = $pregunta['tipo'];
                    $question->respuesta = $pregunta['respuesta'];
                    $question->respuestaA = $pregunta['respuestaA'];
                    $question->respuestaB = $pregunta['respuestaB'];
                    $question->respuestaC = $pregunta['respuestaC'];
                    $question->respuestaD = $pregunta['respuestaD'];
                    $question->idLectura = $pregunta['idLectura'];
                    $question->save();
                }else{
                    $question = Pregunta::create([
                        'idSeccion' => $request['idSeccion'],
                        'idLectura' => $pregunta['idLectura'],
                        'pregunta' => $pregunta['pregunta'],
                        'respuesta' => $pregunta['respuesta'],
                        'tipo' => $pregunta['tipo'],
                        'respuestaA' => $pregunta['respuestaA'],
                        'respuestaB' => $pregunta['respuestaB'],
                        'respuestaC' => $pregunta['respuestaC'],
                        'respuestaD' => $pregunta['respuestaD'],
                        'indice' => $pregunta['indice'],
                        'activo' => 1,
                        'eliminado' => 0
                    ]);
                }
            }
            $respuesta['si'] = $si;
            $respuesta['no'] = $no;
            return response()->json($respuesta, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }
}