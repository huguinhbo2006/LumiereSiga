<?php

namespace App\Http\Controllers;
use App\Opcionespermiso;
use App\Modulospermiso;
use App\Modulo;
use App\Opcione;


use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class PermisosController extends BaseController
{
    function mostrar(Request $request){
    	try{
    		$opcionesp = Opcionespermiso::where('idTipoUsuario', '=', $request['id'])->get();
    		$modulosp = Modulospermiso::where('idTipoUsuario', '=', $request['id'])->get();
    		$listaIdModulos = array();
    		$listaIdOpciones = array();

    		foreach ($modulosp as $modulop) {
    			$listaIdModulos[] = $modulop['idModulo'];
    		}

    		foreach ($opcionesp as $opcionp) {
    			$listaIdOpciones[] = $opcionp['idOpcion'];
    		}

    		$resultado = array();
            $final = array();
            $opcionesf = array();
            $modulos =  Modulo::where('eliminado', '=', 0)->get();
            foreach ($modulos as $modulo) {
                $resultado = $modulo;
                $resultado['activo'] = 0;
                if(in_array($resultado['id'], $listaIdModulos)){
                	$resultado['activo'] = 1;
                }
                $opciones = Opcione::where('eliminado', '=', 0)->where('idModulo', '=', $modulo['id'])->orderBy('nombre', 'DESC')->get();
                foreach ($opciones as $opcion) {
                    $opcion['activo'] = 0;
                    if(in_array($opcion['id'], $listaIdOpciones)){
                        $opcion['activo'] = 1;
                    }
                    $opcionesf[] = $opcion;
                }
                $resultado['opciones'] = $opcionesf;
                $final[] = $resultado;
                $opcionesf = array();
            }
    		return response()->json($final, 200);
    	}catch(Exception $e){
    		return response()->json('Error en el servidor', 400);
    	}
    }

    function activarModulo(Request $request){
    	try{
    		if(Modulospermiso::where('idModulo', '=', $request['idModulo'])->where('idTipoUsuario', '=', $request['idTipoUsuario'])->count() > 0){
    			return response()->json('Este usuario ya cuenta con este permiso', 400);
    		}else{
    			$modulo = Modulospermiso::create([
	            	'idModulo' => $request['idModulo'],
	            	'idTipoUsuario' => $request['idTipoUsuario']
	            ]);
	            return response()->json($request, 200);
    		}
            
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivarModulo(Request $request){
    	try{
    		if(Modulospermiso::where('idModulo', '=', $request['idModulo'])->where('idTipoUsuario', '=', $request['idTipoUsuario'])->count() > 0){
    			$modulo = Modulospermiso::where('idModulo', '=', $request['idModulo'])->where('idTipoUsuario', '=', $request['idTipoUsuario']);
    			$modulo->delete();
	            return response()->json($modulo, 200);
    		}else{
    			return response()->json('Este usuario no cuenta con este permiso', 400);
    		}
            
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activarOpcion(Request $request){
    	try{
    		if(Opcionespermiso::where('idOpcion', '=', $request['idOpcion'])->where('idTipoUsuario', '=', $request['idTipoUsuario'])->count() > 0){
    			return response()->json('Este usuario ya cuenta con este permiso', 400);
    		}else{
    			$opcion = Opcionespermiso::create([
	            	'idOpcion' => $request['idOpcion'],
	            	'idTipoUsuario' => $request['idTipoUsuario']
	            ]);
	            return response()->json($request, 200);
    		}
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivarOpcion(Request $request){
    	try{
    		$modulo = Opcionespermiso::where('idOpcion', '=', $request['idOpcion'])->where('idTipoUsuario', '=', $request['idTipoUsuario']);
    		if($modulo->count() > 0){
    			$modulo->delete();
	            return response()->json($modulo, 200);
    		}else{
    			return response()->json('Este usuario no cuenta con este permiso', 400);
    		}
            
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }
}