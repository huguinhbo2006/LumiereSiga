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
use App\Metascurso;
use App\Sucursale;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
include "funciones/FuncionesGenerales.php";


class MetascursosController extends BaseController
{
    function mostrar(){
        try {
            $metas = Metascurso::join('sucursales', 'idSucursal', '=', 'sucursales.id')->
            join('calendarios', 'idCalendario', '=', 'calendarios.id')->
            join('niveles', 'idNivel', '=', 'niveles.id')->
            join('subniveles', 'idSubnivel', '=', 'subniveles.id')->
            join('modalidades', 'idModalidad', '=', 'modalidades.id')->
            join('cursos', 'idCurso', '=', 'cursos.id')->
            select(
                'metascursos.*', 
                'calendarios.nombre as calendario',
                'sucursales.nombre as sucursal',
                'niveles.nombre as nivel',
                'subniveles.nombre as subnivel',
                'modalidades.nombre as modalidad',
                'cursos.nombre as curso'
            )->
            where('metascursos.eliminado', '=', 0)->get();

            $altas = Altacurso::join('niveles', 'idNivel', '=', 'niveles.id')->
            join('subniveles', 'idSubnivel', '=', 'subniveles.id')->
            join('calendarios', 'idCalendario', '=', 'calendarios.id')->
            join('modalidades', 'idModalidad', '=', 'modalidades.id')->
            join('cursos', 'idCurso', '=', 'cursos.id')->
            join('sedes', 'idSede', '=', 'sedes.id')->
            select(
                'altacursos.*',
                'niveles.nombre as nivel',
                'subniveles.nombre as subniveles',
                'cursos.nombre as curso',
                'modalidades.nombre as modalidad',
                'calendarios.nombre as calendario',
                'sedes.nombre as sede',
                'cursos.icono'
            )->
            whereRaw("DATE_FORMAT(altacursos.fin,'%y-%m-%d') > CURDATE()")->get();

            $consulta = "SELECT * FROM calendarios WHERE fin > NOW() AND eliminado = 0 AND activo = 1";
            $calendarios = DB::select($consulta, array());
            $sucursales = Sucursale::where('eliminado', '=', 0)->get();
            $niveles = Nivele::where('eliminado', '=', 0)->get();
            $subniveles = Subnivele::where('eliminado', '=', 0)->get();
            $modalidades = Modalidade::where('eliminado', '=', 0)->get();
            $cursos = Curso::where('eliminado', '=', 0)->get();

            $respuesta['listas']['calendarios'] = $calendarios;
            $respuesta['listas']['sucursales'] = $sucursales;
            $respuesta['listas']['metas'] = $metas;
            $respuesta['listas']['niveles'] = $niveles;
            $respuesta['listas']['subniveles'] = $subniveles;
            $respuesta['listas']['modalidades'] = $modalidades;
            $respuesta['listas']['cursos'] = $cursos;
            $respuesta['listas']['altas'] = $altas;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function nuevo(Request $request){
        try {
            $existe = Metascurso::where('idCalendario', '=', $request['idCalendario'])->
            where('idNivel', '=', $request['idNivel'])->
            where('idSubnivel', '=', $request['idSubnivel'])->
            where('idModalidad', '=', $request['idModalidad'])->
            where('idCurso', '=', $request['idCurso'])->
            where('idSucursal', '=', $request['idSucursal'])->get();
            if(count($existe) > 0){
                $meta = $existe[0];
                $meta->calendario = Calendario::find($meta->idCalendario)->nombre;
                $meta->nivel = Nivele::find($meta->idNivel)->nombre;
                $meta->subnivel = Subnivele::find($meta->idSubnivel)->nombre;
                $meta->modalidad = Modalidade::find($meta->idModalidad)->nombre;
                $meta->curso = Curso::find($meta->idCurso)->nombre;
                $meta->sucursal = Sucursale::find($meta->idSucursal)->nombre;

                $mensaje = "Ya existe una meta para el calendario ".$meta->calendario." en el nivel ".$meta->nivel." subnivel ".$meta->subnivel." modalidad ".$meta->modalidad." curso ".$meta->curso." sucursal ".$meta->sucursal;
                return response()->json($mensaje, 400);
            }else{
                $meta = Metascurso::create([
                    'idCalendario' => $request['idCalendario'],
                    'idNivel' => $request['idNivel'],
                    'idSubnivel' => $request['idSubnivel'],
                    'idModalidad' => $request['idModalidad'],
                    'idCurso' => $request['idCurso'],
                    'idSucursal' => $request['idSucursal'],
                    'meta' => $request['meta'],
                    'eliminado' => 0,
                    'activo' => 1
                ]);

                $meta->calendario = Calendario::find($meta->idCalendario)->nombre;
                $meta->nivel = Nivele::find($meta->idNivel)->nombre;
                $meta->subnivel = Subnivele::find($meta->idSubnivel)->nombre;
                $meta->modalidad = Modalidade::find($meta->idModalidad)->nombre;
                $meta->curso = Curso::find($meta->idCurso)->nombre;
                $meta->sucursal = Sucursale::find($meta->idSucursal)->nombre;

                return response()->json($meta, 200);
            }
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request){
        try {
            $meta = Metascurso::find($request['id']);
            $meta->meta = $request['meta'];
            $meta->save();
            $meta->calendario = Calendario::find($meta->idCalendario)->nombre;
            $meta->nivel = Nivele::find($meta->idNivel)->nombre;
            $meta->subnivel = Subnivele::find($meta->idSubnivel)->nombre;
            $meta->modalidad = Modalidade::find($meta->idModalidad)->nombre;
            $meta->curso = Curso::find($meta->idCurso)->nombre;
            $meta->sucursal = Sucursale::find($meta->idSucursal)->nombre;

            return response()->json($meta, 200);
        } catch (Exception $e) {
            
        }
    }

    function eliminar(Request $request){
        try {
            $meta = Metascurso::find($request['id']);
            $meta->delete();
            $meta->calendario = Calendario::find($meta->idCalendario)->nombre;
            $meta->nivel = Nivele::find($meta->idNivel)->nombre;
            $meta->subnivel = Subnivele::find($meta->idSubnivel)->nombre;
            $meta->modalidad = Modalidade::find($meta->idModalidad)->nombre;
            $meta->curso = Curso::find($meta->idCurso)->nombre;
            $meta->sucursal = Sucursale::find($meta->idSucursal)->nombre;

            return response()->json($meta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}