<?php

namespace App\Http\Controllers;
use App\Cursosparidade;
use App\Curso;
use App\Paridade;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class CursosparidadesController extends BaseController
{
    function mostrar(Request $request){
        try {
            $respuesta['cursos'] = Curso::select('nombre', 'id')->where('eliminado', '=', 0)->get();
            $respuesta['paridades'] = Cursosparidade::where('eliminado', '=', 0)->get();
            $respuesta['agregados'] = Cursosparidade::join('cursos', 'idCurso', '=', 'cursos.id')->
            select(
                'cursos.*',
                'cursosparidades.idCurso'
            )->where('cursosparidades.idParidad', '=', $request['idParidad'])->get();

            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function nuevo(Request $request){
        try {
            $curso = Cursosparidade::create([
                'idParidad' => $request['idParidad'],
                'idCurso' => $request['idCurso'],
                'eliminado' => 0,
                'activo' => 1
            ]);

            return response()->json($curso, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request){
        try {
            $curso = Cursosparidade::where('idParidad', '=', $request['idParidad'])->where('idCurso', '=', $request['idEliminado'])->get()[0];
            $curse = Cursosparidade::find($curso->id);
            $curso->delete();

            return response()->json($curse, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}