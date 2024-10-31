<?php

	namespace App\Clases;
	use Carbon\Carbon;
	use App\Egreso;
	use App\Ingreso;
	use App\Sucursale;
	use App\Calendario;
	use App\Nivele;

	class Folios{
		function proximoIngreso($idNivel, $idCalendario, $idSucursal){
			try {
				$cantidad = Ingreso::where('idNivel', '=', $idNivel)->
	            where('idCalendario', '=', $idCalendario)->
	            where('idSucursal', '=', $idSucursal)->get();
	            $sucursal = Sucursale::find($idSucursal);
	            $calendario = Calendario::find($idCalendario);
	            $nivel = Nivele::find($idNivel);
	            $separados = explode("-", $calendario->nombre);
	            $folio = substr($separados[0], -2).$separados[1].substr($nivel->nombre, 0, 1).$sucursal->abreviatura.'-'.(count($cantidad) + 1);
	            return $folio;
			} catch (Exception $e) {
				return null;
			}
		}

		function proximoEgreso($idNivel, $idCalendario, $idSucursal){
			try {
				$cantidad = Egreso::where('idNivel', '=', $idNivel)->
	            where('idCalendario', '=', $idCalendario)->
	            where('idSucursal', '=', $idSucursal)->get();
	            $sucursal = Sucursale::find($idSucursal);
	            $calendario = Calendario::find($idCalendario);
	            $nivel = Nivele::find($idNivel);
	            $separados = explode("-", $calendario->nombre);
	            $folio = substr($separados[0], -2).$separados[1].substr($nivel->nombre, 0, 1).$sucursal->abreviatura.'-'.(count($cantidad) + 1);
	            return $folio;
			} catch (Exception $e) {
				return null;
			}
		}
	}

	
?>