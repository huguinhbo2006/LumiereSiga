<?php

namespace App\Http\Controllers;
use App\Tipopago;
use App\Comisioncurso;
use App\Calendario;
use App\Curso;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class ConfiguracionesventasController extends BaseController
{
    function traerComisionesColbach(){
        try {
            $comisionesCOLBACH = Tipopago::where('eliminado', '=', 0)->get();
            return response()->json($comisionesCOLBACH, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function nuevaComisionColbach(Request $request){
        try {
            $comision = Tipopago::create([
                'nombre' => $request['nombre'],
                'comision' => $request['comision'],
                'corte' => $request['corte'],
                'valeCorte' => $request['valeCorte'],
                'eliminado' => 0,
                'activo' => 1
            ]);
            return response()->json($comision, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminarComisionColbach(Request $request){
        try {
            $comision = Tipopago::find($request['id']);
            $comision->eliminado = 1;
            $comision->save();
            return response()->json($comision, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function selects(){
        try {
            $respuesta['calendarios'] = Calendario::all();
            $respuesta['cursos'] = Curso::all();
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function nuevaComisionCurso(Request $request){
        try {
            $existe = Comisioncurso::where('idCalendario', '=', $request['idCalendario'])->
            where('idCurso', '=', $request['idCurso'])->get();
            if(count($existe) > 0){
                return response()->json('Ya existe comision para este curso en este calendario', 400);
            }
            $comision = Comisioncurso::create([
                'idCalendario' => $request['idCalendario'],
                'idCurso' => $request['idCurso'],
                'tipo' => $request['tipo'],
                'comision' => $request['comision'],
                'eliminado' => 0,
                'activo' => 1
            ]);
            return response()->json($comision, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function traerComisionesCurso(Request $request){
        try {
            $comisiones = Comisioncurso::join('calendarios', 'idCalendario', '=', 'calendarios.id')->
                                         join('cursos', 'idCurso', '=', 'cursos.id')->
                                         select('comisioncursos.*', 'calendarios.nombre as calendario', 'cursos.nombre as curso')->
                                         where('comisioncursos.eliminado', '=', 0)->
                                         where('comisioncursos.idCalendario', '=', $request['idCalendario'])->get();
            $respuesta = array();
            foreach ($comisiones as $comision) {
                if(intval($comision->tipo) === 1){
                    $comision->comision = $comision->comision.' %';
                }else{
                    $comision->comision = '$'.$comision->comision;
                }
                $respuesta[] = $comision;
            }
            return response()->json($comisiones, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificarComisionCurso(Request $request){
        try {
            $comision = Comisioncurso::find($request['id']);
            $comision->comision = $request['comision'];
            $comision->tipo = $request['tipo'];
            $comision->save();
            return response()->json($comision, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminarComisionCurso(Request $request){
        try {
            $comision = Comisioncurso::find($request['id']);
            $comision->delete();
            return response()->json($comision, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}