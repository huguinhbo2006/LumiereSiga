<?php

namespace App\Http\Controllers;

use App\Usuario;
use App\Empleado;
use App\Calendario;
use App\Usuariosucursale;
use App\Sucursale;
use App\Ficha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DesarrolloController extends Controller
{
    function verPassword(Request $request){
        try{
            $codificacion = Hash::make($request['password']);
            $respuesta['codificacion'] = $codificacion;
            return response()->json($respuesta, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }
}