<?php

namespace App\Http\Controllers;
use App\Aula;
use App\Sucursale;
use App\Calendario;
use App\Aulasdisponible;
use App\Modalidade;


use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class AulasdisponiblesController extends BaseController
{
    function mostrar(){
        try{
            $aulas = Aulasdisponible::join('aulas', 'idAula', '=', 'aulas.id')->join('sucursales', 'aulasdisponibles.idSucursal', '=', 'sucursales.id')->join('calendarios', 'idCalendario', '=', 'calendarios.id')->join('modalidades', 'idModalidad', '=', 'modalidades.id')->select('aulasdisponibles.*', 'calendarios.nombre as calendario', 'sucursales.nombre as sucursal', 'aulas.nombre as aula', 'modalidades.nombre as modalidad')->where('aulasdisponibles.eliminado', '=', 0)->get();
            return response()->json($aulas, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function crear(Request $request){
        try {
            $existe = Aulasdisponible::where('idAula', '=', $request['idAula'])->where('idSucursal', '=', $request['idSucursal'])->where('idCalendario', '=', $request['idCalendario'])->get();


            if(count($existe) > 0){
                if($existe[0]->eliminado === 0){
                    return response()->json('Ya existe un aula con esas caracteristicas');
                }else{
                    $existe[0]->eliminado = 0;
                    return response()->json($aula, 200);
                }
            }else{
                $aula = Aulasdisponible::create([
                    'idAula' => $request['idAula'],
                    'idSucursal' => $request['idSucursal'],
                    'idCalendario' => $request['idCalendario'],
                    'idModalidad' => $request['idModalidad'],
                    'cupo' => $request['cupo'],
                    'activo' => 1,
                    'eliminado' => 0
                ]);
                return response()->json($aula, 200);
            }
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request){
        try{
            $aula = Aulasdisponible::find($request['id']);
            $aula->eliminado = 1;
            $aula->save();
            return response()->json($aula, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }
}