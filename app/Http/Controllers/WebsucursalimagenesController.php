<?php

namespace App\Http\Controllers;
use App\Websucursalimagene;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
include "funciones/FuncionesGenerales.php";

class WebsucursalimagenesController extends BaseController
{
    function nuevo(Request $request){
        try {
            $nuevo = Websucursalimagene::create([
                'imagen' => $request['imagen'],
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
            $imagenes = Websucursalimagene::where('idSucursal', '=', $request['idSucursal'])->get();
            return response()->json($imagenes, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request){
        try {
            $imagen = Websucursalimagene::find($request['id']);
            $imagen->delete();
            return response()->json($imagen, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}