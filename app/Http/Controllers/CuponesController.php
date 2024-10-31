<?php

namespace App\Http\Controllers;
use App\Cupone;
use App\Ficha;
use App\Alumno;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class CuponesController extends BaseController
{
    function cursosCongelados(Request $request){
        try {
            $cupones = Cupone::where('idFicha', '<>', 0)->get();
            $respuesta = array();
            foreach ($cupones as $cupon) {
                $ficha = Ficha::find($cupon->idFicha);
                if(intval($ficha->idCalendario) === intval($request['id'])){
                    $cupon->idFicha = $ficha->id;
                    $cupon->ficha = $ficha->folio;
                    $alumno = Alumno::find($ficha->idAlumno);
                    $cupon->alumno = $alumno->nombre.' '.$alumno->apellidoPaterno.' '.$alumno->apellidoMaterno;
                    $respuesta[] = $cupon;
                }
            }
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}