<?php

namespace App\Http\Controllers;
use App\Empleado;
use App\Asignacionesprofesore;
use Carbon\Carbon;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
include "funciones/FuncionesGrupo.php";
include "funciones/FuncionesGenerales.php";

class AsignacionesprofesoresController extends BaseController
{
    function asignacion(Request $request){
        try{
            $gruposParidades = traerGruposParidad($request['idGrupo']);
            foreach ($gruposParidades as $otros) {
                $calendario = calendarioGrupo($otros);
                $existe = Asignacionesprofesore::where('idProfesor', '=', $request['idProfesor'])->
                                                 where('idSucursal', '=', $request['idSucursal'])->
                                                 where('idGrupo', '=', $otros)->get();
                if(count($existe) > 0){
                    return response()->json('El profesor ya fue asignado a este grupo', 400);
                }else{
                    $asignacion = Asignacionesprofesore::create([
                        'idProfesor' => $request['idProfesor'],
                        'idCalendario' => $calendario->id,
                        'idSucursal' => $request['idSucursal'],
                        'idGrupo' => $otros,
                        'eliminado' => 0,
                        'activo' => 1
                    ]);
                }
            }

            return response()->json('Todo bien', 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function mostrar(Request $request){
        try {
            $asignaciones = Asignacionesprofesore::where('idSucursal', '=', $request['idSucursal'])->where('idCalendario', '=', $request['idCalendario'])->where('idGrupo', '=', $request['idGrupo'])->get();
            $respuesta = array();
            foreach ($asignaciones as $asignacion) {
                $profesor = Empleado::join('puestos', 'idPuesto', '=', 'puestos.id')->
                                      select('empleados.*', 'puestos.nombre as descripcion')->
                                      where('empleados.activo', '=', 1)->
                                      where('empleados.eliminado', '=', 0)->
                                      where('empleados.idDepartamento', '=', 1)->
                                      where('empleados.id', '=', $asignacion->idProfesor)->get()[0];
                $asignacion->nombre = $profesor->nombre;
                $respuesta[] = $asignacion;
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
                $asignacion = Asignacionesprofesore::where('idProfesor', '=', $request['idProfesor'])->
                                                  where('idSucursal', '=', $request['idSucursal'])->
                                                  where('idGrupo', '=', $grupo)->get();
                $eliminar = Asignacionesprofesore::find($asignacion[0]->id);
                $eliminar->delete();
            }

            return response()->json($asignacion, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function activos(Request $request){
        try {
            $profesores = Empleado::join('puestos', 'idPuesto', '=', 'puestos.id')->select('empleados.*', 'puestos.nombre as descripcion')->
            where('empleados.activo', '=', 1)->where('empleados.eliminado', '=', 0)->where('empleados.idDepartamento', '=', 1)->get();
            return response()->json($profesores, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}