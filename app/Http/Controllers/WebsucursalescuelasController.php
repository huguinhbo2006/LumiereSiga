<?php

namespace App\Http\Controllers;
use App\Websucursalescuela;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
include "funciones/FuncionesGenerales.php";

class WebsucursalescuelasController extends BaseController
{
    function nuevo(Request $request){
        try {
            $nuevo = Websucursalescuela::create([
                'escuela' => $request['escuela'],
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
            $escuelas = Websucursalescuela::where('idSucursal', '=', $request['idSucursal'])->get();
            return response()->json($escuelas, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request){
        try {
            $escuela = Websucursalescuela::find($request['id']);
            $escuela->delete();
            return response()->json($escuela, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}