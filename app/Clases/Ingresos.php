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
	use App\Ingresosolicitude;
	use Illuminate\Support\Facades\DB;

	class Ingresos{
		function totalEfectivo($sucursal){
			$total = Ingreso::where('activo', '=', 1)->where('idSucursal', '=', $sucursal)->where('idFormaPago', '=', 1)->sum('monto');
			return $total + 0;
		}

		function listas(){
			try {
	            return $array(
	            	'rubros' => Rubro::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	            	'tipos' => Tiposingreso::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	            	'calendarios' => Calendario::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	            	'formas' => Formaspago::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	            	'metodos' => Metodospago::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	            	'niveles' => Nivele::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	            	'cuentas' => Cuenta::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	            	'bancos' => Banco::where('eliminado', '=', 0)->get(),
	            	'sucursales' => Sucursale::where('eliminado', '=', 0)->get()
	            );
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

		function solicitudes(){
			try {
				$solicitudes = Ingresosolicitude::join('ingresos', 'idIngreso', '=', 'ingresos.id')->
		        join('usuarios', 'idUsuarioSolicito', '=', 'usuarios.id')->
		        join('empleados', 'usuarios.idEmpleado', '=', 'empleados.id')->
		        join('rubros', 'ingresosolicitudes.idRubro', '=', 'rubros.id')->
		        join('tiposingresos', 'ingresosolicitudes.idTipo', '=', 'tiposingresos.id')->
		        join('formaspagos', 'ingresosolicitudes.idFormaPago', '=', 'formaspagos.id')->
		        join('metodospagos', 'ingresosolicitudes.idMetodoPago', '=', 'metodospagos.id')->
		        leftjoin('bancos', 'ingresosolicitudes.idBanco', '=', 'bancos.id')->
		        leftjoin('cuentas', 'ingresosolicitudes.idCuenta', '=', 'cuentas.id')->
		        select(
		            'ingresos.folio',
		            'ingresosolicitudes.*',
		            'empleados.nombre as empleado',
		            'rubros.nombre as rubro',
		            'tiposingresos.nombre as tipo',
		            'formaspagos.nombre as forma',
		            'metodospagos.nombre as metodo',
		            'bancos.nombre as banco',
		            'cuentas.nombre as cuenta',
		            DB::raw("(CASE 
		                        WHEN(ingresosolicitudes.estatus = 2) THEN 'bg-verde'
		                        WHEN(ingresosolicitudes.estatus = 3) THEN 'bg-rojo'
		                        END) AS bg")
		        )->get();

		        return $solicitudes;
			} catch (Exception $e) {
				return null;
			}
		}

		function modificar($dato){
			try {
				$ingreso = Ingreso::find($dato['id']);
	            $ingreso->concepto = $dato['concepto'];
	            $ingreso->monto = $dato['monto'];
	            $ingreso->observaciones = $dato['observaciones'];
	            $ingreso->idRubro = $dato['idRubro'];
	            $ingreso->idTipo = $dato['idTipo'];
	            $ingreso->idCalendario = $dato['idCalendario'];
	            $ingreso->idFormaPago = $dato['idFormaPago'];
	            $ingreso->idMetodoPago = $dato['idMetodoPago'];
	            $ingreso->idNivel = $dato['idNivel'];
	            $ingreso->idBanco = (intval($dato['idFormaPago']) === 1) ? null : $dato['idBanco'];
	            $ingreso->nombreCuenta = (intval($dato['idFormaPago']) === 1) ? null : $dato['nombreCuenta'];
	            $ingreso->numeroReferencia = (intval($dato['idFormaPago']) === 1) ? null : $dato['numeroReferencia'];
	            $ingreso->idCuenta = (intval($dato['idFormaPago']) === 1) ? null : $dato['idCuenta'];
	            $ingreso->save();

	            return $ingreso;
			} catch (Exception $e) {
				return null;
			}
		}

		function ingresosDiariosUsuario($usuarioID, $sucursalID){
			try {
		        return Ingreso::join('rubros', 'idRubro', '=', 'rubros.id')->
		        join('formaspagos', 'idFormaPago', '=', 'formaspagos.id')->
		        select(
		            'ingresos.folio',
		            'rubros.nombre as rubro',
		            'ingresos.concepto',
		            DB::raw("CONCAT('$',FORMAT(ingresos.monto,2)) AS monto"),
		            'formaspagos.nombre as forma',
		            DB::raw("(CASE 
		                        WHEN(ingresos.referencia = 1) THEN 'Comun'
		                        WHEN(ingresos.referencia = 2) THEN 'Inscripcion'
		                        WHEN(ingresos.referencia = 3) THEN 'Abonos'
		                        WHEN(ingresos.referencia = 4) THEN 'Vale'
		                        WHEN(ingresos.referencia = 5) THEN 'Transferencia'
		                        ELSE 'Desconocido'
		                        END) AS referencia"),
		            DB::raw('DATE_FORMAT(ingresos.created_at, "%d-%m-%Y %H:%i:%s") as fecha')
		        )->
		        where('idUsuario', '=', $usuarioID)->
		        where('idSucursal', '=', $sucursalID)->
		        where('ingresos.eliminado', '=', 0)->
		        whereRaw("DATE_FORMAT(ingresos.created_at,'%y-%m-%d') = CURDATE()")->get();
	      	} catch (Exception $e) {
	        	return null;
	      	}
		}

		function totalEfectivoUsuarioDia($sucursal, $usuario){
			$total = Ingreso::where('activo', '=', 1)->
			where('idSucursal', '=', $sucursal)->
			where('idFormaPago', '=', 1)->
			where('idUsuario', '=', $usuario)->
			whereRaw("DATE_FORMAT(ingresos.created_at,'%y-%m-%d') = CURDATE()")->
			sum('monto');
			return $total + 0;
		}
	}
?>