<?php

namespace App\Http\Controllers;
use App\Formularioclasegrati;
use App\Formularioinformacionpersonalizada;
use App\Formulariocupondescuento;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
include "funciones/FuncionesGenerales.php";

class FormulariosController extends BaseController
{
    function guardarClaseGratis(Request $request){
      try {
        $existe = Formularioclasegrati::where('celular', '=', $request['celular'])->get();
        if(count($existe) > 0){
          return response()->json('Ya has hecho un resgistro anteriormente', 400);
        }
        $registro = Formularioclasegrati::create([
          'nombre' => $request['nombre'],
          'celular' => $request['celular'],
          'idCarrera' => $request['idCarrera'],
          'promedio' => $request['promedio'],
          'idSucursal' => $request['idSucursal'],
          'activo' => 1,
          'eliminado' => 0
        ]);
        return response()->json($registro, 200);
      } catch (Exception $e) {
        return response()->json('Error en el servidor', 400);
      }
    }

    function guardarInformacionPersonalizada(Request $request){
      try {
        $existe = Formularioinformacionpersonalizada::where('celular', '=', $request['celular'])->get();
        if(count($existe) > 0){
          return response()->json($existe[0], 200);
        }else{
          $registro = Formularioinformacionpersonalizada::create([
            'nombre' => $request['nombre'],
            'celular' => $request['celular'],
            'activo' => 1,
            'eliminado' => 0
          ]);

          return response()->json($registro, 200);
        }
      } catch (Exception $e) {
        return response()->json('Error en el servidor', 400);
      }
    }

    function guardarCuponDescuento(Request $request){
      try {
        $existe = Formulariocupondescuento::where('celular', '=', $request['celular'])->get();
        if(count($existe)){
          return response()->json($existe[0], 200);
        }else{
          $registro = Formulariocupondescuento::create([
            'nombre' => $request['nombre'],
            'celular' => $request['celular'],
            'descuento' => $request['descuento'],
            'activo' => 1,
            'eliminado' => 0
          ]);

          return response()->json($registro, 200);
        }
      } catch (Exception $e) {
        return response()->json('Error en el servidor', 400);
      }
    }
}