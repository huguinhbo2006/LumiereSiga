<?php

namespace App\Http\Controllers;
use App\Alumno;
use App\Egreso;
use App\Calendario;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
include "funciones/imagenes.php";
include "funciones/FuncionesGenerales.php";

class SoporteController extends BaseController
{
    function datosAlumno(Request $request){
        try{
            $alumno = Alumno::find($request['id']);
            return response()->json($alumno, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificarAlumno(Request $request){
        try {
            $existe = Alumno::where('codigo', '=', $request['codigo'])->get();
            if(count($existe) > 0)
                return response()->json('El alumno con el codigo '.$request['codigo'].'. Ya existe', 400);

            $alumno = Alumno::find($request['id']);
            $alumno->nombre = $request['nombre'];
            $alumno->apellidoPaterno = $request['apellidoPaterno'];
            $alumno->apellidoMaterno = $request['apellidoMaterno'];
            $alumno->fechaNacimiento = $request['fechaNacimiento'];
            $alumno->codigo = $request['codigo'];
            $alumno->save();
            return response()->json($alumno, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}