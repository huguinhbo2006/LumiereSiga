<?php

namespace App\Http\Controllers;
use App\Sedesucursale;
use App\Sucursale;
use App\Sede;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
include "funciones/FuncionesGenerales.php";

class SedesucursalesController extends BaseController
{
    function mostrar(Request $request){
        try {
            $sucursales = Sucursale::where('eliminado', '=', 0)->get();
            $sedes = Sedesucursale::where('eliminado', '=', 0)->get();

            $respuesta['sucursales'] = $sucursales;
            $respuesta['sedes'] = $sedes;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function nuevo(Request $request){
        try {
            $existe = Sedesucursale::where('idSucursal', '=', $request['idSucursal'])->where('idSede', '=', $request['idSede'])->get();
            if(count($existe) > 0){
                $sucursal = Sucursale::find($request['idSucursal']);
                $sede = Sede::find($request['idSede']);
                return response()->json('La sucursal '.$sucursal->nombre.' ya fue asignado a la sede '.$sede->nombre, 400);
            }else{
                $sucursal = Sedesucursale::create([
                    'idSede' => $request['idSede'],
                    'idSucursal' => $request['idSucursal'],
                    'eliminado' => 0,
                    'activo' => 1
                ]);

                return response()->json($sucursal, 200);
            }
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request){
        try {
            $sucursal = Sedesucursale::where('idSede', '=', $request['idSede'])->where('idSucursal', '=', $request['idSucursal'])->get()[0];
            $sucursal = Sedesucursale::find($sucursal->id);
            $sucursal->delete();

            return response()->json($sucursal, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}