<?php 
	use App\Grupo;
	use App\Altacurso;
	use App\Alumnocargo;
	use App\Alumnoabono;
	use App\Alumnodescuento;
	use App\Alumnoextra;
	use App\Alumnodevolucione;
	use App\Horario;
	use App\Modalidad;
	use App\Categoria;
	use App\Turno;
	use App\Nivele;
	use App\Subnivele;
	use App\Curso;
	use App\Calendario;
	use App\Ficha;
	use Carbon\Carbon;

	use Illuminate\Support\Facades\DB;

	function compararFechas($mayor, $menor){
		try {
			$fechaMayor = Carbon::parse($mayor);
			$fechaMenor = Carbon::parse($menor);
			if($fechaMayor->gt($fechaMenor)){
				return true;
			}else{
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}

	function traerInformacionGrupo($grupo){
		try {
			$consulta = "SELECT  g.*, ac.idCalendario, ac.idNivel, ac.idSubnivel, ac.idModalidad, ac.idCategoria, ac.idCurso, ac.inicio, ac.fin, ac.limitePago, ac.precio, c.nombre as calendario, t.nombre as turno, n.nombre as nivel, s.nombre as subnivel, m.nombre as modalidad, cat.nombre as categoria, CONCAT(h.inicio,'-',h.fin) as horario, cr.nombre as curso, cr.icono as icono
            FROM altacursos ac, grupos g, calendarios c, turnos t, niveles n, subniveles s, modalidades m, categorias cat, cursos cr, horarios h
            WHERE ac.idCalendario = c.id AND ac.idCurso = cr.id AND ac.idNivel = n.id AND ac.idSubnivel = s.id AND ac.idModalidad = m.id AND ac.idCategoria = cat.id AND g.idHorario = h.id AND g.idTurno = t.id AND g.idAltaCurso = ac.id AND g.eliminado = 0 AND g.id = $grupo";

            $registros = DB::select($consulta, array());
            return response()->json($registros[0], 200);
		} catch (Exception $e) {
			return null;
		}
	}

	function desactivarEstadoCuenta($ficha){
		try {
			$consultaAbonos = "UPDATE alumnoabonos set activo = 0 WHERE idFicha = $ficha";
            $RCA = DB::update($consultaAbonos, array());

            $consultaCargos = "UPDATE alumnocargos set activo = 0 WHERE idFicha = $ficha";
            $RCC = DB::update($consultaCargos, array());

            $consultaDescuentos = "UPDATE alumnodescuentos set activo = 0 WHERE idFicha = $ficha";
            $RCD = DB::update($consultaDescuentos, array());

            $consultaExtras = "UPDATE alumnoextras set activo = 0 WHERE idFicha = $ficha";
            $RCE = DB::update($consultaExtras, array());

            $consultaDevoluciones = "UPDATE alumnodevoluciones set activo = 0 WHERE idFicha = $ficha";
            $RCV = DB::update($consultaDevoluciones, array());

            return true;
		} catch (Exception $e) {
			return false;
		}
	}

	function activarEstadoCuenta($ficha){
		try {
			$consultaAbonos = "UPDATE alumnoabonos set activo = 1 WHERE idFicha = $ficha";
            $RCA = DB::update($consultaAbonos, array());

            $consultaCargos = "UPDATE alumnocargos set activo = 1 WHERE idFicha = $ficha";
            $RCC = DB::update($consultaCargos, array());

            $consultaDescuentos = "UPDATE alumnodescuentos set activo = 1 WHERE idFicha = $ficha";
            $RCD = DB::update($consultaDescuentos, array());

            $consultaExtras = "UPDATE alumnoextras set activo = 1 WHERE idFicha = $ficha";
            $RCE = DB::update($consultaExtras, array());

            $consultaDevoluciones = "UPDATE alumnodevoluciones set activo = 1 WHERE idFicha = $ficha";
            $RCV = DB::update($consultaDevoluciones, array());

            return true;
		} catch (Exception $e) {
			return false;
		}
	}

	function adeudosAlumno($alumno){
		try{
			$respuesta = array();
			$dato = array();
			$fichas = Ficha::where('idAlumno', '=', $alumno)->where('estatus', '=', 1)->get();
			foreach ($fichas as $ficha) {
				$dato['ficha'] = $ficha->id;
				$dato['totalFicha'] = calcularTotalFicha($ficha->id);

				$grupo = Grupo::find($ficha->idGrupo);
				$alta = Altacurso::find($grupo->idAltaCurso);
				$dato['limitePago'] = $alta->limitePago;
				if($dato['totalFicha'] > 0 && compararFechas(Carbon::now(), $dato['limitePago'])){
					$dato['adeudo'] = true;
				}else{
					$dato['adeudo'] = false;
				}
				$respuesta[] = $dato;
				$dato = array();
			}
			return $respuesta;
		}catch(Exception $e){
			return null;
		}
	}

	function calcularTotalFicha($ficha){
		try{
			$total = 0;
			$cargos = Alumnocargo::where('idFicha', '=', $ficha)->get();
			$abonos = Alumnoabono::where('idFicha', '=', $ficha)->get();
			$descuentos = Alumnodescuento::where('idFicha', '=', $ficha)->get();
			$devoluciones = Alumnodevolucione::where('idFicha', '=', $ficha)->get();
			$extras = Alumnoextra::where('idFicha', '=', $ficha)->get();

			foreach ($cargos as $cargo) {
				$total = floatval($total) + floatval($cargo->monto);
			}
			foreach ($abonos as $abono) {
				$total = floatval($total) - floatval($abono->monto);
			}
			foreach ($descuentos as $descuento) {
				$total = floatval($total) - floatval($descuento->monto);
			}
			foreach ($extras as $extra) {
				$total = floatval($total) - floatval($extra->monto);
			}
			foreach ($devoluciones as $devolucion) {
				$total = floatval($total) + floatval($devolucion->monto);
			}
			return $total;
		}catch(Exception $e){
			return null;
		}
	}

	function alumnosExternos($sucursal, $calendario, $busqueda){
		try {
			$consulta = "SELECT CONCAT(a.nombre, ' ', a.apellidoPaterno, ' ', a.apellidoMaterno) as alumno, f.created_at as fecha, c.nombre as calendario, n.nombre as nivel, s.nombre as subnivel, f.folio, suc.nombre as sucursal, cr.nombre as curso, m.nombre as modalidad, ac.limitePago as limite, 
                (
                    IF(
                        (SELECT SUM(monto) FROM alumnocargos WHERE idFicha = f.id LIMIT 1) IS NULL, 0, (SELECT SUM(monto) FROM alumnocargos WHERE idFicha = f.id AND eliminado = 0 LIMIT 1)
                    ) -
                    IF(
                        (SELECT SUM(monto) FROM alumnoabonos WHERE idFicha = f.id LIMIT 1) IS NULL, 0, (SELECT SUM(monto) FROM alumnoabonos WHERE idFicha = f.id AND eliminado = 0 LIMIT 1)
                    ) -
                    IF(
                        (SELECT SUM(monto) FROM alumnodescuentos WHERE idFicha = f.id LIMIT 1) IS NULL, 0, (SELECT SUM(monto) FROM alumnodescuentos WHERE idFicha = f.id AND eliminado = 0 LIMIT 1)
                    ) +
                    IF(
                        (SELECT SUM(monto) FROM alumnodevoluciones WHERE idFicha = f.id LIMIT 1) IS NULL, 0, (SELECT SUM(monto) FROM alumnodevoluciones WHERE idFicha = f.id AND eliminado = 0 LIMIT 1)
                    ) -
                    IF(
                        (SELECT SUM(monto) FROM alumnoextras WHERE idFicha = f.id LIMIT 1) IS NULL, 0, (SELECT SUM(monto) FROM alumnoextras WHERE idFicha = f.id AND eliminado = 0 LIMIT 1)
                    )
                ) as saldo, a.id as id
            FROM fichas f, alumnos a, calendarios c, niveles n, grupos g, altacursos ac, subniveles s, sucursales suc, cursos cr, modalidades m
            WHERE f.idSucursalInscripcion <> $sucursal AND f.idSucursalImparticion = $sucursal AND f.idAlumno = a.id AND c.id = f.idCalendario AND n.id = f.idNivel AND f.idGrupo = g.id AND g.idAltaCurso = ac.id AND ac.idSubnivel = s.id AND suc.id = f.idSucursalInscripcion AND cr.id = ac.idCurso AND m.id = ac.idModalidad AND f.idCalendario = $calendario AND CONCAT(a.nombre, ' ', a.apellidoPaterno, ' ', a.apellidoMaterno) LIKE '%$busqueda%' LIMIT 50";
            return DB::select($consulta, array());
		} catch (Exception $e) {
			return null;
		}
	}

	function alumnosInternos($sucursal, $calendario, $busqueda){
		try {
			$consulta = "SELECT CONCAT(a.nombre, ' ', a.apellidoPaterno, ' ', a.apellidoMaterno) as alumno, f.created_at as fecha, c.nombre as calendario, n.nombre as nivel, s.nombre as subnivel, f.folio, suc.nombre as sucursal, cr.nombre as curso, m.nombre as modalidad, ac.limitePago as limite, 
                (
                    IF(
                        (SELECT SUM(monto) FROM alumnocargos WHERE idFicha = f.id LIMIT 1) IS NULL, 0, (SELECT SUM(monto) FROM alumnocargos WHERE idFicha = f.id AND eliminado = 0 LIMIT 1)
                    ) -
                    IF(
                        (SELECT SUM(monto) FROM alumnoabonos WHERE idFicha = f.id LIMIT 1) IS NULL, 0, (SELECT SUM(monto) FROM alumnoabonos WHERE idFicha = f.id AND eliminado = 0 LIMIT 1)
                    ) -
                    IF(
                        (SELECT SUM(monto) FROM alumnodescuentos WHERE idFicha = f.id LIMIT 1) IS NULL, 0, (SELECT SUM(monto) FROM alumnodescuentos WHERE idFicha = f.id AND eliminado = 0 LIMIT 1)
                    ) +
                    IF(
                        (SELECT SUM(monto) FROM alumnodevoluciones WHERE idFicha = f.id LIMIT 1) IS NULL, 0, (SELECT SUM(monto) FROM alumnodevoluciones WHERE idFicha = f.id AND eliminado = 0 LIMIT 1)
                    ) -
                    IF(
                        (SELECT SUM(monto) FROM alumnoextras WHERE idFicha = f.id LIMIT 1) IS NULL, 0, (SELECT SUM(monto) FROM alumnoextras WHERE idFicha = f.id AND eliminado = 0 LIMIT 1)
                    )
                ) as saldo, a.id as id
            FROM fichas f, alumnos a, calendarios c, niveles n, grupos g, altacursos ac, subniveles s, sucursales suc, cursos cr, modalidades m
            WHERE f.idSucursalInscripcion = $sucursal AND f.idAlumno = a.id AND c.id = f.idCalendario AND n.id = f.idNivel AND f.idGrupo = g.id AND g.idAltaCurso = ac.id AND ac.idSubnivel = s.id AND suc.id = f.idSucursalInscripcion AND cr.id = ac.idCurso AND m.id = ac.idModalidad AND f.idCalendario = $calendario AND CONCAT(a.nombre, ' ', a.apellidoPaterno, ' ', a.apellidoMaterno) LIKE '%$busqueda%' LIMIT 50";
            return DB::select($consulta, array());
		} catch (Exception $e) {
			return null;
		}
	}

?>