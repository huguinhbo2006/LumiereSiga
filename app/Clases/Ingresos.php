<?php

	namespace App\Clases;
	use Carbon\Carbon;
	use App\Ingreso;
	use App\Rubro;
	use App\Tiposingreso;
	use App\Calendario;
	use App\Formaspago;
	use App\Metodospago;
	use App\Nivele;
	use App\Cuenta;
	use App\Banco;
	use App\Sucursale;
	use App\Alumnoabono;
	use Illuminate\Support\Facades\DB;

	class Ingresos{
		function totalEfectivo($sucursal){
			$total = Ingreso::where('activo', '=', 1)->where('idSucursal', '=', $sucursal)->where('idFormaPago', '=', 1)->sum('monto');
			return $total + 0;
		}

		function listas(){
			try {
				$respuesta = array();
				$respuesta['rubros'] = Rubro::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
	            $respuesta['tipos'] = Tiposingreso::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
	            $respuesta['calendarios'] = Calendario::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
	            $respuesta['formas'] = Formaspago::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
	            $respuesta['metodos'] = Metodospago::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
	            $respuesta['niveles'] = Nivele::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
	            $respuesta['cuentas'] = Cuenta::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
	            $respuesta['bancos'] = Banco::where('eliminado', '=', 0)->get();
	            $respuesta['sucursales'] = Sucursale::where('eliminado', '=', 0)->get();
	            return $respuesta;
			} catch (Exception $e) {
				return null;
			}
		}

		function completar($ingreso){
			try {
				$ingreso->calendario = Calendario::find($ingreso->idCalendario)->nombre;
		        $ingreso->nivel = Nivele::find($ingreso->idNivel)->nombre;
		        $ingreso->rubro = Rubro::find($ingreso->idRubro)->nombre;
		        $ingreso->forma = Formaspago::find($ingreso->idFormaPago)->nombre;
		        $ingreso->cuenta = (intval($ingreso->idCuenta) > 0) ? Cuenta::find($ingreso->idCuenta)->nombre : 'N/A';
		        $ingreso->montoFormato = '$'.number_format($ingreso->monto, 2, '.', ',');
		        if(intval($ingreso->idFormaPago) !== 1 ){
		            if(strlen($ingreso->imagen) <= 0){
		                $ingreso->title = "Falta Voucher";
		                $ingreso->hayVoucher = 'fas fa-times text-danger';
		            }else{
		                $ingreso->hayVoucher = 'fas fa-check text-success';
		            }
		        }else{
		            $ingreso->hayVoucher = 'N/A';
		        }

		        return $ingreso;
			} catch (Exception $e) {
				return null;
			}
		}

		function busquedaGeneral(){
			try {
				$registros = Ingreso::leftjoin('calendarios', 'idCalendario', '=', 'calendarios.id')->
		            leftjoin('sucursales', 'idSucursal', '=', 'sucursales.id')->
		            leftjoin('rubros', 'idRubro', '=', 'rubros.id')->
		            leftjoin('tiposingresos', 'idTipo', '=', 'tiposingresos.id')->
		            leftjoin('formaspagos', 'idFormaPago', '=', 'formaspagos.id')->
		            leftjoin('metodospagos', 'idMetodoPago', '=', 'metodospagos.id')->
		            leftjoin('usuarios', 'idUsuario', '=', 'usuarios.id')->
		            leftjoin('niveles', 'idNivel', '=', 'niveles.id')->
		            leftjoin('cuentas', 'idCuenta', '=', 'cuentas.id')->
		            leftjoin('vales', 'vales.idIngreso', '=', 'ingresos.id')->
		            select(
		                'ingresos.id',
		                'ingresos.monto',
		                'ingresos.observaciones',
		                'ingresos.idRubro', 
		                'ingresos.idTipo',
		                'ingresos.idSucursal',
		                'ingresos.idCalendario',
		                'ingresos.idFormaPago',
		                'ingresos.idMetodoPago',
		                'ingresos.idNivel',
		                'ingresos.idUsuario',
		                'ingresos.referencia',
		                'ingresos.activo',
		                'ingresos.eliminado',
		                'ingresos.created_at',
		                'ingresos.updated_at',
		                'ingresos.folio',
		                'ingresos.idBanco',
		                'ingresos.nombreCuenta',
		                'ingresos.numeroReferencia',
		                'ingresos.idCuenta',
		                'ingresos.fecha',
		                'calendarios.nombre as calendario',
		                'niveles.nombre as nivel',
		                'rubros.nombre as rubro',
		                'formaspagos.nombre as forma',
		                'metodospagos.nombre as metodo',
		                DB::raw("DATE_FORMAT(ingresos.created_at, '%d-%m-%Y %H:%i:%s') as fechaFormato"),
		                DB::raw("(CASE 
		                            WHEN(ingresos.idRubro = 2 AND ingresos.idTipo = 3) THEN vales.folio
		                            ELSE ingresos.concepto
		                            END) AS concepto"),
		                        'formaspagos.nombre as forma',
		                DB::raw("IF(ingresos.idFormaPago <> 1, cuentas.nombre, 'N/A') as cuenta"),
		                DB::raw("IF(ingresos.idFormaPago <> 1, IF(LENGTH(ingresos.imagen) > 0, 'SI', 'NO'), 'N/A') as hayVoucher"),
		                DB::raw("CONCAT('$',FORMAT(ingresos.monto,2)) AS montoFormato"),
		                DB::raw("IF(ingresos.activo = 0, 'bg-rojo', '') as bg")
		            );
		        return $registros;
			} catch (Exception $e) {
				return null;
			}
		}

		function traerAbono($id){
			try {
				$abono = Alumnoabono::where('idIngreso', '=', $id)->get();
				return (count($abono) > 0) ? $abono[0] : null;
			} catch (Exception $e) {
				return null;
			}
		}
	}

	
?>