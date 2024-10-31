<?php

namespace App\Http\Controllers;
use App\Empleado;
use App\Usuario;
use App\Usuariosucursale;
use App\Sucursale;
use Illuminate\Support\Facades\Hash;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
include "funciones/FuncionesGenerales.php";



class EmpleadosController extends BaseController
{
    function mostrar(){
        try{
            $empleados = Empleado::join('departamentos', 'idDepartamento', '=', 'departamentos.id')->join('sucursales', 'idSucursal', '=', 'sucursales.id')->join('puestos', 'idPuesto', '=', 'puestos.id')->select('empleados.*', 'departamentos.nombre as departamento', 'sucursales.nombre as sucursal', 'puestos.nombre as puesto')->where('empleados.eliminado', '=', 0)->where('empleados.id', '<>', 1)->where('departamentos.id', '<>', 1)->get();
            $json = array();
            foreach ($empleados as $empleado) {
                $usuario = Usuario::where('idEmpleado', '=', $empleado['id'])->get();
                $empleado['bg'] = (intval($empleado->activo) === 1) ? '' : 'bg-rojo';
                $empleado['usuario'] = $usuario;
                $json[] = $empleado; 
            }
            return response()->json($json, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function nuevo(Request $request){
        try{
            $personales = $request['personales'];
            $domicilio = $request['domicilio'];
            $fiscales = $request['fiscales'];
            $empresa = $request['empresa'];
            $imagenes = $request['imagenes'];

            $fechaIngreso = ($fiscales['fechaIngreso'] == null || $fiscales['fechaIngreso'] == '') ? null : $fiscales['fechaIngreso'];
            $fechaAltaIMSS = ($fiscales['fechaAltaIMSS'] == null || $fiscales['fechaAltaIMSS'] == '') ? null : $fiscales['fechaAltaIMSS'];
            $fechaNacimiento = ($personales['fechaNacimiento'] == null || $personales['fechaNacimiento'] == '') ? null : $personales['fechaNacimiento'];
            $hay = Empleado::all();
            
            $empleado = Empleado::create([
                //Personales
                'nombre' => $personales['nombre'],
                'correo' => $personales['correo'],
                'telefono' => $personales['telefono'],
                'celular' => $personales['celular'],
                'estadoCivil' => $personales['estadoCivil'],
                'fechaNacimiento' => $fechaNacimiento,
                //Domicilio
                'calle' => $domicilio['calle'],
                'numeroInterior' => $domicilio['numeroInterior'],
                'numeroExterior' => $domicilio['numeroExterior'],
                'colonia' => $domicilio['colonia'],
                'idMunicipio' => $domicilio['idMunicipio'],
                'idEstado' => $domicilio['idEstado'],
                'codigoPostal' => $domicilio['codigoPostal'],
                //Fiscales
                'nss' => strlen($fiscales['nss'] > 0) ? $fiscales['nss'] : count($hay)+1,
                'rfc' => strlen($fiscales['rfc'] > 0) ? $fiscales['rfc'] : count($hay)+1,
                'curp' => strlen($fiscales['curp'] > 0) ? $fiscales['curp'] : count($hay)+1,
                'fechaAltaIMSS' => $fechaAltaIMSS,
                'fechaIngreso' => $fechaIngreso,
                'cuentaBancaria' => $fiscales['cuentaBancaria'],
                //Empresa
                
                'sueldoBase' => $empresa['sueldoBase'],
                'sueldoFiscal' => $empresa['sueldoFiscal'],
                'idSucursal' => $empresa['idSucursal'],
                'idDepartamento' => $empresa['idDepartamento'],
                'idPuesto' => $empresa['idPuesto'],
                'bonoPuntualidad' => $empresa['bonoPuntualidad'],
                //Imagenes
                'actaNacimiento' => $imagenes['actaNacimiento'],
                'comprobanteDomicilio' => $imagenes['comprobanteDomicilio'],
                'curpImagen' => $imagenes['curpImagen'],
                'ifef' => $imagenes['ifef'],
                'ifet' => $imagenes['ifet'],
                'rfcImagen' => $imagenes['rfcImagen'],
                'carta1' => $imagenes['carta1'],
                'carta2' => $imagenes['carta2'],
                'nssImagen' => $imagenes['nssImagen'],
                'comprobanteEstudios' => $imagenes['comprobanteEstudios'],
                //Otros
                'activo' => 1,
                'eliminado' => 0
                
            ]);

            return response()->json($empleado, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request){
        try{
            $personales = $request['personales'];
            $domicilio = $request['domicilio'];
            $fiscales = $request['fiscales'];
            $empresa = $request['empresa'];
            $imagenes = $request['imagenes'];


            $fechaIngreso = (isset($fiscales['fechaIngreso']) && strlen($fiscales['fechaIngreso']) > 0) ? $fiscales['fechaIngreso'] : null;
            $fechaAltaIMSS = (!array_key_exists("fechaAltaIMSS", $fiscales)) ? null : $fiscales['fechaAltaIMSS'];
            $fechaNacimiento = (isset($personales['fechaNacimiento']) && strlen($personales['fechaNacimiento']) > 0) ? $personales['fechaNacimiento'] : null;

            $empleado = Empleado::find($personales['id']);
            //Personales
            $empleado->nombre = $personales['nombre'];
            $empleado->telefono = $personales['telefono'];
            $empleado->celular = $personales['celular'];
            $empleado->fechaNacimiento = $fechaNacimiento;
            $empleado->estadoCivil = $personales['estadoCivil'];
            $empleado->correo = $personales['correo'];

            //Domicilio
            
            $empleado->calle = $domicilio['calle'];
            $empleado->numeroInterior = $domicilio['numeroInterior'];
            $empleado->numeroExterior = $domicilio['numeroExterior'];
            $empleado->colonia = $domicilio['colonia'];
            $empleado->idMunicipio = $domicilio['idMunicipio'];
            $empleado->idEstado = $domicilio['idEstado'];
            $empleado->codigoPostal = $domicilio['codigoPostal'];
            
            //Fiscales
            $empleado->nss = $fiscales['nss'];
            $empleado->rfc = $fiscales['rfc'];
            $empleado->curp = $fiscales['curp'];
            $empleado->fechaAltaIMSS = $fechaAltaIMSS;
            $empleado->fechaIngreso = $fechaIngreso;
            $empleado->cuentaBancaria = $fiscales['cuentaBancaria'];

            //Empresa
            $empleado->idSucursal = $empresa['idSucursal'];
            $empleado->idDepartamento = $empresa['idDepartamento'];
            $empleado->idPuesto = $empresa['idPuesto'];
            $empleado->sueldoBase = $empresa['sueldoBase'];
            $empleado->sueldoFiscal = $empresa['sueldoFiscal'];
            $empleado->bonoPuntualidad = $empresa['bonoPuntualidad'];
            
            
            //Imagenes
            $empleado->actaNacimiento = $imagenes['actaNacimiento'];
            $empleado->comprobanteDomicilio = $imagenes['comprobanteDomicilio'];
            $empleado->curpImagen = $imagenes['curpImagen'];
            $empleado->ifef = $imagenes['ifef'];
            $empleado->ifet = $imagenes['ifet'];
            $empleado->rfcImagen = $imagenes['rfcImagen'];
            $empleado->carta1 = $imagenes['carta1'];
            $empleado->carta2 = $imagenes['carta2'];
            $empleado->nssImagen = $imagenes['nssImagen'];
            $empleado->comprobanteEstudios = $imagenes['comprobanteEstudios'];
            
            $empleado->save();
            return response()->json($empleado, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            $empleado = Empleado::find($request['id']);
            $empleado->fechaSalida = null;
            $empleado->activo = 1;
            $empleado->save();

            return response()->json($empleado, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            $carbon = new \Carbon\Carbon();
            $date = $carbon->now();
            $empleado = new Empleado();
            $empleado = Empleado::find($request['id']);
            $empleado->activo = 0;
            $empleado->fechaSalida = $date;
            $empleado->save();

            return response()->json($empleado, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $empleado = new Empleado();
            $empleado = Empleado::find($request['id']);
            $empleado->eliminado = 1;
            $empleado->save();

            return response()->json($empleado, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function busquedaNombre(Request $request){
        try{
            $empleados = Empleado::join('departamentos', 'idDepartamento', '=', 'departamentos.id')->join('sucursales', 'idSucursal', '=', 'sucursales.id')->join('puestos', 'idPuesto', '=', 'puestos.id')->select('empleados.*', 'departamentos.nombre as departamento', 'sucursales.nombre as sucursal', 'puestos.nombre as puesto')->where('empleados.eliminado', '=', 0)->where('empleados.id', '<>', 1)->where('empleados.nombre', 'LIKE', '%'.$request['busqueda'].'%')->get();
            $json = array();
            foreach ($empleados as $empleado) {
                $usuario = Usuario::where('idEmpleado', '=', $empleado['id'])->get();
                $empleado['usuario'] = $usuario;
                $json[] = $empleado; 
            }
            return response()->json($json, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function sucursales(Request $request){
        try {
            $sucursales = Sucursale::all();
            $asignadas = Usuariosucursale::where('idUsuario', '=', $request['id'])->get();
            $usuario = Usuario::find($request['id']);
            $empleado = Empleado::find($usuario->idEmpleado);
            $disponibles = array();
            $agregadas = array();
            $respuesta = array();
            foreach ($sucursales as $sucursal) {
                $existe = false;
                foreach ($asignadas as $asignada) {
                    if(intval($sucursal->id) === intval($asignada->idSucursal)){
                        $agregadas[] = $sucursal;
                        $existe = true;
                    }
                }
                if(!$existe && (intval($sucursal->id !== intval($empleado->idSucursal)))){
                    $disponibles[] = $sucursal;
                }
            }
            $respuesta['agregadas'] = $agregadas;
            $respuesta['disponibles'] = $disponibles;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function agregarSucursal(Request $request){
        try {
            $sucursal = Usuariosucursale::create([
                'activo' => 1,
                'eliminado' => 0,
                'idSucursal' => $request['idSucursal'],
                'idUsuario' => $request['idUsuario']
            ]);

            return response()->json($sucursal, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminarSucursal(Request $request){
        try {
            $sucursal = Usuariosucursale::where('idUsuario', '=', $request['idUsuario'])->where('idSucursal', '=', $request['idSucursal'])->get()[0];
            $sucursal->delete();

            return response()->json('Sucursal eliminada correctamente', 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function imagenes(Request $request){
        try {
            $empleado = Empleado::find($request['idEmpleado']);
            $empleado->actaNacimiento = $request['actaNacimiento'];
            $empleado->comprobanteDomicilio = $request['comprobanteDomicilio'];
            $empleado->curpImagen = $request['curpImagen'];
            $empleado->ifef = $request['ifef'];
            $empleado->ifet = $request['ifet'];
            $empleado->rfcImagen = $request['rfcImagen'];
            $empleado->carta1 = $request['carta1'];
            $empleado->carta2 = $request['carta2'];
            $empleado->nssImagen = $request['nssImagen'];
            $empleado->comprobanteEstudios = $request['comprobanteEstudios'];

            return response()->json($empleado, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}
