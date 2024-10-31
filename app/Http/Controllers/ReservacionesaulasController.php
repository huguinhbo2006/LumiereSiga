<?php

namespace App\Http\Controllers;
use App\Reservacionesaula;
use App\Asignacionesprofesore;
use App\Paridade;
use App\Calendario;
use Carbon\Carbon;
use App\Aula;
use App\Nivele;
use App\Subnivele;
use App\Modalidade;
use App\Categoria;
use App\Sede;
use App\Sedesucursale;
use App\Altacurso;
use App\Cursosparidade;
use App\Grupo;
use App\Ficha;
use App\Horario;
use App\Empleado;
use App\Usuario;
use App\Sucursale;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
include "funciones/FuncionesGrupo.php";
include "funciones/FuncionesGenerales.php";

class ReservacionesaulasController extends BaseController
{
    function mostrar(Request $request){
        try{
            $calendario = $request['idCalendario'];
            $consulta = "SELECT  g.*, ac.idCalendario, ac.idNivel, ac.idSubnivel, ac.idModalidad, ac.idCategoria, ac.idCurso, ac.inicio, ac.fin, ac.limitePago, ac.precio, c.nombre as calendario, t.nombre as turno, n.nombre as nivel, s.nombre as subnivel, m.nombre as modalidad, cat.nombre as categoria, CONCAT(h.inicio,'-',h.fin) as horario, cr.nombre as curso, cr.icono as icono, se.nombre as sede
            FROM altacursos ac, grupos g, calendarios c, turnos t, niveles n, subniveles s, modalidades m, categorias cat, cursos cr, horarios h, sedes se
            WHERE ac.idCalendario = c.id AND ac.idCurso = cr.id AND ac.idNivel = n.id AND ac.idSubnivel = s.id AND ac.idModalidad = m.id AND ac.idCategoria = cat.id AND ac.idSede = se.id AND g.idHorario = h.id AND g.idTurno = t.id AND g.idAltaCurso = ac.id AND g.eliminado = 0 AND NOW() BETWEEN c.inicio AND c.fin";
            $registros = DB::select($consulta, array());
            $respuesta = array();
            foreach ($registros as $registro) {
                $reservaciones = Reservacionesaula::where('idGrupo', '=', $registro->id)->where('idSucursal', '=', $request['idSucursal'])->get();

                $profesores = Asignacionesprofesore::where('idGrupo', '=', $registro->id)->where('idSucursal', '=', $request['idSucursal'])->get();
                $teachers = array();
                foreach ($profesores as $profesor) {
                    $empleado = Empleado::find($profesor->idProfesor);
                    $prof['nombre'] = $empleado->nombre;
                    $usuario = Usuario::where('idEmpleado', '=', $empleado->id)->get();
                    (count($usuario) > 0) ? $prof['imagen'] = $usuario[0]->foto : $prof['imagen'] = '../assets/profesor.png';
                    $teachers[] = $prof;
                }
                $registro->profesores = $teachers;
                $registro->aulas = count($reservaciones);
                $respuesta[] = $registro;
            }
            return response()->json($respuesta, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function reservacion(Request $request){
        try{
            $reservaciones = Reservacionesaula::where('idSucursal', '=', $request['idSucursal'])->
                                                where('idCalendario', '=', $request['idCalendario'])->
                                                where('idAula', '=', $request['idAula'])->get();
            $grupo = $request['idGrupo'];
            $modalidadGrupo = modalidadGrupo($grupo);
            $altaGrupo = altaGrupo($grupo);
            $horarioGrupo = horarioGrupo($grupo);

            foreach ($reservaciones as $reservacion) {
                $alta = altaGrupo($reservacion->idGrupo);
                $modalidad = modalidadGrupo($alta->idModalidad);
                $horario = horarioGrupo($reservacion->idGrupo);

                if($altaGrupo->idModalidad === 1){
                    if($altaGrupo->idModalidad === $alta->idModalidad){
                        if(!compararDisponibilidades($altaGrupo->inicio, $alta->inicio, $altaGrupo->fin, $alta->fin)){
                            if(!compararDisponibilidades($horarioGrupo->inicio, $horario->inicio, $horarioGrupo->fin, $horario->fin)){
                                return response()->json('Aula ya reservada', 400);
                            }
                        }    
                    }
                }else if($altaGrupo->idModalidad === 2){
                    if($alta->idModalidad !== 1){
                        if(!compararDisponibilidades($altaGrupo->inicio, $alta->inicio, $altaGrupo->fin, $alta->fin)){
                            if(!compararDisponibilidades($horarioGrupo->inicio, $horario->inicio, $horarioGrupo->fin, $horario->fin)){
                                return response()->json('Aula ya reservada', 400);
                            }
                        }
                    }
                }else{
                    if($alta->idModalidad !== 1){
                        if($alta->idModalidad === 2 || $alta->idModalidad === $altaGrupo->idModalidad){
                            if(!compararDisponibilidades($altaGrupo->inicio, $alta->inicio, $altaGrupo->fin, $alta->fin)){
                                if(!compararDisponibilidades($horarioGrupo->inicio, $horario->inicio, $horarioGrupo->fin, $horario->fin)){
                                    return response()->json('Aula ya reservada', 400);
                                }
                            }       
                        }
                    }
                }
            }
            $gruposParidades = traerGruposParidad($grupo);
            foreach ($gruposParidades as $otros) {
                $calendarioGrupo = calendarioGrupo($otros);
                $reservacion = Reservacionesaula::create([
                    'idAula' => $request['idAula'],
                    'idCalendario' => $calendarioGrupo->id,
                    'idSucursal' => $request['idSucursal'],
                    'idGrupo' => $otros,
                    'eliminado' => 0,
                    'activo' => 1
                ]);
            }

            return response()->json($reservacion, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function mostrarReservaciones(Request $request){
        try {
            $reservaciones =Reservacionesaula::where('idGrupo', '=', $request['idGrupo'])->where('idSucursal', '=', $request['idSucursal'])->get();
            $respuesta = array();

            foreach ($reservaciones as $reservacion) {
                $aula = Aula::find($reservacion->idAula);
                $reservacion->nombre = $aula->nombre;
                $reservacion->cupo = $aula->cupo;
                $respuesta[] = $reservacion;
            }
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request){
        try {
            $gruposParidades = traerGruposParidad($request['idGrupo']);
            foreach ($gruposParidades as $grupo) {
                $reservacion = Reservacionesaula::where('idAula', '=', $request['idAula'])->
                                                  where('idSucursal', '=', $request['idSucursal'])->
                                                  where('idGrupo', '=', $grupo)->get();
                $eliminar = Reservacionesaula::find($reservacion[0]->id);
                $eliminar->delete();
            }

            return response()->json($reservacion, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function listas() {
        try {
            $consulta = "SELECT * FROM calendarios WHERE fin > NOW() AND eliminado = 0 AND activo = 1";
            $respuesta['calendarios'] = DB::select($consulta, array());
            $respuesta['paridades'] = Paridade::where('eliminado', '=', 0)->get();
            $respuesta['niveles'] = Nivele::where('eliminado', '=', 0)->get();
            $respuesta['subniveles'] = Subnivele::where('eliminado', '=', 0)->get();
            $respuesta['categorias'] = Categoria::where('eliminado', '=', 0)->get();
            $respuesta['modalidades'] = Modalidade::where('eliminado', '=', 0)->get();
            $respuesta['sucursales'] = Sucursale::where('eliminado', '=', 0)->get();
            $respuesta['sedes'] = Sede::where('eliminado', '=', 0)->get();
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function horarios(Request $request) {
        try {
            $cursos = Cursosparidade::where('idParidad', '=', $request['idParidad'])->get();
            $anteriores = 0;
            $grupos = array();
            $altas = array();
            $respuestas = array();

            foreach ($cursos as $curso) {
                $alta = Altacurso::where('idCalendario', '=', $request['idCalendario'])->
                               where('idNivel', '=', $request['idNivel'])->
                               where('idSubnivel', '=', $request['idSubnivel'])->
                               where('idModalidad', '=', $request['idModalidad'])->
                               where('idCategoria', '=', $request['idCategoria'])->
                               where('idSede', '=', $request['idSede'])->
                               where('idCurso', '=', $curso->idCurso)->get();
                if(count($alta) > 0){
                    $grupo = Grupo::where('idAltaCurso', '=', $alta[0]->id)->get();
                    if(count($grupo) > $anteriores){
                        $anteriores = count($grupo);
                        $grupos = $grupo;
                    }
                    $altas[] = $alta[0];
                }
            }

            foreach ($grupos as $group) {
                $respuesta = array();
                $paridad = Cursosparidade::where('idCurso', '=', $altas[0]->id)->get();
                $cupo = 0;
                $inscritos = 0;
                $lugares = 0;
                $reservaciones = Reservacionesaula::where('idGrupo', '=', $group->id)->
                                                    where('idSucursal', '=', $request['idSucursal'])->get();
                if(count($reservaciones) < 1){
                    //return response()->json('No se ha reservado un aula para este grupo', 400);
                }
                
                foreach ($reservaciones as $reservacion) {
                    $aula = Aula::find($reservacion->idAula);
                    $cupo = intval($cupo) + intval($aula->cupo);
                }
                $paridadesGrupo = traerGruposParidad($group->id);
                $cursos = array();
                foreach ($paridadesGrupo as $grupo) {
                    $fichas = Ficha::where('idSucursalImparticion', '=', $request['idSucursal'])->
                                     where('idGrupo', '=', $grupo)->
                                     where('estatus', '=', 1)->get();
                    $inscritos = intval($inscritos) + intval(count($fichas));
                    $other['nombre'] = count($fichas);
                    $imagen = imagenGrupo($grupo);
                    $other['imagen'] = (strlen($imagen) > 0) ? $imagen : '../assets/profesor.png';
                    $other['fecha'] = fechaInicioGrupo($grupo);
                    $cursos[] = $other;
                }
                burbuja($cursos);
                $respuesta['cupo'] = $cupo;
                $respuesta['inscritos'] = $inscritos;
                $respuesta['lugares'] = (intval($cupo) - intval($inscritos));
                $respuesta['profesores'] = $cursos;
                $horario = Horario::find($group->idHorario);
                $respuesta['horario'] = $horario->inicio.' - '.$horario->fin;
                $respuestas[] = $respuesta;
            }
            return response()->json($respuestas, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}