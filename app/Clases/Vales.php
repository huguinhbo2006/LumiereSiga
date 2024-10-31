<?php

	namespace App\Clases;
	use Illuminate\Support\Facades\DB;
	use App\Vale;
	use App\Ingreso;
	use App\Clases\Folios;

	class Vales{

		function recibidos(){
			try {
				return Vale::leftjoin('calendarios', 'vales.idCalendario', '=', 'calendarios.id')->
	            join('sucursales', 'idSucursalSalida', '=', 'sucursales.id')->
	            select(
	                'vales.*',
	                DB::raw('CONCAT(calendarios.inicio, " - ", calendarios.fin) as calendario'),
	                'sucursales.nombre as sucursal',
	                DB::raw("CONCAT('$',FORMAT(vales.monto,2)) AS montoFormato"),
	                DB::raw("DATE_FORMAT(vales.created_at, '%d-%m-%Y %H:%i:%s') as fechaFormato")
	            )->
	            where('vales.eliminado', '=', 0)->
	            where('vales.aceptado', '=', 0)->get();
			} catch (Exception $e) {
				return null;
			}
		}

		function crearIngreso($datos){
			try {
				$funcionesFolios = new Folios();
				$folio = $funcionesFolios->proximoIngreso($datos['idNivel'], $datos['idCalendario'], 1);

				$ingreso = Ingreso::create([
	                'concepto' => 'Vale de Sucursal '.$datos['sucursal'],
	                'monto' => $datos['monto'],
	                'observaciones' => $datos['observaciones'],
	                'idRubro' => 2,
	                'idTipo' => 3,
	                'idSucursal' => $datos['idSucursalEntrada'],
	                'idCalendario' => $datos['idCalendario'],
	                'idFormaPago' => 1,
	                'idMetodoPago' => 1,
	                'idUsuario' => $datos['usuarioID'],
	                'idNivel' => $datos['idNivel'],
	                'folio' => $folio,
	                'referencia' => 4,
	                'activo' => 1,
	                'eliminado' => 0,
	            ]);
				return $ingreso;
			} catch (Exception $e) {
				return null;
			}
		}
	}

	
?>