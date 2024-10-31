<?php

namespace App\Http\Controllers;
use App\Websucursalruta;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
include "funciones/FuncionesGenerales.php";

class WebsucursalrutasController extends BaseController
{
    function nuevo(Request $request){
        try {
            $nuevo = Websucursalruta::create([
                'ruta' => $request['ruta'],
                'idSucursal' => $request['idSucursal'],
                'activo' => 1,
                'eliminado' => 0
            ]);

            return response()->json($nuevo, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function mostrar(Request $request){
        try {
            $rutas = Websucursalruta::where('idSucursal', '=', $request['idSucursal'])->get();
            return response()->json($rutas, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request){
        try {
            $ruta = Websucursalruta::find($request['id']);
            $ruta->delete();
            return response()->json($ruta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}