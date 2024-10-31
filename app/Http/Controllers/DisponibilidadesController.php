<?php

namespace App\Http\Controllers;
use App\Disponibilidade;
use App\Informacioncurso;
use App\Grupo;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class DisponibilidadesController extends BaseController
{
    function informacion(Request $request){
        try{
            $cursos = Grupo::where('idHorario', '=', $request['idHorario'])->get();
            $respuesta = array();
            foreach ($cursos as $curso) {
                $ic = Informacioncurso::join('cursos', 'idCurso', '=', 'cursos.id')->select('informacioncursos.*', 'cursos.nombre as curso', 'cursos.icono')->where('informacioncursos.id', '=', $curso->idInformacionCurso)->where('informacioncursos.idModalidad', '=', $request['idModalidad'])->where('informacioncursos.idCalendario', '=', $request['idCalendario'])->get();
                $ic[0]->grupo = $curso->id;
                $respuesta[] = $ic[0];
            }
            return response()->json($respuesta, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function agregar(Request $request){
        try{
            $disponibilidad = Disponibilidade::create([
                'idGrupo' => $request['grupo'],
                'idAula' => $request['idAula'],
                'activo' => 1,
                'eliminado' => 0
            ]);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }
}