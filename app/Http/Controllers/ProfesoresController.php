<?php

namespace App\Http\Controllers;
use App\Empleado;
use App\Usuario;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
include "funciones/FuncionesGenerales.php";



class ProfesoresController extends BaseController
{
    function mostrar(){
        try{
            $profesores = Empleado::join('departamentos', 'idDepartamento', '=', 'departamentos.id')->join('sucursales', 'idSucursal', '=', 'sucursales.id')->join('puestos', 'idPuesto', '=', 'puestos.id')->select('empleados.*', 'departamentos.nombre as departamento', 'sucursales.nombre as sucursal', 'puestos.nombre as puesto')->where('empleados.eliminado', '=', 0)->where('empleados.id', '<>', 1)->where('departamentos.id', '=', 1)->get();
            return response()->json($profesores, 200);
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
            
            $profesor = Empleado::create([
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
                
                //'idLetra' => $empresa['idLetra'],
                'idSucursal' => $empresa['idSucursal'],
                'idDepartamento' => $empresa['idDepartamento'],
                'idPuesto' => $empresa['idPuesto'],
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

            return response()->json($profesor, 200);
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

            $profesor = Empleado::find($personales['id']);
            //Personales
            $profesor->nombre = $personales['nombre'];
            $profesor->telefono = $personales['telefono'];
            $profesor->celular = $personales['celular'];
            $profesor->fechaNacimiento = $fechaNacimiento;
            $profesor->estadoCivil = $personales['estadoCivil'];
            $profesor->correo = $personales['correo'];

            //Domicilio
            
            $profesor->calle = $domicilio['calle'];
            $profesor->numeroInterior = $domicilio['numeroInterior'];
            $profesor->numeroExterior = $domicilio['numeroExterior'];
            $profesor->colonia = $domicilio['colonia'];
            $profesor->idMunicipio = $domicilio['idMunicipio'];
            $profesor->idEstado = $domicilio['idEstado'];
            $profesor->codigoPostal = $domicilio['codigoPostal'];
            
            //Fiscales
            $profesor->nss = $fiscales['nss'];
            $profesor->rfc = $fiscales['rfc'];
            $profesor->curp = $fiscales['curp'];
            $profesor->fechaAltaIMSS = $fechaAltaIMSS;
            $profesor->fechaIngreso = $fechaIngreso;
            $profesor->cuentaBancaria = $fiscales['cuentaBancaria'];

            //Empresa
            $profesor->idSucursal = $empresa['idSucursal'];
            $profesor->idDepartamento = $empresa['idDepartamento'];
            $profesor->idPuesto = $empresa['idPuesto'];
            $profesor->idLetra = $empresa['idLetra'];
            
            
            //Imagenes
            $profesor->actaNacimiento = $imagenes['actaNacimiento'];
            $profesor->comprobanteDomicilio = $imagenes['comprobanteDomicilio'];
            $profesor->curpImagen = $imagenes['curpImagen'];
            $profesor->ifef = $imagenes['ifef'];
            $profesor->ifet = $imagenes['ifet'];
            $profesor->rfcImagen = $imagenes['rfcImagen'];
            $profesor->carta1 = $imagenes['carta1'];
            $profesor->carta2 = $imagenes['carta2'];
            $profesor->nssImagen = $imagenes['nssImagen'];
            $profesor->comprobanteEstudios = $imagenes['comprobanteEstudios'];
            
            $profesor->save();
            return response()->json($profesor, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function activar(Request $request){
        try{
            $profesor = Empleado::find($request['id']);
            $profesor->fechaSalida = null;
            $profesor->activo = 1;
            $profesor->save();

            return response()->json($profesor, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function desactivar(Request $request){
        try{
            $carbon = new \Carbon\Carbon();
            $date = $carbon->now();
            $profesor = Empleado::find($request['id']);
            $profesor->activo = 0;
            $profesor->fechaSalida = $date;
            $profesor->save();

            return response()->json($profesor, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function eliminar(Request $request){
        try{
            $profesor = Empleado::find($request['id']);
            $profesor->eliminado = 1;
            $profesor->save();

            return response()->json($profesor, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }
}
