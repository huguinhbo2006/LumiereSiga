<?php

	namespace App\Clases;
	use App\Clases\Egresos;
	use App\Clases\Ingresos;
	use Carbon\Carbon;
	use App\Ingreso;
	use App\Egresos;

	class Sucursales{
		function saldo($sucursal){
			try {
				$egresos = new Egresos();
				$ingresos = new Ingresos();	
				return floatval($ingresos) - floatval($egresos);
			} catch (Exception $e) {
				return null;
			}
		}
	}

	
?>