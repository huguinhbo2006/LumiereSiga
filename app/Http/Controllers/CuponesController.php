<?php

namespace App\Http\Controllers;
use App\Cupone;
use App\Ficha;
use App\Alumno;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CuponesController extends BaseController
{
    function cursosCongelados(Request $request){
        try {
            $cupones = Cupone::join('fichas', 'cupones.idFicha', '=', 'fichas.id')->
            join('alumnos', 'fichas.idAlumno', '=', 'alumnos.id')->
            select([
                'fichas.id as idFicha',
                'fichas.folio as ficha',
                'alumnos.id as idAlumno',
                DB::raw("CONCAT(alumnos.nombre, ' ', alumnos.apellidoPaterno, ' ', alumnos.apellidoMaterno) as alumno"),
                'cupones.monto as monto',
                'cupones.cupon as codigo'
            ])->where('cupones.idFicha', '<>', 0)->
            where('fichas.idCalendario', '=', $request['idFicha'])->get();
            return response()->json($cupones, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}