<?php

namespace App\Http\Controllers;
use App\Reservacionesgrupo;
use Carbon\Carbon;
include "funciones/FuncionesReservacionGrupos.php";

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservacionesgruposController extends BaseController
{
    function nueva(Request $request){
        try{
            $existe = Reservacionesgrupo::where('idGrupo', '=', $request['idGrupo'])->where('idReservacion', '=', $request['idReservacion'])->get();
            if(count($existe) > 0){
                if($existe[0]->eliminado === 1){
                    $existe[0]->eliminado = 0;
                    $existe[0]->save();
                    return response()->json($existe[0], 200);
                }else{
                    return response()->json("Ya existe el registro", 400);
                }
            }else{
                $reservacion = Reservacionesgrupo::create([
                    'idGrupo' => $request['idGrupo'],
                    'idReservacion' => $request['idReservacion'],
                    'activo' => 1,
                    'eliminado' => 0
                ]);
                return response()->json($reservacion, 200);
            }
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function posibles(Request $request){
        try{
            $datos = array();
            $consulta = "SELECT i.inicio, i.fin, i.idNivel, i.idSubnivel, i.idCurso, i.idModalidad, i.idCalendario, g.*, t.nombre as turno, CONCAT(h.inicio, ' - ', h.fin) AS horario, c.nombre AS curso, c.icono, n.nombre AS nivel, s.nombre AS subnivel, m.nombre AS modalidad, ca.nombre AS calendario FROM informacioncursos i, grupos g, turnos t, horarios h, cursos c, niveles n, subniveles s, modalidades m, calendarios ca WHERE g.eliminado = 0 AND g.idInformacionCurso = i.id AND g.idTurno = t.id AND g.idHorario = h.id AND i.idCurso = c.id AND i.idNivel = n.id AND i.idSubnivel = s.id AND i.idModalidad = m.id AND i.idCalendario = ca.id ";

            if(strlen($request['modalidad']) > 0 && $request['modalidad'] !== "0") {
                $modalidad = $request['modalidad'];
                $consulta .= "AND m.id = $modalidad ";
            }
            if(strlen($request['calendario']) > 0 && $request['calendario'] !== "0") {
                $calendario = $request['calendario'];
                $consulta .= "AND ca.id = $calendario ";
            }
            if(strlen($request['curso']) > 0 && $request['curso'] !== "0") {
                $curso = $request['curso'];
                $consulta .= "AND c.id = $curso ";
            }
            if(strlen($request['turno']) > 0 && $request['turno'] !== "0") {
                $turno = $request['turno'];
                $consulta .= "AND t.id = $turno "; 
            }
            if(strlen($request['horario']) > 0 && $request['horario'] !== "0") {
                $horario = $request['horario'];
                $consulta .= "AND h.id = $horario ";
            }
            if(strlen($request['inicio']) > 5 && strlen($request['fin']) > 5) {
                $inicio = $request['inicio'];
                $fin = $request['fin'];
                $consulta .= "AND i.inicio = '$inicio' AND i.fin = '$fin'";
            }
            $registros = DB::select($consulta, $datos);
            $final = array();
            foreach ($registros as $registro) {
                $existe = Reservacionesgrupo::where('idGrupo', '=', $registro->id)->where('eliminado', '=', 0)->where('idReservacion', '=', $request['reservacion'])->get();
                if(count($existe) <= 0){
                    $final[] = $registro;
                }
            }
            return response()->json($final, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function agregados(Request $request){
        try{
            $grupos = Reservacionesgrupo::where('idReservacion', '=', $request['id'])->where('eliminado', '=', 0)->get();
            $final = array();
            foreach ($grupos as $grupo) {
                $dato = traerInformacionGrupo($grupo->idGrupo, $grupo->id);
                $final[] = $dato[0];
            }
            return response()->json($final, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $reservacion = Reservacionesgrupo::find($request['id']);
            $reservacion->eliminado = 1;
            $reservacion->save();
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }
}