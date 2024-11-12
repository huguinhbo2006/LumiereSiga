<?php

	namespace App\Clases;
	use Carbon\Carbon;

	class Generales{

		function formatearObjetoArray($objeto){
			try {
				$array = array();
				foreach (json_decode($objeto) as $key => $valor) {
	                $array[] = $valor;
	            }
	            return $array;
			} catch (Exception $e) {
				return null;
			}
			
		}

		function listaObjetosArray($lista){
			try {
				if(!is_null($lista)){
					$final = array();
					foreach ($lista as $dato) {
						$final[] = $this->formatearObjetoArray($dato);
					}
					return $final;	
				}else{
					return [];
				}
			} catch (Exception $e) {
				return null;
			}
		}
	}
?>