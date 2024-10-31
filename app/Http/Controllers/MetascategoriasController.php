<?php

namespace App\Http\Controllers;
use App\Metascategoria;
use App\Calendario;
use App\Sucursale;
use App\Categoria;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
include "funciones/FuncionesGenerales.php";

class MetascategoriasController extends BaseController
{
    function nuevo(Request $request){
        try {
            $existe = Metascategoria::where('idCalendario', '=', $request['idCalendario'])->
            where('idSucursal', '=', $request['idSucursal'])->
            where('idCategoria', '=', $request['idCategoria'])->get();
            if(count($existe) > 0){
                $meta = $existe[0];
                $meta->calendario = Calendario::find($meta->idCalendario)->nombre;
                $meta->sucursal = Sucursale::find($meta->idSucursal)->nombre;
                $meta->categoria = Categoria::find($meta->idCategoria)->nombre;
                $mes = mesEntero($meta->mes);

                $mensaje = "Ya existe una meta para la categoria ".$meta->categoria." para la sucursal ".$meta->sucursal." en el calendario ".$meta->calendario;
                return response()->json($mensaje, 400);
            }else{
                $meta = Metascategoria::create([
                    'idCalendario' => $request['idCalendario'],
                    'idSucursal' => $request['idSucursal'],
                    'idCategoria' => $request['idCategoria'],
                    'meta' => $request['meta'],
                    'eliminado' => 0,
                    'activo' => 1
                ]);

                $meta->calendario = Calendario::find($meta->idCalendario)->nombre;
                $meta->sucursal = Sucursale::find($meta->idSucursal)->nombre;
                $meta->categoria = Categoria::find($meta->idCategoria)->nombre;

                return response()->json($meta, 200);
            }
        } catch (Exception $e) {
            
        }
    }

    function modificar(Request $request){
        try {
            $meta = Metascategoria::find($request['id']);
            $meta->meta = $request['meta'];
            $meta->save();
            $meta->calendario = Calendario::find($meta->idCalendario)->nombre;
            $meta->sucursal = Sucursale::find($meta->idSucursal)->nombre;
            $meta->categoria = Categoria::find($meta->idCategoria)->nombre;

            return response()->json($meta, 200);
        } catch (Exception $e) {
            
        }
    }

    function mostrar(Request $request){
        try {
            $metas = Metascategoria::join('sucursales', 'idSucursal', '=', 'sucursales.id')->
            join('calendarios', 'idCalendario', '=', 'calendarios.id')->
            join('categorias', 'idCategoria', '=', 'categorias.id')->
            select(
                'metascategorias.*', 
                'calendarios.nombre as calendario',
                'sucursales.nombre as sucursal',
                'categorias.nombre as categoria'
            )->
            where('metascategorias.eliminado', '=', 0)->get();

            $consulta = "SELECT * FROM calendarios WHERE fin > NOW() AND eliminado = 0 AND activo = 1";
            $calendarios = DB::select($consulta, array());
            $sucursales = Sucursale::where('eliminado', '=', 0)->get();
            $categorias = Categoria::where('eliminado', '=', 0)->get();

            $respuesta['listas']['calendarios'] = $calendarios;
            $respuesta['listas']['sucursales'] = $sucursales;
            $respuesta['listas']['metas'] = $metas;
            $respuesta['listas']['categorias'] = $categorias;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request){
        try {
            $meta = Metascategoria::find($request['id']);
            $meta->delete();

            $meta->calendario = Calendario::find($meta->idCalendario)->nombre;
            $meta->sucursal = Sucursale::find($meta->idSucursal)->nombre;
            $meta->categoria = Categoria::find($meta->idCategoria)->nombre;

            return response()->json($meta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}