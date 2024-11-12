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

		function start(){
			return DB::beginTransaction();
		}

		function commit(){
			return DB::commit();
		}

		function rollback(){
			return DB::rollback();
		}
	}

	
?>