<?php

namespace App\Http\Controllers;
use App\Bloqueohorario;
use App\Grupo;
use App\Sucursale;
use App\Calendario;
use App\Nivele;
use App\Subnivele;
use App\Categoria;
use App\Modalidade;
use App\Curso;
use App\Sede;
use App\Turno;
use App\Horario;
use App\Sedesucursale;
use Carbon\Carbon;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
include "funciones/FuncionesGenerales.php";

class BloqueohorariosController extends BaseController
{
    function bloquear(Request $request){
        try {
            $existe = Bloqueohorario::where('idGrupo', '=', $request['id'])->where('idSucursal', '=', $request['sucursalID'])->get();
            if(count($existe) > 0){
                foreach ($existe as $eliminar) {
                    $del = Bloqueohorario::find($eliminar->id);
                    $del->delete();
                }
            }
            $bloqueo = Bloqueohorario::create([
                'idGrupo' => $request['id'],
                'idSucursal' => $request['sucursalID'],
                'activo' => 1,
                'eliminado' => 0
            ]);

            return response()->json($bloqueo, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function desbloquear(Request $request){
        try {
            $bloqueo = Bloqueohorario::find($request['idBloqueo']);
            $bloqueo->delete();

            return response()->json($bloqueo, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function mostrar(Request $request){
        try {
            $grupos = Grupo::join('altacursos', 'idAltaCurso', '=', 'altacursos.id')->
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
            whereRaw('NOW() BETWEEN calendarios.inicio AND calendarios.fin')->get();
            $sucursales = Sucursale::where('eliminado', '=', 0)->get();

            $respuesta['grupos'] = $grupos;
            $respuesta['sucursales'] = $sucursales;
            $respuesta['calendarios'] = Calendario::whereRaw("NOW() BETWEEN inicio AND fin")->get();
            $respuesta['niveles'] = Nivele::where('eliminado', '=', 0)->get();
            $respuesta['subniveles'] = Subnivele::where('eliminado', '=', 0)->get();
            $respuesta['categorias'] = Categoria::where('eliminado', '=', 0)->get();
            $respuesta['modalidades'] = Modalidade::where('eliminado', '=', 0)->get();
            $respuesta['cursos'] = Curso::where('eliminado', '=', 0)->get();
            $respuesta['sedes'] = Sede::where('eliminado', '=', 0)->get();
            $respuesta['turnos'] = Turno::where('eliminado', '=', 0)->get();
            $selectHorarios = "SELECT CONCAT(inicio, ' - ', fin) as nombre, id FROM horarios WHERE eliminado = 0";
            $respuesta['horarios'] = DB::select($selectHorarios, array());
            $respuesta['sedesucursales'] = Sedesucursale::where('eliminado', '=', 0)->get();
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}