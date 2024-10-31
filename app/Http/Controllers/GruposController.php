<?php

namespace App\Http\Controllers;
use App\Grupo;
use App\Horario;
use App\Altacurso;
use App\Turno;


use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GruposController extends BaseController
{
    function nuevo(Request $request){
        try {
            $existe = Grupo::where('idAltaCurso', '=', $request['idAltaCurso'])->
                    where('idTurno', '=', $request['idTurno'])->
                    where('idHorario', '=', $request['idHorario'])->get();
            if(count($existe) > 0){
                if($existe[0]->eliminado === 1){
                    $existe[0]->eliminado = 0;
                    $existe[0]->save();
                    $horario = Horario::find($existe[0]->idHorario);
                    $existe[0]->nombre = $horario->inicio.' - '.$horario->fin;
                    return response()->json($existe[0], 200);    
                }else{
                    return response()->json('El grupo ya existe', 400);
                }
            }else{
                $grupo = Grupo::create([
                    'idAltaCurso' => $request['idAltaCurso'],
                    'idTurno' => $request['idTurno'],
                    'idHorario' => $request['idHorario'],
                    'eliminado' => 0,
                    'activo' => 1
                ]);
                $horario = Horario::find($grupo->idHorario);
                $grupo->nombre = $horario->inicio.' - '.$horario->fin;

                return response()->json($grupo, 200);
            }
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function mostrar(Request $request){
        try {
            $respuesta['listas']['grupos'] = Grupo::join('horarios', 'idHorario', '=', 'horarios.id')->
            select(
                DB::raw('CONCAT(horarios.inicio, " - ", horarios.fin) as nombre'),
                'grupos.*'
            )->
            where('grupos.eliminado', '=', 0)->
            where('grupos.idAltaCurso', '=', $request['idAltaCurso'])->get();
            $respuesta['listas']['turnos'] = Turno::where('eliminado', '=', 0)->get();
            $respuesta['listas']['horarios'] = Horario::select(
                DB::raw('CONCAT(horarios.inicio, " - ", horarios.fin) as nombre'),
                'horarios.id',
                'horarios.idTurno'
            )->where('eliminado', '=', 0)->get();

            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request){
        try {
            $grupo = Grupo::find($request['id']);
            $grupo->delete();

            return response()->json($grupo, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function obtener(Request $request) {
        try {
            $calendario = $request['idCalendario'];
            $consulta = "SELECT  g.*, ac.idCalendario, ac.idNivel, ac.idSubnivel, ac.idModalidad, ac.idCategoria, ac.idCurso, ac.inicio, ac.fin, ac.limitePago, ac.precio, c.nombre as calendario, t.nombre as turno, n.nombre as nivel, s.nombre as subnivel, m.nombre as modalidad, cat.nombre as categorias, CONCAT(h.inicio,'-',h.fin) as horario, cr.nombre as curso, cr.icono as icono, se.nombre as sede
            FROM altacursos ac, grupos g, calendarios c, turnos t, niveles n, subniveles s, modalidades m, categorias cat, cursos cr, horarios h, sedes se
            WHERE ac.idCalendario = c.id AND ac.idCurso = cr.id AND ac.idNivel = n.id AND ac.idSubnivel = s.id AND ac.idModalidad = m.id AND ac.idCategoria = cat.id AND ac.idSede = se.id AND g.idHorario = h.id AND g.idTurno = t.id AND g.idAltaCurso = ac.id AND g.eliminado = 0 AND ac.idCalendario = $calendario";
            $registros = DB::select($consulta, array());
            return response()->json($registros, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}