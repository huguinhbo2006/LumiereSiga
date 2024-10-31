<?php

	namespace App\Clases;
	use Carbon\Carbon;
	use App\Ficha;
	use Illuminate\Support\Facades\DB;

	class Fichas{
		function costo($ficha){
			try {
				$costo = Ficha::join('grupos', 'idGrupo', '=', 'grupos.id')->
				join('altacursos', 'idAltaCurso', '=', 'altacursos.id')->
				leftjoin('alumnodescuentos', 'fichas.id', '=', 'alumnodescuentos.idFicha')->
				select(
					DB::raw('(altacursos.precio - alumnodescuentos.monto) as descuento')
				)->where('fichas.id', '=', $ficha)->get();
				return (count($costo) > 0) ? $costo[0]->descuento : 0;
			} catch (Exception $e) {
				return null;
			}
		}
	}
?>