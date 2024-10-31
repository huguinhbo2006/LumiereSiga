<?php

namespace App\Http\Controllers;
use App\Examene;
use App\Categoria;
use App\Nivele;
use App\Subnivele;
use App\Examenpermiso;
include "funciones/FuncionesGenerales.php";

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamenpermisosController extends BaseController
{
    function selectores() {
        try {
            $niveles = Nivele::where('eliminado', '=', 0)->get();
            $subniveles = Subnivele::where('eliminado', '=', 0)->get();
            $categorias = Categoria::where('eliminado', '=', 0)->get();

            $respuesta['niveles'] = $niveles;
            $respuesta['subniveles'] = $subniveles;
            $respuesta['categorias'] = $categorias;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function nuevo(Request $request){
        try {
            $existe = Examenpermiso::where('idExamen', '=', $request['idExamen'])->
                                     where('idNivel', '=', $request['idNivel'])->
                                     where('idSubnivel', '=', $request['idSubnivel'])->
                                     where('idCategoria', '=', $request['idCategoria'])->get();
            if(count($existe) > 0){
                return response()->json('El permiso ya existe para este examen', 400);
            }else{
                $permiso = Examenpermiso::create([
                    'idExamen' => $request['idExamen'],
                    'idNivel' => $request['idNivel'],
                    'idSubnivel' => $request['idSubnivel'],
                    'idCategoria' => $request['idCategoria'],
                    'eliminado' => 0,
                    'activo' => 1
                ]);
                return response()->json($permiso, 200);
            }
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function mostrar(Request $request){
        try {
            $permisos = Examenpermiso::join('niveles', 'idNivel', '=', 'niveles.id')->
                                       join('subniveles', 'idSubnivel', '=', 'subniveles.id')->
                                       join('categorias', 'idCategoria', '=', 'categorias.id')->
                                       select('niveles.nombre as nivel', 'examenpermisos.*', 'subniveles.nombre as subnivel', 'categorias.nombre as categoria')->
                                       where('examenpermisos.idExamen', '=', $request['id'])->get();
            return response()->json($permisos, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);   
        }
    }

    function eliminar(Request $request){
        try {
            $permiso = Examenpermiso::find($request['id']);
            $permiso->delete();
            return response()->json('Permiso eliminado', 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}