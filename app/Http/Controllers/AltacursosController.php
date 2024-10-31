<?php

namespace App\Http\Controllers;
use App\Altacurso;
use App\Nivele;
use App\Subnivele;
use App\Curso;
use App\Modalidade;
use App\Calendario;
use App\Categoria;
use App\Sede;
use App\Clases\Altacursos;
use App\Clases\Fechas;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class AltacursosController extends BaseController
{
    function nuevo(Request $request) {
        try {
            $funciones = new Altacursos();
            $validacion = $funciones->validarCurso($request);

            if(is_null($validacion['error'])){
                return response()->json('Error en la validacion del curso', 400);
            }

            if($validacion['error']){
                return response()->json($validacion['mensaje'], 400);
            }
            
            $curso = Altacurso::create([
                'idCalendario' => $request['idCalendario'],
                'idNivel' => $request['idNivel'],
                'idSubnivel' => $request['idSubnivel'],
                'idCurso' => $request['idCurso'],
                'idModalidad' => $request['idModalidad'],
                'idCategoria' => $request['idCategoria'],
                'idSede' => $request['idSede'],
                'inicio' => $request['inicio'],
                'fin' => $request['fin'],
                'limitePago' => $request['limitePago'],
                'precio' => $request['precio'],
                'activo' => 1,
                'eliminado' => 0
            ]);

            $curso = $funciones->complementarCurso($curso);

            return (is_null($curso)) ? response()->json('Error al completar curso', 400) : response()->json($curso, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function mostrar() {
        try {
            $funciones = new Altacursos();
            $respuesta['datos'] = Altacurso::join('niveles', 'idNivel', '=', 'niveles.id')->
            join('subniveles', 'idSubnivel', '=', 'subniveles.id')->
            join('calendarios', 'idCalendario', '=', 'calendarios.id')->
            join('categorias', 'idCategoria', '=', 'categorias.id')->
            join('modalidades', 'idModalidad', '=', 'modalidades.id')->
            join('cursos', 'idCurso', '=', 'cursos.id')->
            join('sedes', 'idSede', '=', 'sedes.id')->
            select(
                'altacursos.*',
                'niveles.nombre as nivel',
                'subniveles.nombre as subnivel',
                'cursos.nombre as curso',
                'modalidades.nombre as modalidad',
                'categorias.nombre as categoria',
                'calendarios.nombre as calendario',
                'sedes.nombre as sede',
                'cursos.icono'
            )->
            where('altacursos.eliminado', '=', 0)->
            whereRaw("DATE_FORMAT(altacursos.fin,'%y-%m-%d') > CURDATE()")->get();;
            
            $respuesta['listas'] = $funciones->listas();
            
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request) {
        try {
            $curso = Altacurso::find($request['id']);
            $curso->precio = $request['precio'];
            $curso->inicio = $request['inicio'];
            $curso->fin = $request['fin'];
            $curso->limitePago = $request['limitePago'];
            $curso->save();

            return response()->json($curso, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request){
        try {
            $curso = Altacurso::find($request['id']);
            $curso->delete();
            return response()->json($curso, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}