<?php

namespace App\Http\Controllers;
use App\Inventariocomputadora;
use App\Inventarioobjeto;
use App\Inventariocomputadoraobjeto;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
include "funciones/FuncionesGenerales.php";

class BancosController extends BaseController
{
    function mostrar(Request $request){
        try {
            $computadoras = Inventariocomputadora::where('idSucursal', '=', $request['sucursalID'])->get();
            $
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function nuevaComputadora(Request $request){
        try {
            $computadora = Inventariocomputadora::create([
                'idSucursal' => $request['sucursalID'],
                'idUsuario' => $request['usuarioID'],
                'idTipo' => $request['idTipo'],
                'procesador' => $request['procesador'],
                'ram' => $request['ram'],
                'disco' => $request['disco'],
                'windows' => $request['windows'],
                'office' => $request['office'],
                'estatus' => 1,
                'activo' => 1,
                'eliminado' => 0
            ]);
            return response()->json($computadora, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 200);
        }
    }
}