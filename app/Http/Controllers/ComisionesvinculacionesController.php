<?php

namespace App\Http\Controllers;
use App\Usuario;
use App\Nivele;
use App\Ficha;
use Carbon\Carbon;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
include "funciones/FuncionesGenerales.php";
include "funciones/funcionesBaseDatos.php";

class ComisionesvinculacionesController extends BaseController
{
    function listas(){
        try {
            $empleados = Usuario::join('empleados', 'idEmpleado', '=', 'empleados.id')->
                                  select('usuarios.id as id', 'empleados.nombre as nombre')->
                                  where('empleados.idDepartamento', '=', 6)->
                                  where('empleados.idPuesto', '=', 25)->get();
            $actual = date("Y");
            $principal = 2021;
            $anios = array();
            for ($i=2021; $i < $actual+1; $i++) { 
                $res['nombre'] = $i;
                $res['id'] = $i;
                $anios[] = $res;
            }
            $niveles = Nivele::where('activo', '=', 1)->get();
            $respuesta['empleados'] = $empleados;
            $respuesta['years'] = $anios;
            $respuesta['niveles'] = $niveles;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function comisiones(Request $request){
        try {
            $fichas = Ficha::leftjoin('alumnos', 'idAlumno', '=', 'alumnos.id')->
            leftjoin('grupos', 'idGrupo', '=', 'grupos.id')->
            leftjoin('altacursos', 'grupos.idAltaCurso', '=', 'altacursos.id')->
            leftjoin('alumnodescuentos', 'fichas.id', '=', 'alumnodescuentos.idFicha')->
            select(
                'fichas.id',
                DB::raw('CONCAT(alumnos.nombre, " ", alumnos.apellidoPaterno, " ", alumnos.apellidoMaterno) as nombre'),
                'altacursos.precio',
                DB::raw('(SELECT SUM(monto) FROM alumnodescuentos WHERE idFicha = fichas.id) as descuento'),
                DB::raw("(
                    altacursos.precio -
                    IF((SELECT SUM(monto) FROM alumnodescuentos WHERE idFicha = fichas.id AND eliminado = 0 LIMIT 1) IS NULL, 0, (SELECT SUM(monto) FROM alumnodescuentos WHERE idFicha = fichas.id AND eliminado = 0 LIMIT 1))
                ) as final")
            )->where('fichas.idUsuarioInformacion', '=', $request['idEmpleado'])->
            whereRaw('MONTH(fichas.fecha) = '.$request['mes'])->get();
            return response()->json($fichas, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}