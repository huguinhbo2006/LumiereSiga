<?php

namespace App\Http\Controllers;
use App\Aula;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
include "funciones/FuncionesGenerales.php";

class AulasController extends BaseController
{
    function nuevo(Request $request){
        try{
            $aula = Aula::create([
                'nombre' => $request['nombre'],
                'cupo' => $request['cupo'],
                'idSucursal' => $request['sucursalID'],
                'activo' => 1,
                'eliminado' => 0
            ]);

            return response()->json($aula, 200);
        }catch(Exception $e){
            return response()->json('Error de servidor', 400);
        }
    }

    function mostrar(Request $request){
        try {
            $aulas = Aula::join('sucursales', 'idSucursal', '=', 'sucursales.id')->
            select('aulas.*', 'sucursales.nombre as sucursal')->
            where('aulas.eliminado', '=', 0)->
            where('aulas.idSucursal', '=', $request['sucursalID'])->get();
            return response()->json($aulas, 200);
        } catch (Exception $e) {
            return response()->json('Error de servidor', 400);
        }
    }

    function activos(Request $request){
        try {
            $aulas = Aula::join('sucursales', 'idSucursal', '=', 'sucursales.id')->select('aulas.*', 'sucursales.nombre as sucursal')->where('aulas.eliminado', '=', 0)->where('aulas.activo', '=', 1)->where('aulas.idSucursal', '=', $request['sucursalID'])->get();
            return response()->json($aulas, 200);
        } catch (Exception $e) {
            return response()->json('Error de servidor', 400);
        }
    }

    function activar(Request $request){
        try{
            $aula = Aula::find($request['id']);
            $aula->activo = 1;
            $aula->save();

            return response()->json($aula, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function desactivar(Request $request){
        try{
            $aula = Aula::find($request['id']);
            $aula->activo = 0;
            $aula->save();

            return response()->json($aula, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request){
        try{
            $aula = Aula::find($request['id']);
            $aula->eliminado = 1;
            $aula->save();

            return response()->json($aula, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request){
        try{
            $aula = Aula::find($request['id']);
            $aula->cupo = $request['cupo'];
            $aula->nombre = $request['nombre'];
            $aula->save();

            return response()->json($aula, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }
}