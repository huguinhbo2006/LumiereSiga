<?php

namespace App\Http\Controllers;

use App\Usuario;
use App\Empleado;
use App\Calendario;
use App\Usuariosucursale;
use App\Sucursale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
include "funciones/generales.php";

class UsuariosController extends Controller
{
    function nuevo(Request $request){
        try{
            $existe = Usuario::where('idEmpleado', '=', $request['idEmpleado'])->get();
            if(count($existe) > 0){
                $usuario = $existe[0];
                $usuario->eliminado = 0;
                $usuario->activo = 1;
                $usuario->usuario = $request['usuario'];
                $usuario->password = Hash::make($request['password']);
                $usuario->idEmpleado = $request['idEmpleado'];
                $usuario->idTipoUsuario = $request['idTipoUsuario'];
                $usuario->foto = '';
                $usuario->save();

                return response()->json($usuario, 200);
            }else{
                $usuario = Usuario::create([
                    'usuario' => $request['usuario'],
                    'password' => Hash::make($request['password']),
                    'idEmpleado' => $request['idEmpleado'],
                    'idTipoUsuario' => $request['idTipoUsuario'],
                    'activo' => 1,
                    'eliminado' => 0,
                    'foto' => ''
                ]);
                return response()->json($request, 200);
            }
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function traer(Request $request){
        try{
            $usuario = Usuario::where('idEmpleado', '=', $request['id'])->where('eliminado', '=', 0)->get();
            if(count($usuario) > 0){
                return response()->json($usuario[0], 200);
            }else{
                return response()->json(array(), 200);
            }
            return response()->json($usuario[0], 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificar(Request $request){
        try{
            $usuario = Usuario::find($request['id']);
            if($request['password'] !== '0'){
                $usuario->password = Hash::make($request['password']);
            }
            $usuario->usuario = $request['usuario'];
            $usuario->idTipoUsuario = $request['idTipoUsuario'];
            $usuario->save();

            return response()->json($usuario, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor");
        }
    }

    function eliminar(Request $request){
        try{
            $usuario = Usuario::find($request['id']);
            $usuario->eliminado = 1;
            $usuario->save();

            return response()->json($usuario, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function informacion(Request $request){
        try{
            $usuario = Usuario::join('empleados', 'idEmpleado', '=', 'empleados.id')->
            join('sucursales', 'empleados.idSucursal', '=', 'sucursales.id')->
            select('empleados.nombre', 'empleados.idSucursal', 'usuarios.foto', 'usuarios.id', 'usuarios.idTipoUsuario', 'sucursales.nombre as sucursal')->
            where('usuarios.usuario', '=', $request['usuario'])->
            where('usuarios.activo', '=', 1)->
            where('usuarios.eliminado', '=', 0)->get();
            if(count($usuario) > 0){
                $user = $usuario[0];
                $user->calendario = calendarioActual();
                $respuesta['usuario'] = $user;

                $sucursales = Usuariosucursale::join('sucursales', 'idSucursal', '=', 'sucursales.id')->
                select('sucursales.*')->
                where('usuariosucursales.idUsuario', '=', $user->id)->get();
                $sucursales[] = Sucursale::find($user->idSucursal);
                $respuesta['sucursales'] = $sucursales;

                $respuesta['permisos'] = traerPermisosUsuario($user->idTipoUsuario);
                return response()->json($respuesta, 200);
            }

            
            return response()->json('Usuario inactivo o eliminado', 400);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function modificarImagen(Request $request){
        try {
            $usuario = Usuario::find($request['id']);
            $usuario->foto = $request['foto'];
            $usuario->save();

            return response()->json($usuario, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function datosUsuario(Request $request){
        try {
            $usuario = Usuario::find($request['id']);
            $consulta = Empleado::join('departamentos', 'idDepartamento', '=', 'departamentos.id')->
                                  join('puestos', 'idPuesto', '=', 'puestos.id')->
                                  join('sucursales', 'idSucursal', '=', 'sucursales.id')->
                                  select('departamentos.nombre as departamento', 'sucursales.nombre as sucursal', 'puestos.nombre as puesto', 'empleados.*')->
                                  where('empleados.id', '=', $usuario->idEmpleado)->get();
            $consulta[0]->usuario = $usuario->usuario;
            $consulta[0]->imagen = $usuario->foto;
            return response()->json($consulta[0], 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificarPassword(Request $request){
        try {
            $usuario = Usuario::find($request['id']);
            $usuario->password = Hash::make($request['password']);
            $usuario->save();

            return response()->json($usuario, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
    
}