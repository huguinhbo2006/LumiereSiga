<?php 
	use App\Examenporcentaje;
	use App\Seccionesporcentaje;

	use Illuminate\Support\Facades\DB;

	function traerPorcentajesExamen($idExamen){
		try {
			$porcentajes = Examenporcentaje::where('idExamen', '=', $idExamen)->
                                             where('eliminado', '=', 0)->get();
            return $porcentajes;
		} catch (Exception $e) {
			return null;
		}
	}
?>