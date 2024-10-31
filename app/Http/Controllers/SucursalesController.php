<?php

namespace App\Http\Controllers;
use App\Sucursale;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class SucursalesController extends BaseController
{
    function mostrar(){
        try{
            $sucursales =  Sucursale::where('eliminado', '=', 0)->take(100)->get();
            return response()->json($sucursales, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function nuevo(Request $request){
        try{
            $sucursal = Sucursale::create([
                'nombre' => $request['nombre'],
                'telefono' => $request['telefono'],
                'direccion' => $request['direccion'],
                'abreviatura' => $request['abreviatura'],
                'whatsapp' => $request['whatsapp'],
                'imagen' => $request['imagen'],
                'mapa' => $request['mapa'],
                'activo' => '1',
                'eliminado' => '0'
            ]);

            return response()->json($request, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            if(intval($request['id']) === 1){
                return response()->json('No se puede modificar esta sucursal', 400);
            }
            $sucursal = Sucursale::find($request['id']);
            $sucursal->nombre = $request['nombre'];
            $sucursal->direccion = $request['direccion'];
            $sucursal->telefono = $request['telefono'];
            $sucursal->abreviatura = $request['abreviatura'];
            $sucursal->whatsapp = $request['whatsapp'];
            $sucursal->mapa = $request['mapa'];
            $sucursal->imagen = $request['imagen'];
            $sucursal->save();

            return response()->json($sucursal, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            if(intval($request['id']) === 1){
                return response()->json('No se puede activar esta sucursal', 400);
            }
            $sucursal = Sucursale::find($request['id']);
            $sucursal->activo = 1;
            $sucursal->save();

            return response()->json($sucursal, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            if(intval($request['id']) === 1){
                return response()->json('No se puede desactivar esta sucursal', 400);
            }
            $sucursal = Sucursale::find($request['id']);
            $sucursal->activo = 0;
            $sucursal->save();

            return response()->json($sucursal, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            if(intval($request['id']) === 1){
                return response()->json('No se puede eliminar esta sucursal', 400);
            }
            $sucursal = Sucursale::find($request['id']);
            $sucursal->eliminado = 1;
            $sucursal->save();

            return response()->json($sucursal, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }
}