<?php

namespace App\Http\Controllers;
use App\Onlineventa;
use App\Cursosonlineventa;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
include "logs.php";

class VentasOnlineController extends BaseController
{
    function guardarCliente(Request $request){
        try{
            $venta = Onlineventa::create([
                'nombre' => $request['nombre'],
                'correo' => $request['correo'],
                'telefono' => $request['telefono'],
                'folio' => $request['folio'],
                'activo' => 1,
                'eliminado' => 0
            ]);
            return response()->json($venta, 200);
        }catch(Exception $e){
            return response()->json("Error al guardar venta", 400);
        }
    }

    function guardarCursos(Request $request){
        try{
            $iva = ($request['hay'] === 0) ? false : true;
            Cursosonlineventa::create([
                'idVenta' => $request['idVenta'],
                'precio' => $request['precioFinal'],
                'iva' => $iva,
                'nombre' => $request['nombre'],
                'calendario' => $request['calendario'],
                'modalidad' => $request['modalidad'],
                'plantel' => $request['plantel'],
                'horario' => $request['horario'],
                'activo' => 1,
                'eliminado' => 0
            ]);
            return response()->json("Todo bien", 200);
        }catch(Exception $e){
            return response()->json("Error al guardar venta", 400);
        }
    }

    function ventas(){
        try{
            $ventas = Onlineventa::where('eliminado', '=', 0)->get();
            return response()->json($ventas, 200);
        }catch(Exception $e){
            return response()->json("Error al traer ventas", 400);
        }
    }
}