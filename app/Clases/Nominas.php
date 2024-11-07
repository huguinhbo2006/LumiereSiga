<?php

	namespace App\Clases;
	use Carbon\Carbon;
	use App\Nomina;
	use App\Sucursale;
	use App\Percepcione;
	use App\Deduccione;
	use App\Egreso;
	use App\Nominaegreso;
	use App\Clases\Folios;
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

		function autorizadas($sucursalID){
			try {
				$nominas = Nomina::join('empleados', 'idEmpleado', '=', 'empleados.id')->
	               join('sucursales', 'nominas.idSucursal', '=', 'sucursales.id')->
	               join('calendarios', 'idCalendario', '=', 'calendarios.id')->
	               join('departamentos', 'nominas.idDepartamento', '=', 'departamentos.id')->
	               join('niveles', 'idNivel', '=', 'niveles.id')->
	               select('nominas.id', 
	                      'nominas.folio', 
	                      'departamentos.nombre as departamento', 
	                      'niveles.nombre as nivel', 
	                      'sucursales.nombre as sucursal', 
	                      'empleados.nombre as empleado', 
	                      'calendarios.nombre as calendario',
	                      DB::raw("CONCAT('$',FORMAT(nominas.total, 2)) AS total"))->
	               where('nominas.idSucursal', '=', $sucursalID)->
	               where('nominas.eliminado', '=', 0)->
	               where('estatus', '=', 1)->get();
	            return $nominas;
			} catch (Exception $e) {
				return null;
			}
		}

		function datos($nominaID){
			try {
				$nomina = Nomina::find($nominaID);
				$nomina->percepcionesEfectivo = $this->percepcionesEfectivo($nominaID);
				$nomina->percepcionesDeposito = $this->percepcionesDeposito($nominaID);
				$nomina->deduccionesEfectivo = $this->deduccionesEfectivo($nominaID);
				$nomina->deduccionesDeposito = $this->deduccionesDeposito($nominaID);
				$nomina->totalEfectivo = floatval($nomina->percepcionesEfectivo) - floatval($nomina->deduccionesEfectivo);
				$nomina->totalDeposito = floatval($nomina->percepcionesDeposito) - floatval($nomina->deduccionesDeposito);
				return $nomina;
			} catch (Exception $e) {
				return null;
			}
		}

		function percepcionesEfectivo($nominaID){
			try {
				return Percepcione::where('idNomina', '=', $nominaID)->where('idFormaPago', '=', 1)->sum('monto');
			} catch (Exception $e) {
				return null;
			}
		}

		function percepcionesDeposito($nominaID){
			try {
				return Percepcione::where('idNomina', '=', $nominaID)->where('idFormaPago', '=', 4)->sum('monto');
			} catch (Exception $e) {
				return null;
			}
		}

		function deduccionesEfectivo($nominaID){
			try {
				return Deduccione::where('idNomina', '=', $nominaID)->where('idFormaPago', '=', 1)->sum('monto');
			} catch (Exception $e) {
				return null;
			}
		}

		function deduccionesDeposito($nominaID){
			try {
				return Deduccione::where('idNomina', '=', $nominaID)->where('idFormaPago', '=', 4)->sum('monto');
			} catch (Exception $e) {
				return null;
			}
		}

		function crearEgresoEfectivo($nomina){
			try {
				$folios = new Folios();

				$folio = $folios->proximoEgreso($nomina->idNivel, $nomina->idCalendario, $nomina->idSucursal);
				$egreso = Egreso::create([
                    'concepto' => 'Pago en Efectivo a Nomina',
                    'monto' => $totalEfectivo,
                    'observaciones' => $nomina->observaciones,
                    'idRubro' => 3,
                    'idTipo' => 4,
                    'idSucursal' => $nomina->idSucursal,
                    'idCalendario' => $nomina->idCalendario,
                    'idFormaPago' => 1,
                    'idUsuario' => $request['usuario'],
                    'referencia' => 3,
                    'idNivel' => $nomina->idNivel,
                    'folio' => $folio,
                    'idCuenta' => 0,
                    'activo' => 1,
                    'eliminado' => 0,
                ]);
                $primer = Nominaegreso::create([
                    'idNomina' => $nomina->id,
                    'idEgreso' => $egreso->id,
                    'eliminado' => 0,
                    'activo' => 1,
                    'tipo' => 1
                ]);
			} catch (Exception $e) {
				return null;
			}
		}

		function crearEgresoDeposito(){
			try {
				
			} catch (Exception $e) {
				return null;
			}
		}
	}
?>