<?php

	namespace App\Clases;
	use Carbon\Carbon;
	use App\Nomina;
	use App\Sucursale;
	use Illuminate\Support\Facades\DB;

	class Nominas{

		function listas(){
			try {
				return array(
					'sucursales' => Sucursale::where('eliminado', '=', 0)->get()
				);
			} catch (Exception $e) {
				return null;
			}
		}

		function creadas(){
			try {
				return Nomina::join('empleados', 'idEmpleado', '=', 'empleados.id')->
	            join('sucursales', 'nominas.idSucursal', '=', 'sucursales.id')->
	            join('calendarios', 'idCalendario', '=', 'calendarios.id')->
	            join('departamentos', 'nominas.idDepartamento', '=', 'departamentos.id')->
	            join('niveles', 'idNivel', '=', 'niveles.id')->
	            select(
	            	'nominas.*', 
	            	'departamentos.nombre as departamento',
	            	'niveles.nombre as nivel',
	            	'sucursales.nombre as sucursal',
	            	'empleados.nombre as empleado',
	            	'calendarios.nombre as calendario',
	            	DB::raw('CONCAT("$", nominas.total) as totalFormato')
	            )->
	            where('nominas.eliminado', '=', 0)->
	            where('estatus', '=', 0)->get();
			} catch (Exception $e) {
				return null;
			}
		}

		function datos(){
			try {
			} catch (Exception $e) {
				return null;
			}
		}
	}
?>