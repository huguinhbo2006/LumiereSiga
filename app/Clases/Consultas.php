<?php

	namespace App\Clases;
	use Illuminate\Support\Facades\DB;

	class Consultas{
		function capturarConsultas(){
			DB::enableQueryLog();
		}

		function obtenerConsultas(){
			return DB::getQueryLog();
		}

		function abrirRollback(){
			return DB::beginTransaction();
		}

		function finalizarRollback(){
			return DB::commit();
		}

		function activarRollback(){
			return DB::rollback();
		}
	}

	
?>