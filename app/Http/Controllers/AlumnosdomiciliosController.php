<?php

namespace App\Http\Controllers;
use App\Alumnosdomicilio;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlumnosdomiciliosController extends BaseController
{
    function nuevo(Request $request){
        try{
            $domicilio = Alumnosdomicilio::create([
                'domicilio' => $request['domicilio'],
                'numero' => $request['numero'],
                'colonia' => $request['colonia'],
                'codigoPostal' => $request['codigoPostal'],
                'idAlumno' => $request['idAlumno'],
                'idEstado' => $request['idEstado'],
                'idMunicipio' => $request['idMunicipio'],
                'activo' => 1,
                'eliminado' => 0
            ]);
            return response()->json($domicilio, 200);
        }catch(Exception $e){
            return response()->json('Error de servidor', 400);
        }
    }

    function mostrar(Request $request){
        try {
            $domicilios = Alumnosdomicilio::where('eliminado', '=', 0)->where('idAlumno', '=', $request['idAlumno'])->get();
            return response()->json($domicilios, 200);
        } catch (Exception $e) {
            return response()->json('Error de servidor', 400);
        }
    }

    function activos(Request $request){
        try {
            $domicilios = Alumnosdomicilio::where('eliminado', '=', 0)->where('idAlumno', '=', $request['idAlumno'])->where('activo', '=', 1)->get();
            return response()->json($domicilios, 200);
        } catch (Exception $e) {
            return response()->json('Error de servidor', 400);
        }
    }

    function activar(Request $request){
        try{
            $domicilio = Alumnosdomicilio::find($request['id']);
            $domicilio->activo = 1;
            $domicilio->save();
            return response()->json($domicilio, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function desactivar(Request $request){
        try{
            $domicilio = Alumnosdomicilio::find($request['id']);
            $domicilio->activo = 0;
            $domicilio->save();
            return response()->json($domicilio, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request){
        try{
            $domicilio = Alumnosdomicilio::find($request['id']);
            $domicilio->eliminado = 1;
            $domicilio->save();
            return response()->json($domicilio, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request){
        try{
            $domicilio = Alumnosdomicilio::find($request['id']);
            $domicilio->domicilio = $request['domicilio'];
            $domicilio->numero = $request['numero'];
            $domicilio->colonia = $request['colonia'];
            $domicilio->codigoPostal = $request['codigoPostal'];
            $domicilio->idAlumno = $request['idAlumno'];
            $domicilio->idEstado = $request['idEstado'];
            $domicilio->idMunicipio = $request['idMunicipio'];
            $domicilio->save();
            return response()->json($domicilio, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function buscar(Request $request){
        try{
            $busqueda = $request['busqueda'];
            $consulta = "SELECT id,nombre FROM alumnosdomicilios WHERE domicilio LIKE '%$busqueda%'";
            $domicilios = DB::select($consulta, array());
            return response()->json($domicilios, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function traer(Request $request) {
        try{
            $domicilio = Alumnosdomicilio::find($request['id']);
            return response()->json($domicilio, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function primer(Request $request){
        try{
            $domicilio = Alumnosdomicilio::where('idAlumno', '=', $request['idAlumno'])->get();
            return response()->json($domicilio[0], 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }
}