<?php
	use App\Formaspago;
	namespace App\Clases;
	use Illuminate\Support\Facades\DB;

	class Catalogos{
		function formasPagos(){
			try {
				return Formaspago::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
			} catch (Exception $e) {
				return null;
			}
		}
	}

	
?>