<?php 
	use App\Ingreso;
	use App\Ficha;
	use App\Egreso;
	use App\Formaspago;
	use App\Vale;
	use App\Valeadministrativo;
	use App\Sucursale;
	use App\Calendario;
	use App\Nivele;
	use App\Percepcione;
	use App\Deduccione;
	use App\Nominaegreso;
	use App\Nomina;
	use App\Alumnocargo;
	use App\Alumnoabono;
	use App\Alumnodescuento;
	use App\Alumnodevolucione;
	use App\Alumnoextra;
	use App\Opcionespermiso;
	use App\Modulospermiso;
	use App\Modulo;
	use App\Opcione;
	use App\Log;
	use App\Altacurso;
	use App\Valesgerenciale;
	use Carbon\Carbon;

	function burbuja(&$arreglo){
	    $longitud = count($arreglo);
	    for ($i = 0; $i < $longitud; $i++) {
	        for ($j = 0; $j < $longitud - 1; $j++) {
	            if (mayor($arreglo[$j]['fecha'], $arreglo[$j + 1]['fecha'])) {
	                $temporal = $arreglo[$j];
	                $arreglo[$j] = $arreglo[$j + 1];
	                $arreglo[$j + 1] = $temporal;
	            }
	        }
	    }
	}

	function existeAltaCurso($curso){
		try {
			$existe = Altacurso::where('idNivel', '=', $curso['idNivel'])->
        		where('idSubnivel', '=', $curso['idSubnivel'])->
        		where('idCategoria', '=', $curso['idCategoria'])->
        		where('idModalidad', '=', $curso['idModalidad'])->
        		where('idCalendario', '=', $curso['idCalendario'])->
        		where('idCurso', '=', $curso['idCurso'])->
        		where('idSede', '=', $curso['idSede'])->
        		where('eliminado', '=', 0)->
        		get();

        	
        	if(count($existe) > 0){
        		if(mayor($curso['inicio'], $existe[0]->fin )){
        			return false;
        		}
        		else{
        			return true;
        		}
        	}else{
        		return false;
        	}
		} catch (Exception $e) {
			return true;
		}
	}

	function agregarLog($mensaje, $usuario){
		try {
			Log::create([
                'idUsuario' => $usuario,
                'accion' => $mensaje,
                'activo' => 1,
                'eliminado' => 0
            ]);
            return true;
		} catch (Exception $e) {
			return null;
		}
	}

	function mayor($fecha1, $fecha2){
		try {
			$fechaUno = Carbon::parse($fecha1);
			$fechaDos = Carbon::parse($fecha2);
			if($fechaUno->gt($fechaDos)){
				return true;
			}
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	function menor($fecha1, $fecha2){
		try {
			$fechaUno = Carbon::parse($fecha1);
			$fechaDos = Carbon::parse($fecha2);
			if($fechaUno->lt($fechaDos)){
				return true;
			}
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	function igual($fecha1, $fecha2){
		try {
			$fechaUno = Carbon::parse($fecha1);
			$fechaDos = Carbon::parse($fecha2);
			if($fechaUno->eq($fechaDos)){
				return true;
			}
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	function traerTotalIngresos($sucursal){
		try {
			$ingreso = Ingreso::select(DB::raw('SUM(ingresos.monto) as total'))->
                                  where('activo', '=', 1)->
                                  where('idSucursal', '=', $sucursal)->
                                  where('idFormaPago', '=', 1)->
                                  get();
            $ingresoFinal = $ingreso[0]->total + 0;
            return $ingresoFinal;
		} catch (Exception $e) {
			return null;
		}
	}

	function traerTotalEgresos($sucursal){
		try {
			$egreso = Egreso::select(DB::raw('SUM(egresos.monto) as total'))->
                                  where('activo', '=', 1)->
                                  where('idSucursal', '=', $sucursal)->
                                  where('idFormaPago', '=', 1)->
                                  get();

            $egresoFinal = $egreso[0]->total + 0; 
            return $egresoFinal;
		} catch (Exception $e) {
			return null;
		}
	}

	function traerTotalVales($sucursal){
		try {
			$vales = Vale::select(DB::raw('SUM(vales.monto) as total'))->
                                  where('eliminado', '=', 0)->
                                  where('idSucursalSalida', '=', $sucursal)->
                                  where('aceptado', '=', 0)->
                                  get();
            $total = $vales[0]->total + 0;
            return $total;
		} catch (Exception $e) {
			return null;
		}
	}

	function saldoTotalSucursal($sucursal){
		try {
			$ingresos = traerTotalIngresos($sucursal) + 0;
			$egresos = traerTotalEgresos($sucursal) + 0;
			//$vales = traerTotalVales($sucursal) + 0;
			$totalValeAdministrativo = 0;
			$Valeadministrativo = Valeadministrativo::where('idSucursal', '=', $sucursal)->get();
			if(count($Valeadministrativo) > 0){
				$totalValeAdministrativo = $Valeadministrativo[0]->monto;
			}
			$total = $ingresos - $egresos -  $totalValeAdministrativo;
			return $total;
		} catch (Exception $e) {
			return null;
		}
	}

	function compararDisponibilidades($fechaInicio1, $fechaInicio2, $fechaFin1, $fechaFin2){
		try {
			if(igual($fechaInicio1, $fechaInicio2)){
				return false;                        
            }
            if(mayor($fechaInicio1, $fechaInicio2) && menor($fechaInicio1, $fechaFin2)){
                return false;
            }
            if(menor($fechaInicio1, $fechaInicio2) && mayor($fechaFin1, $fechaInicio2)){
                return false;
            }
            return true;
		} catch (Exception $e) {
			return null;
		}
	}

	function formatearFecha($fecha){
		try {
			$date = Carbon::parse($fecha)->locale('es');
			return ucfirst($date->dayName).' '.$date->day.' de '.ucfirst($date->monthName).' del '.$date->year;
		} catch (Exception $e) {
			return null;
		}
	}

	function formatearFechaHora($fecha){
		try {
			$date = Carbon::parse($fecha)->locale('es');
			return ucfirst($date->dayName).' '.$date->day.' de '.ucfirst($date->monthName).' del '.
			$date->year.' a las '.$date->format('h:i:s');
		} catch (Exception $e) {
			return null;
		}
	}

	function formatearFechaMes($fecha){
		try {
			$date = Carbon::parse($fecha)->locale('es');
			return $date->day.' de '.ucfirst($date->monthName);
		} catch (Exception $e) {
			return null;
		}
	}

	function mes($fecha) {
		try {
			$date = Carbon::parse($fecha)->locale('es');
			return ucfirst($date->monthName);
		} catch (Exception $e) {
			return null;
		}	
	}

	function formatearHora($fecha){
		try {
			$date = Carbon::parse($fecha);
			return $date->format('h:i:s');
		} catch (Exception $e) {
			
		}
	}

	function formatearFechaD($fecha){
		try {
			$date = Carbon::parse($fecha);
			return $date->format('d-m-Y h:i:s');
		} catch (Exception $e) {
			
		}
	}

	function calcularEgresosNomina($nomina, $usuario){
		try {
			$totalPercepcionesEfectivo = 0;
            $totalPercepcionesDeposito = 0;
            $totalDeduccionesEfectivo = 0;
            $totalDeduccionesDeposito = 0;

            $percepciones = Percepcione::where('idNomina', '=', $nomina)->where('eliminado', '=', 0)->get();

            foreach ($percepciones as $dato) {
                if($dato->idFormaPago === 1){
                    $totalPercepcionesEfectivo = $totalPercepcionesEfectivo + floatval($dato->monto);
                }else{
                    $totalPercepcionesDeposito = $totalPercepcionesDeposito + floatval($dato->monto);
                }
            }

            $deducciones = Deduccione::where('idNomina', '=', $nomina)->where('eliminado', '=', 0)->get();
            foreach ($deducciones as $deduccion) {
                if($deduccion->idFormaPago === 1){
                    $totalDeduccionesEfectivo = $totalDeduccionesEfectivo + floatval($deduccion->monto);
                }else{
                    $totalDeduccionesDeposito = $totalDeduccionesDeposito + floatval($deduccion->monto);
                }
            }


            $totalEfectivo = $totalPercepcionesEfectivo - $totalDeduccionesEfectivo;
            if($totalEfectivo > -1){
            	$percepcionEfectivo = Nominaegreso::where('idNomina', '=', $nomina)->where('tipo', '=', 1)->get();
            	if(count($percepcionEfectivo) > 0){
            		$egreso = Egreso::find($percepcionEfectivo[0]->idEgreso);
            		$egreso->monto = $totalEfectivo;
            		$egreso->save();
            	}else{
            		$nomina = Nomina::find($nomina);
            		$folio = proximoFolioNomina($nomina->idNivel, $nomina->idCalendario, $nomina->idSucursal);
            		$egreso = Egreso::create([
	                    'concepto' => 'Pago en Efectivo a Nomina',
	                    'monto' => $totalEfectivo,
	                    'observaciones' => $nomina->observaciones,
	                    'idRubro' => 3,
	                    'idTipo' => 4,
	                    'idSucursal' => $nomina->idSucursal,
	                    'idCalendario' => $nomina->idCalendario,
	                    'idFormaPago' => 1,
	                    'idUsuario' => $usuario,
	                    'referencia' => 3,
	                    'idNivel' => $nomina->idNivel,
	                    'folio' => $folio,
	                    'idCuenta' => 0,
	                    'activo' => 1,
	                    'eliminado' => 0,
	                ]);
	                $primer = Nominaegreso::create([
	                    'idNomina' => $nomina->id,
	                    'idEgreso' => $egreso->id,
	                    'eliminado' => 0,
	                    'activo' => 1,
	                    'tipo' => 1
	                ]);
            	}
            }

            $totalDeposito = $totalPercepcionesDeposito - $totalDeduccionesDeposito;
            if($totalDeposito > -1){
            	$percepcionDeposito = Nominaegreso::where('idNomina', '=', $nomina)->where('tipo', '=', 2)->get();
            	if(count($percepcionDeposito) > 0){
            		$egreso = Egreso::find($percepcionDeposito[0]->idEgreso);
            		$egreso->monto = $totalDeposito;
            		$egreso->idFormaPago = 4;
            		$egreso->save();
            	}else{
            		$nomina = Nomina::find($nomina);
            		$folio = proximoFolioNomina($nomina->idNivel, $nomina->idCalendario, $nomina->idSucursal);
            		$egreso = Egreso::create([
	                    'concepto' => 'Pago en Deposito a Nomina',
	                    'monto' => $totalDeposito,
	                    'observaciones' => $nomina->observaciones,
	                    'idRubro' => 3,
	                    'idTipo' => 4,
	                    'idSucursal' => $nomina->idSucursal,
	                    'idCalendario' => $nomina->idCalendario,
	                    'idFormaPago' => 4,
	                    'idUsuario' => $usuario,
	                    'referencia' => 3,
	                    'idNivel' => $nomina->idNivel,
	                    'folio' => $folio,
	                    'idCuenta' => 0,
	                    'activo' => 1,
	                    'eliminado' => 0,
	                ]);
	                $primer = Nominaegreso::create([
	                    'idNomina' => $nomina->id,
	                    'idEgreso' => $egreso->id,
	                    'eliminado' => 0,
	                    'activo' => 1,
	                    'tipo' => 2
	                ]);
            	}
            }

            $nomina = Nomina::find($nomina);
            $nomina->total = ($totalEfectivo + $totalDeposito);
            $nomina->save();
            return true;
		} catch (Exception $e) {
			return $e;
		}
	}

	function calcularTotalNomina($nomina){
		try {
			$totalPercepcionesEfectivo = 0;
            $totalPercepcionesDeposito = 0;
            $totalDeduccionesEfectivo = 0;
            $totalDeduccionesDeposito = 0;

            $percepciones = Percepcione::where('idNomina', '=', $nomina)->where('eliminado', '=', 0)->get();

            foreach ($percepciones as $dato) {
                if($dato->idFormaPago === 1){
                    $totalPercepcionesEfectivo = $totalPercepcionesEfectivo + floatval($dato->monto);
                }else{
                    $totalPercepcionesDeposito = $totalPercepcionesDeposito + floatval($dato->monto);
                }
            }

            $deducciones = Deduccione::where('idNomina', '=', $nomina)->where('eliminado', '=', 0)->get();
            foreach ($deducciones as $deduccion) {
                if($deduccion->idFormaPago === 1){
                    $totalDeduccionesEfectivo = $totalDeduccionesEfectivo + floatval($deduccion->monto);
                }else{
                    $totalDeduccionesDeposito = $totalDeduccionesDeposito + floatval($deduccion->monto);
                }
            }

            $totalEfectivo = $totalPercepcionesEfectivo - $totalDeduccionesEfectivo;
            $totalDeposito = $totalPercepcionesDeposito - $totalDeduccionesDeposito;

            $nomina = Nomina::find($nomina);
            $nomina->total = ($totalEfectivo + $totalDeposito);
            $nomina->save();
            return true;
		} catch (Exception $e) {
			return $e;
		}
	}

	function eliminarDeposito($nomina, $monto){
		try {
            $totalPercepcionesDeposito = 0;
            $totalDeduccionesDeposito = 0;

            $percepciones = Percepcione::where('idNomina', '=', $nomina)->where('eliminado', '=', 0)->get();

            foreach ($percepciones as $dato) {
                if($dato->idFormaPago !== 1)
                    $totalPercepcionesDeposito = $totalPercepcionesDeposito + floatval($dato->monto);
            }

            $deducciones = Deduccione::where('idNomina', '=', $nomina)->where('eliminado', '=', 0)->get();
            foreach ($deducciones as $deduccion) {
                if($deduccion->idFormaPago !== 1)
                    $totalDeduccionesDeposito = $totalDeduccionesDeposito + floatval($deduccion->monto);
            }

            $totalPercepcionesDeposito = $totalPercepcionesDeposito - floatval($monto);

            return ($totalDeduccionesDeposito > $totalPercepcionesDeposito);
		} catch (Exception $e) {
			return null;
		}
	}

	function eliminarEfectivo($nomina, $monto){
		try {
			$totalPercepcionesEfectivo = 0;
            $totalDeduccionesEfectivo = 0;

            $percepciones = Percepcione::where('idNomina', '=', $nomina)->where('eliminado', '=', 0)->get();

            foreach ($percepciones as $dato) {
                if($dato->idFormaPago !== 1)
                    $totalPercepcionesEfectivo = $totalPercepcionesEfectivo + floatval($dato->monto);
            }

            $deducciones = Deduccione::where('idNomina', '=', $nomina)->where('eliminado', '=', 0)->get();
            foreach ($deducciones as $deduccion) {
                if($deduccion->idFormaPago !== 1)
                    $totalDeduccionesEfectivo = $totalDeduccionesEfectivo + floatval($deduccion->monto);
            }

            $totalDeduccionesEfectivo = $totalDeduccionesEfectivo - floatval($monto);

            return ($totalDeduccionesEfectivo > $totalPercepcionesEfectivo);
		} catch (Exception $e) {
			return null;
		}
	}

	function saldoFicha($ficha){
		try {
			$total = 0;
			$cargos = Alumnocargo::where('idFicha', '=', $ficha)->where('eliminado', '=', 0)->get();
			$abonos = Alumnoabono::where('idFicha', '=', $ficha)->where('eliminado', '=', 0)->get();
			$descuentos = Alumnodescuento::where('idFicha', '=', $ficha)->where('eliminado', '=', 0)->get();
			$devoluciones = Alumnodevolucione::where('idFicha', '=', $ficha)->where('eliminado', '=', 0)->get();
			$extras = Alumnoextra::where('idFicha', '=', $ficha)->where('eliminado', '=', 0)->get();

			foreach ($cargos as $cargo) {
				$total = $total + floatval($cargo->monto);
			}
			foreach ($abonos as $abono) {
				$total = $total - floatval($abono->monto);
			}
			foreach ($descuentos as $descuento) {
				$total = $total - floatval($descuento->monto);
			}
			foreach ($devoluciones as $devolucion) {
				$total = $total + floatval($devolucion->monto);
			}
			foreach ($extras as $extra) {
				$total = $total - floatval($extra->monto);
			}
			return $total;
		} catch (Exception $e) {
			return null;
		}
	}

	function calendarioActual(){
		try {
			$consulta = "SELECT id, nombre, inicio, fin FROM calendarios WHERE NOW() BETWEEN inicio AND fin and eliminado = 0 ORDER BY fin desc";
            $registros = DB::select($consulta, array());
            $calendario = array();
            if(count($registros) > 0){
                $calendario['calendario'] = $registros[0]->id;
                $calendario['ncalendario'] = $registros[0]->nombre;
                $calendario['inicio'] = $registros[0]->inicio;
                $calendario['fin'] = $registros[0]->fin;
            }else{
                $calendario['calendario'] = 0;
                $calendario['ncalendario'] = 'No se ha creado calendario para la fecha actual';
                $calendario['inicio'] = '';
                $calendario['fin'] = '';
            }
            return $calendario;
		} catch (Exception $e) {
			return null;
		}
	}

	function traerPermisosUsuario($id){
		try {
			$modulos = Modulospermiso::join('modulos', 'idModulo', '=', 'modulos.id')->
			select('modulos.*')->
			where('modulospermisos.idTipoUsuario', '=', $id)->
			orderBy('modulos.nombre', 'ASC')->get();
			foreach ($modulos as $modulo) {
				$opciones = Opcionespermiso::join('opciones', 'idOpcion', '=', 'opciones.id')->
				select('opciones.*')->
				where('opcionespermisos.idTipoUsuario', '=', $id)->
				where('opciones.idModulo', '=', $modulo->id)->
				orderBy('opciones.nombre', 'ASC')->get();
				$modulo->opciones = $opciones;
				$final[] = $modulo;
			}
            return $final;
		} catch (Exception $e) {
			return null;
		}
	}

	function traerDatosNomina($id){
		try {
			$consulta = "SELECT n.*, (SELECT IF(SUM(monto) IS NULL, 0, SUM(monto)) FROM deducciones WHERE idNomina = n.id AND idFormaPago = 1 AND eliminado = 0) AS deduccionesEfectivo, (SELECT IF(SUM(monto) IS NULL, 0, SUM(monto)) FROM deducciones WHERE idNomina = n.id AND idFormaPago = 4 AND eliminado = 0) AS deduccionesDeposito, (SELECT IF(SUM(monto) IS NULL, 0, SUM(monto)) FROM percepciones WHERE idNomina = n.id AND idFormaPago = 1 AND eliminado = 0) AS percepcionesEfectivo, (SELECT IF(SUM(monto) IS NULL, 0, SUM(monto)) FROM percepciones WHERE idNomina = n.id AND idFormaPago = 4 AND eliminado = 0) AS percepcionesDeposito FROM nominas n WHERE n.id = $id";
			$respuesta = DB::select($consulta, array());
			return (count($respuesta) > 0) ? $respuesta[0] : null;
		} catch (Exception $e) {
			return null;
		}
	}

	function proximoFolioEgreso($nivel, $calendario, $sucursal){
		try {
			$cantidad = Egreso::where('idNivel', '=', $nivel)->
								where('idCalendario', '=', $calendario)->
								where('idSucursal', '=', $sucursal)->get();
            $sucursal = Sucursale::find($sucursal);
            $calendario = Calendario::find($calendario);
            $nivel = Nivele::find($nivel);
            $separados = explode("-", $calendario->nombre);

            $folio = substr($separados[0], -2).$separados[1].substr($nivel->nombre, 0, 1).$sucursal->abreviatura.'-'.(count($cantidad) + 1);
            return $folio;
		} catch (Exception $e) {
			return null;
		}
	}

	function numeroVales($calendario, $sucursal){
		try {
			$vales = Vale::where('idCalendario', '=', $calendario)->where('idSucursalSalida', '=', $sucursal)->get();
			return count($vales);
		} catch (Exception $e) {
			return null;
		}
	}

	function numeroValesGerenciales($calendario, $sucursal){
		try {
			$vales = Valesgerenciale::where('idCalendario', '=', $calendario)->where('idSucursal', '=', $sucursal)->get();
			return count($vales);
		} catch (Exception $e) {
			return null;
		}
	}

	function proximoFolioIngreso($idNivel, $idCalendario, $idSucursal){
		try {
			$cantidad = Ingreso::where('idNivel', '=', $idNivel)->
            where('idCalendario', '=', $idCalendario)->
            where('idSucursal', '=', $idSucursal)->get();
            $sucursal = Sucursale::find($idSucursal);
            $calendario = Calendario::find($idCalendario);
            $nivel = Nivele::find($idNivel);
            $separados = explode("-", $calendario->nombre);
            $folio = substr($separados[0], -2).$separados[1].substr($nivel->nombre, 0, 1).$sucursal->abreviatura.'-'.(count($cantidad) + 1);
            return $folio;
		} catch (Exception $e) {
			return null;
		}
	}

	function proximoFolioFicha($calendario, $nivel, $sucursal){
		try {
			$fichas = Ficha::where('idCalendario', '=', $calendario)->
			where('idSucursalInscripcion', '=', $sucursal)->
			where('idNivel', '=', $nivel)->get();
			
			$nivel = Nivele::find($nivel);
			$sucursal = Sucursale::find($sucursal);
			$calendario = Calendario::find($calendario);
			$separacion = explode("-", $calendario->nombre);

            $folioFicha = substr($separacion[0], -2).$separacion[1].substr($nivel->nombre, 0, 1)."-".$sucursal->abreviatura.(count($fichas)+1);
            return $folioFicha;
		} catch (Exception $e) {
			return null;
		}
	}

	function proximoFolioNomina($nivel, $calendario, $sucursal) {
		try {
			$cantidad = Egreso::where('idNivel', '=', $nivel)->
								where('idCalendario', '=', $calendario)->
								where('idSucursal', '=', $sucursal)->get();
            $sucursal = Sucursale::find($sucursal);
            $calendario = Calendario::find($calendario);
            $nivel = Nivele::find($nivel);
            $separados = explode("-", $calendario->nombre);

            $folio = substr($separados[0], -2).$separados[1].substr($nivel->nombre, 0, 1).$sucursal->abreviatura.'-'.(count($cantidad) + 1);
            return $folio;
		} catch (Exception $e) {
			return null;
		}
	}

	function mesEntero($mes){
		switch(intval($mes)){
			case 1:
				return 'Enero';
			break;
			case 2:
				return 'Febrero';
			break;
			case 3:
				return 'Marzo';
			break;
			case 4:
				return 'Abril';
			break;
			case 5:
				return 'Mayo';
			break;
			case 6:
				return 'Junio';
			break;
			case 7:
				return 'Julio';
			break;
			case 8:
				return 'Agosto';
			break;
			case 9:
				return 'Septiembre';
			break;
			case 10:
				return 'Octubre';
			break;
			case 11:
				return 'Noviembre';
			break;
			case 12:
				return 'Diciembre';
			break;
			default:
				return 'No existe el mes '.$mes;
			break;
		}
	}

	function metaGeneralArray($metas){
		try {
			$meta = 0;
			$inscritos = 0;
			$metaGeneral = array();
			foreach ($metas as $goal) {
				$meta += intval($goal['meta']);
				$inscritos += intval($goal['inscritos']);
				$metaGeneral['calendario'] = (isset($goal['calendario'])) ? $goal['calendario'] : '';
				$metaGeneral['mes'] = (isset($goal['mes'])) ? $goal['mes'] : '';
				$metaGeneral['nivel'] = (isset($goal['nivel'])) ? $goal['nivel'] : '';
				$metaGeneral['subnivel'] = (isset($goal['subnivel'])) ? $goal['subnivel'] : '';
				$metaGeneral['categoria'] = (isset($goal['categoria'])) ? $goal['categoria'] : '';
			}
			$metaGeneral['meta'] = $meta;
			$metaGeneral['inscritos'] = $inscritos;
			return $metaGeneral;
		} catch (Exception $e) {
			return response()->json();
		}
	}

	function calendarioActualDia($dia){
		try {
			$consulta = "SELECT id, nombre FROM calendarios WHERE '$dia' BETWEEN inicio AND fin and eliminado = 0 ORDER BY fin desc";
            $registros = DB::select($consulta, array());
            $calendario = array();
            if(count($registros) > 0){
                $calendario['id'] = $registros[0]->id;
                $calendario['nombre'] = $registros[0]->nombre;
            }else{
                $calendario['id'] = 0;
                $calendario['nombre'] = 'No se ha creado calendario para la fecha actual';
            }
            return $calendario;
		} catch (Exception $e) {
			return null;
		}
	}

	function generarCodigo($longitud) {
		$codigo = "";
		for ($i=0; $i < $longitud; $i++) { 
			$numero = rand(0, 9);
			$codigo.= $numero;	
		}
	    
	    return $codigo;
	}  
?>