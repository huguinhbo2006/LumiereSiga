<?php

namespace App\Http\Controllers;
use App\Empresavinculacione;
use App\Empresaseguimiento;
use App\Empresaconvenio;
use App\Empleado;
use App\Nivele;
use App\Subnivele;
use App\Conceptosdescuento;
use App\Usuario;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VinculacionesController extends BaseController
{
    function nuevaEmpresa(Request $request){
    	try {
    		$empresa = Empresavinculacione::create([
    			'nombre' => $request['nombre'],
    			'direccion' => $request['direccion'],
    			'telefono' => $request['telefono'],
    			'celular' => $request['celular'],
    			'correo' => $request['correo'],
    			'responsable' => $request['responsable'],
    			'activo' => 1,
    			'eliminado' => 0
    		]);
    		return response()->json($empresa, 200);
    	} catch (Exception $e) {
    		return response()->json('Error en el servidor', 400);
    	}
    }

    function mostrarEmpresas(){
    	try {
    		$empresas = Empresavinculacione::
            select([
                'empresavinculaciones.*',
                DB::raw("(
                        CASE 
                        WHEN(estatus = 1) THEN 'bg-azul'
                        WHEN(estatus = 2) THEN 'bg-amarillo'
                        WHEN(estatus = 3) THEN 'bg-rojo'
                        WHEN(estatus = 4) THEN 'bg-morado'
                        WHEN(estatus = 5) THEN 'bg-verde'
                       END
                    ) AS bg"),
                DB::raw("(
                        CASE 
                        WHEN(estatus = 1) THEN 'Prospecto'
                        WHEN(estatus = 2) THEN 'Interesado'
                        WHEN(estatus = 3) THEN 'No Interesado'
                        WHEN(estatus = 4) THEN 'Pausado'
                        WHEN(estatus = 5) THEN 'Convenio'
                       END
                    ) AS estatusActual")
            ])->
            where('eliminado', '=', 0)->get();
    		return response()->json($empresas, 200);
    	} catch (Exception $e) {
    		return response()->json('Error en el servidor', 400);
    	}
    }

    function modificarEmpresa(Request $request){
        try {
            $empresa = Empresavinculacione::find($request['id']);
            $empresa->nombre = $request['nombre'];
            $empresa->direccion = $request['direccion'];
            $empresa->telefono = $request['telefono'];
            $empresa->celular = $request['celular'];
            $empresa->correo = $request['correo'];
            $empresa->responsable = $request['responsable'];
            $empresa->save();
            return response()->json($empresa, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function traerEmpresa(Request $request){
        try {
            $empresa = Empresavinculacione::
            select([
                'empresavinculaciones.*',
                DB::raw("(
                        CASE 
                        WHEN(estatus = 1) THEN 'Prospecto'
                        WHEN(estatus = 2) THEN 'Interesado'
                        WHEN(estatus = 3) THEN 'No Interesado'
                        WHEN(estatus = 4) THEN 'Pausado'
                        WHEN(estatus = 5) THEN 'Convenio'
                       END
                    ) AS estatusActual"),
                DB::raw("(
                        CASE 
                        WHEN(estatus = 1) THEN 'text-info'
                        WHEN(estatus = 2) THEN 'text-yellow'
                        WHEN(estatus = 3) THEN 'text-red'
                        WHEN(estatus = 4) THEN 'text-purple'
                        WHEN(estatus = 5) THEN 'text-green'
                       END
                    ) AS bg")
            ])->find($request['id']);

            $seguimientos = Empresaseguimiento::join('usuarios', 'empresaseguimientos.idUsuario', '=', 'usuarios.id')->
            join('empleados', 'usuarios.idEmpleado', '=', 'empleados.id')->
            select(
                'empresaseguimientos.*',
                DB::raw("(
                        CASE 
                        WHEN(medio = 1) THEN 'fab fa-whatsapp text-success'
                        WHEN(medio = 2) THEN 'fab fa-facebook text-primary'
                        WHEN(medio = 3) THEN 'fab fa-instagram text-pink'
                        WHEN(medio = 4) THEN 'fab fa-telegram text-info'
                        WHEN(medio = 5) THEN 'fas fa-phone text-danger'
                        WHEN(medio = 6) THEN 'fas fa-people-arrows text-purple'
                        WHEN(medio = 7) THEN 'fas fa-envelope text-magenta'
                       END
                    ) AS logo"),
                'empleados.nombre as usuario'
            )->where('idEmpresa', '=', $request['id'])->get();

            $convenios = Empresaconvenio::join('niveles', 'idNivel', '=', 'niveles.id')->
            join('subniveles', 'idSubnivel', '=', 'subniveles.id')->
            join('conceptosdescuentos', 'idConceptoDescuento', '=', 'conceptosdescuentos.id')->
            join('usuarios', 'idUsuario', '=', 'usuarios.id')->
            join('empleados', 'idEmpleado', '=', 'empleados.id')->
            select(
                'empresaconvenios.*',
                'niveles.nombre as nivel',
                'subniveles.nombre as subnivel',
                'conceptosdescuentos.nombre as concepto',
                'empleados.nombre as usuario',
                DB::raw("(
                        CASE 
                        WHEN(tipoDescuento = 1) THEN CONCAT(descuento,'%')
                        WHEN(tipoDescuento = 2) THEN CONCAT('$',descuento)
                       END
                    ) AS des")
            )->where('idEmpresa', '=', $request['id'])->get();

            $respuesta['empresa'] = $empresa;
            $respuesta['seguimientos'] = $seguimientos;
            $respuesta['convenios'] = $convenios;

            $respuesta['listas']['niveles'] = Nivele::where('eliminado', '=', 0)->get();
            $respuesta['listas']['subniveles'] = Subnivele::where('eliminado', '=', 0)->get();
            $respuesta['listas']['conceptos'] = Conceptosdescuento::where('eliminado', '=', 0)->get();


            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function guardarSeguimientoEmpresa(Request $request){
        try {
            $seguimiento = Empresaseguimiento::create([
                'comentario' => $request['comentario'],
                'medio' => $request['medio'],
                'fecha' => $request['fecha'],
                'idEmpresa' => $request['idEmpresa'],
                'idUsuario' => $request['usuarioID'],
                'eliminado' => 0,
                'activo' => 1
            ]);

            switch($seguimiento->medio){
                case 1:
                    $seguimiento->logo = "fab fa-whatsapp text-success";
                    break;
                case 2:
                    $seguimiento->logo = "fab fa-facebook text-primary";
                    break;
                case 3:
                    $seguimiento->logo = "fab fa-instagram text-pink";
                    break;
                case 4:
                    $seguimiento->logo = "fab fa-telegram text-info";
                    break;
                case 5:
                    $seguimiento->logo = "fas fa-phone text-danger";
                    break;
                case 6:
                    $seguimiento->logo = "fas fa-people-arrows text-purple";
                    break;
                case 7:
                    $seguimiento->logo = "fas fa-envelope text-magenta";
                    break;
                default:
                    $seguimiento->logo = "fas fa-percent text-black";
                    break;
            }
            $seguimiento->usuario = Empleado::find($request['usuarioID'])->nombre;
            return response()->json($seguimiento, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function cambiarEstatusEmpresa(Request $request){
        try {
            $empresa = Empresavinculacione::find($request['id']);
            $empresa->estatus = $request['estatus'];
            $empresa->save();
            return response()->json($empresa, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function guardarConvenioEmpresa(Request $request){
        try {
            $existe = Empresaconvenio::where('idEmpresa', '=', $request['idEmpresa'])->where('idNivel', '=', $request['idNivel'])->
            where('idSubnivel', '=', $request['idSubnivel'])->where('idConceptoDescuento', '=', $request['idConceptoDescuento'])->get();
            if(count($existe) > 0){
                return response()->json('Ya existe un convenio', 400);
            }
            $convenio = Empresaconvenio::create([
                'idEmpresa' => $request['idEmpresa'],
                'idNivel' => $request['idNivel'],
                'idSubnivel' => $request['idSubnivel'],
                'idConceptoDescuento' => $request['idConceptoDescuento'],
                'tipoDescuento' => $request['tipoDescuento'],
                'descuento' => $request['descuento'],
                'idUsuario' => $request['usuarioID'],
                'activo' => 1,
                'eliminado' => 0
            ]);

            $convenio->usuario = Empleado::find(Usuario::find($convenio->idUsuario)->idEmpleado)->nombre;
            $convenio->nivel = Nivele::find($convenio->idNivel)->nombre;
            $convenio->subnivel = Subnivele::find($convenio->idSubnivel)->nombre;
            $convenio->concepto = Conceptosdescuento::find($convenio->idConceptoDescuento)->nombre;
            $convenio->des = ($convenio->tipoDescuento === 1) ? $convenio->descuento.'%' : '$'.$convenio->descuento;
            return response()->json($convenio, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function cambiarEstatusConvenio(Request $request){
        try {
            $convenio = Empresaconvenio::find($request['id']);
            $convenio->activo = !$convenio->activo;
            $convenio->save();
            $request['activo'] = $convenio->activo;
            return response()->json($request, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}