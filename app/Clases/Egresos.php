<?php

	namespace App\Clases;
	use Carbon\Carbon;
	use App\Egreso;
	use App\Rubrosegreso;
	use App\Tiposegreso;
	use App\Calendario;
	use App\Formaspago;
	use App\Metodospago;
	use App\Nivele;
	use App\Cuenta;
	use App\Ingreso;
	use App\Alumnodevolucione;

	use Illuminate\Support\Facades\DB;

	class Egresos{
		function totalEfectivo($sucursal){
			$total = Egreso::where('activo', '=', 1)->where('idSucursal', '=', $sucursal)->where('idFormaPago', '=', 1)->sum('monto');
			return $total + 0;
		}

		function busquedaGeneral(){
			try {
				$registros = Egreso::leftjoin('calendarios', 'calendarios.id', '=', 'egresos.idCalendario')->
	               leftjoin('niveles', 'niveles.id', '=', 'egresos.idNivel')->
	               leftjoin('rubrosegresos', 'rubrosegresos.id', '=', 'egresos.idRubro')->
	               leftjoin('tiposegresos', 'tiposegresos.id', '=', 'egresos.idTipo')->
	               leftjoin('formaspagos', 'formaspagos.id', '=', 'egresos.idFormaPago')->
	               leftjoin('nominaegresos', 'nominaegresos.idEgreso', '=', 'egresos.id')->
	               leftjoin('nominas', 'nominas.id', '=', 'nominaegresos.idNomina')->
	               leftjoin('empleados', 'empleados.id', '=', 'nominas.idEmpleado')->
	               leftjoin('departamentos', 'departamentos.id', '=', 'nominas.idDepartamento')->
	               leftjoin('vales', 'vales.idEgreso', '=', 'egresos.id')->
	               select(
	                'egresos.id',
	                'egresos.referencia',
	                'egresos.monto',
	                'egresos.idRubro',
	                'egresos.idTipo',
	                'egresos.idCalendario',
	                'egresos.idNivel',
	                'egresos.idFormaPago',
	                'egresos.observaciones',
	                'niveles.nombre as nivel',
	                'calendarios.nombre as calendario',
	                'egresos.folio',
	                DB::raw('DATE_FORMAT(egresos.created_at, "%d-%m-%Y %H:%i:%s") as fechaFormato'),
	                'rubrosegresos.nombre as rubro',
	                DB::raw("(CASE 
	                    WHEN(egresos.idRubro = 3 AND egresos.idTipo = 4) THEN empleados.nombre
	                    ELSE egresos.concepto
	                    END) AS concepto"),
	                DB::raw("(CASE 
	                    WHEN(egresos.idRubro = 3 AND egresos.idTipo = 4) THEN departamentos.nombre
	                    WHEN(egresos.idRubro = 2 AND egresos.idTipo = 2) THEN vales.folio
	                    ELSE tiposegresos.nombre
	                    END) AS tipo"),
	                'formaspagos.nombre as forma',
	                DB::raw("CONCAT('$',FORMAT(egresos.monto,2)) AS montoFormato"),
	                DB::raw("IF(egresos.activo = 0, 'bg-rojo', '') AS bg")
	               );
                return $registros;
			} catch (Exception $e) {
				return null;
			}
		}

		function listas(){
			try {
				$listas = array();
				$listas['rubros'] = Rubrosegreso::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
	            $listas['tipos'] = Tiposegreso::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
	            $listas['calendarios'] = Calendario::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
	            $listas['formas'] = Formaspago::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
	            $listas['metodos'] = Metodospago::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
	            $listas['niveles'] = Nivele::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
	            $listas['cuentas'] = Cuenta::where('eliminado', '=', 0)->where('activo', '=', 1)->get();

	            return $listas;
			} catch (Exception $e) {
				return null;
			}
		}

		function completar($egreso){
			try {
				$fecha = Carbon::parse($egreso->created_at);
	            $egreso->calendario = Calendario::find($egreso->idCalendario)->nombre;
	            $egreso->nivel = Nivele::find($egreso->idNivel)->nombre;
	            $egreso->fechaFormato = $fecha->format('d-m-Y h:i:s');
	            $egreso->rubro = Rubrosegreso::find($egreso->idRubro)->nombre;
	            $egreso->tipo = Tiposegreso::find($egreso->idTipo)->nombre;
	            $egreso->forma = Formaspago::find($egreso->idFormaPago)->nombre;
	            $egreso->montoFormato = '$'.number_format($egreso->monto, 2, '.', ',');

	            return $egreso;	
			} catch (Exception $e) {
				return null;
			}
		}

		function eliminarDevolucion($egreso){
			try {
				$devolucion = Alumnodevolucione::where('idEgreso', '=', $egreso)->get();
				if(count($devolucion) > 0){
					$devolucionFinal = Alumnodevolucione::find($devolucion->id);
	                $devolucionFinal->eliminado = 1;
	                $devolucionFinal->save();	
				}
                
                return true;
			} catch (Exception $e) {
				return response()->json('Error en el servidor', 400);
			}
		}

		function egresosDiariosUsuario($usuarioID, $sucursalID){
			try {
				return Egreso::join('rubrosegresos', 'idRubro', '=', 'rubrosegresos.id')->
	            join('formaspagos', 'idFormaPago', '=', 'formaspagos.id')->
	            select(
	                'egresos.folio',
	                'rubrosegresos.nombre as rubro',
	                'egresos.concepto',
	                DB::raw("CONCAT('$',FORMAT(egresos.monto,2)) AS monto"),
	                'formaspagos.nombre as forma',
	                DB::raw("(CASE 
	                            WHEN(egresos.referencia = 1) THEN 'Comun'
	                            WHEN(egresos.referencia = 2) THEN 'Devolucion'
	                            WHEN(egresos.referencia = 3) THEN 'Nomina'
	                            WHEN(egresos.referencia = 4) THEN 'Vale'
	                            WHEN(egresos.referencia = 5) THEN 'Transferencia'
	                            ELSE 'Desconocido'
	                            END) AS referencia"),
	                DB::raw('DATE_FORMAT(egresos.created_at, "%d-%m-%Y %H:%i:%s") as fecha')
	            )->
	            where('idUsuario', '=', $usuarioID)->
	            where('idSucursal', '=', $sucursalID)->
	            where('egresos.eliminado', '=', 0)->
	            whereRaw("DATE_FORMAT(egresos.created_at,'%y-%m-%d') = CURDATE()")->get();
			} catch (Exception $e) {
				return null;
			}
		}

		function totalEfectivoUsuarioDia($sucursal, $usuario){
			$total = Egreso::where('activo', '=', 1)->
			where('idSucursal', '=', $sucursal)->
			where('idFormaPago', '=', 1)->
			where('idUsuario', '=', $usuario)->
			whereRaw("DATE_FORMAT(egresos.created_at,'%y-%m-%d') = CURDATE()")->
			sum('monto');
			return $total + 0;
		}
	}
?>