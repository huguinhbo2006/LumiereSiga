<?php  
	use Illuminate\Support\Facades\DB;
	use App\Ficha;

	function datosFichaAlumno($idAlumno, $idFicha){
		try {
			$consulta = "SELECT CONCAT(a.nombre, ' ', a.apellidoPaterno, ' ', a.apellidoMaterno) AS alumno,
						c.nombre AS curso, cl.nombre AS calendario, de.promedio, ca.puntaje
						FROM alumnos a, fichas f, grupos g, altacursos ac, cursos c, calendarios cl, datosescolares de,
						aspiraciones ap, carreras ca
						WHERE a.id = f.idAlumno AND f.idGrupo = g.id AND g.idAltaCurso = ac.id AND ac.idCurso = c.id
						AND cl.id = ac.idCalendario AND de.idAlumno = $idAlumno 
						AND f.id = $idFicha AND a.id = $idAlumno AND ap.idFicha = $idFicha AND ap.idCarrera = ca.id";
			$registro = DB::select($consulta, array());
			return (count($registro) > 0) ? $registro[0] : [];
		} catch (Exception $e) {
			return 'Error en consulta '.$e;
		}
	}

	function alumnoFicha($idFicha, $idExamen){
		try {
			$consulta = "SELECT c.puntaje, d.promedio, e.forma, f.idNivel, f.id as idFicha, e.id as idExamen
						FROM alumnos a, fichas f, datosescolares d, aspiraciones ap, carreras c, examenes e
						WHERE a.id = f.idAlumno AND d.idAlumno = a.id AND ap.idFicha = f.id AND c.id = ap.idCarrera
						AND f.id = $idFicha AND e.id = $idExamen";
			$respuesta = DB::select($consulta, array());
			return $respuesta;
		} catch (Exception $e) {
			return null;
		}
	}

	function traerExamenesFicha($idFicha){
		try {
			$consulta = "SELECT e.*
						FROM examenes e, examenpermisos ep, grupos g, altacursos ac, fichas f
						WHERE ep.idExamen = e.id AND f.idGrupo = g.id AND g.idAltaCurso = ac.id 
						AND ac.idNivel = ep.idNivel AND ep.idSubnivel = ac.idSubnivel 
						AND ep.idCategoria = ac.idCategoria AND f.id = $idFicha AND ac.inicio <= e.inicio
						AND (SELECT COUNT(*) FROM calificaciones WHERE idExamen = e.id AND idFicha = $idFicha) > 0
						ORDER BY e.inicio ASC";
			$registro = DB::select($consulta, array());
			return $registro;
		} catch (Exception $e) {
			return null;
		}
	}

	function traerExamenesGrupo($idGrupo){
		try {
			$consulta = "SELECT e.*
						FROM examenes e, examenpermisos ep, grupos g, altacursos ac
						WHERE ep.idExamen = e.id AND g.idAltaCurso = ac.id 
						AND ac.idNivel = ep.idNivel AND ep.idSubnivel = ac.idSubnivel 
						AND ep.idCategoria = ac.idCategoria AND g.id = $idGrupo
						AND (ac.inicio <= e.inicio OR ac.inicio <= e.fin)
						ORDER BY e.inicio ASC";
			$registro = DB::select($consulta, array());
			return $registro;
		} catch (Exception $e) {
			return null;
		}
	}

	function examenCompletado($idExamen, $idFicha){
		try {
			$consulta = "SELECT ep.porcentaje
						FROM examenporcentajes ep
						WHERE ep.idExamen = $idExamen AND 
						(
							SELECT COUNT(*) 
							FROM calificaciones c, seccionesporcentajes sp 
							WHERE c.idSeccion = sp.idSeccion AND c.idExamen = $idExamen AND c.idFicha = $idFicha
						) = (
							SELECT COUNT(*) 
							FROM seccionesporcentajes sp, examenporcentajes ep 
							WHERE ep.idExamen = $idExamen AND sp.idPorcentaje = ep.id
						)";
			$registros = DB::select($consulta, array());
			return (count($registros) > 0) ? true : false;
		} catch (Exception $e) {
			return false;
		}
	}

	function alumno($idFicha){
		try {
			$consulta = "SELECT c.puntaje, d.promedio, f.idNivel, f.id as idFicha
						FROM alumnos a, fichas f, datosescolares d, aspiraciones ap, carreras c
						WHERE a.id = f.idAlumno AND d.idAlumno = a.id AND ap.idFicha = f.id AND c.id = ap.idCarrera
						AND f.id = $idFicha";
			$respuesta = DB::select($consulta, array());
			return $respuesta;
		} catch (Exception $e) {
			return null;
		}
	}

	function traerSecciones($idPorcentaje, $idFicha, $idExamen){
		try {
			$consulta = "SELECT s.nombre, s.id AS idSeccion, 
							IF((SELECT COUNT(*) FROM calificaciones WHERE idSeccion = s.id AND idFicha = $idFicha) > 0 , 
								(SELECT aciertos FROM calificaciones WHERE idSeccion = s.id AND idFicha = $idFicha LIMIT 1), 
								0) AS aciertos,
							IF((SELECT COUNT(*) FROM calificaciones WHERE idSeccion = s.id AND idFicha = $idFicha) > 0 , 
								(SELECT errores FROM calificaciones WHERE idSeccion = s.id AND idFicha = $idFicha LIMIT 1), 
								0) AS errores,
							IF((SELECT COUNT(*) FROM calificaciones WHERE idSeccion = s.id AND idFicha = $idFicha) > 0 , 
								(SELECT ausentes FROM calificaciones WHERE idSeccion = s.id AND idFicha = $idFicha LIMIT 1), 
								0) AS ausentes,
							IF((SELECT COUNT(*) FROM calificaciones WHERE idSeccion = s.id AND idFicha = $idFicha) > 0 , true, false) AS calificada
							FROM examenporcentajes ep, seccionesporcentajes sp, secciones s
							WHERE ep.idExamen = $idExamen AND sp.idPorcentaje = $idPorcentaje AND sp.idSeccion = s.id 
							AND sp.idPorcentaje = ep.id AND s.eliminado = 0";
			$respuesta = DB::select($consulta, array());
			return $respuesta;
		} catch (Exception $e) {
			return array();
		}
	}

	function traerSeccionesAusentes($idExamen){
		try {
			$consulta = "SELECT s.nombre, s.id AS idSeccion, 
						IF((SELECT COUNT(*) FROM calificaciones WHERE idSeccion = s.id AND idFicha = 157) > 0 , 
							(SELECT aciertos FROM calificaciones WHERE idSeccion = s.id AND idFicha = 157 LIMIT 1), 
							0) AS aciertos,
						IF((SELECT COUNT(*) FROM calificaciones WHERE idSeccion = s.id AND idFicha = 157) > 0 , 
							(SELECT errores FROM calificaciones WHERE idSeccion = s.id AND idFicha = 157 LIMIT 1), 
							0) AS errores,
						IF((SELECT COUNT(*) FROM calificaciones WHERE idSeccion = s.id AND idFicha = 157) > 0 , 
							(SELECT ausentes FROM calificaciones WHERE idSeccion = s.id AND idFicha = 157 LIMIT 1), 
							0) AS ausentes,
						IF((SELECT COUNT(*) FROM calificaciones WHERE idSeccion = s.id) > 0 , true, false) AS calificada
						FROM secciones s
						WHERE s.idExamen = 1 AND s.valido = 0 and s.eliminado = 0";
			$respuesta = DB::select($consulta, array());
			return $respuesta;
		} catch (Exception $e) {
			return array();
		}
	}

	function traerTotalPreguntas($idPorcentaje){
		try {
			$consulta = "SELECT COUNT(*) AS totalPreguntas, e.nombre FROM preguntas p, examenporcentajes e, seccionesporcentajes s WHERE s.idPorcentaje = e.id AND p.idSeccion = s.idSeccion AND e.id = $idPorcentaje GROUP BY e.nombre";
			$respuesta = DB::select($consulta, array());
			return $respuesta;
		} catch (Exception $e) {
			return $e;
		}
	}

	function traerTotalAciertos($secciones){
		try {
			$aciertos = 0;
			foreach ($secciones as $seccion) {
				$aciertos = $aciertos + intval($seccion->aciertos);
			}
			return $aciertos;
		} catch (Exception $e) {
			return null;
		}
	}

	function traerCalificacionExamen($idExamen, $idFicha){
		try {
			$consulta = "SELECT e.forma, ep.porcentaje, ep.id 
							FROM examenporcentajes ep, examenes e
							WHERE ep.idExamen = $idExamen AND ep.idExamen = e.id";
			$porcentajes = DB::select($consulta, array());
			$promedioExamen = 0;
			foreach ($porcentajes as $porcentaje) {
				$idPorcentaje = $porcentaje->id;
				$totalPreguntas = 0;
				$totalAciertos = 0;
				$consulta = "SELECT s.nombre, c.aciertos, 
							(SELECT COUNT(*) FROM preguntas WHERE idSeccion = s.id) AS total
							FROM calificaciones c, secciones s, examenporcentajes ep, seccionesporcentajes sp
							WHERE c.idSeccion = s.id AND c.idFicha = $idFicha AND sp.idSeccion = s.id 
							AND ep.idExamen = $idExamen AND ep.id = sp.idPorcentaje AND ep.id = $idPorcentaje";
				$calificaciones = DB::select($consulta, array());
				foreach ($calificaciones as $calificacion) {
					$totalPreguntas = $totalPreguntas + $calificacion->total;
					$totalAciertos = $totalAciertos + $calificacion->aciertos;
				}
				if(count($calificaciones) > 0){
					if(intval($porcentaje->forma) === 1){
						$promedioExamen = $promedioExamen + floatval( ($totalAciertos/$totalPreguntas) * intval($porcentaje->porcentaje));
					}
				}else{
					$promedioExamen = 0;
				}
			}
			return $promedioExamen;
		} catch (Exception $e) {
			return 0;
		}
	}

	function examenCalificado($idExamen, $idFicha){
		try {
			$consulta = "SELECT COUNT(*) AS calificadas, 
						(SELECT COUNT(*) FROM secciones s, seccionesporcentajes sp WHERE s.id = sp.idSeccion AND s.idExamen = $idExamen) AS total
						FROM secciones s, calificaciones c, seccionesporcentajes sp
						WHERE c.idSeccion = s.id AND sp.idSeccion = s.id
						AND c.idExamen = $idExamen AND c.idFicha = $idFicha";
			$registros = DB::select($consulta, array());
			$respuesta = ($registros[0]->calificadas === $registros[0]->total);
			return $respuesta;
		} catch (Exception $e) {
			return null;
		}
	}

	function traerCalificacionSeccionesPorcentajes($idFicha, $idExamen){
		try {
			$consulta = "SELECT DISTINCT ep.nombre, 
			(
				(( SELECT SUM(aciertos) FROM calificaciones c, seccionesporcentajes ss WHERE c.idSeccion = ss.idSeccion AND c.idFicha = $idFicha AND c.idExamen = $idExamen AND ss.idPorcentaje = ep.id) /
				( SELECT COUNT(*) FROM preguntas p, seccionesporcentajes sq	WHERE p.idSeccion = sq.idSeccion AND sq.idPorcentaje = ep.id)
				* IF(e.forma = 1 , 600, ep.porcentaje))
				+ IF(e.forma = 1 , 200, 0)
			) AS promedio, ep.id as id, 1 as es
			FROM examenporcentajes ep, calificaciones c, seccionesporcentajes sp, examenes e, secciones s
			WHERE ep.idExamen = $idExamen AND sp.idPorcentaje = ep.id AND c.idExamen = $idExamen AND c.idFicha = $idFicha 
			AND c.idSeccion = sp.idSeccion AND ep.idExamen = e.id AND e.id = $idExamen and s.eliminado = 0 AND s.id = sp.idSeccion ";
			$registros = DB::select($consulta, array());
			return $registros;
		} catch (Exception $e) {
			return $e;
		}
	}

	function traerCalificacionSecciones($idFicha, $idExamen){
		try {
			$consulta = "SELECT s.nombre, 
						(
							((SELECT aciertos FROM calificaciones WHERE idSeccion = s.id AND idFicha = $idFicha LIMIT 1) /
							(SELECT COUNT(*) FROM preguntas WHERE idSeccion = s.id)
							* IF(e.forma = 1 , 600, 100))
							+ IF(e.forma = 1 , 200, 0)
						) AS promedio, s.id as id, 0 as es
						FROM secciones s, examenes e
						WHERE s.idExamen = e.id AND e.id = $idExamen AND s.valido = 0 AND s.eliminado = 0";
			$registros = DB::select($consulta, array());
			return $registros;
		} catch (Exception $e) {
			return $e;
		}
	}

	function egresosGenerales($idCalendario, $idNivel){
		try {
			$consulta = "SELECT e.id, e.folio, n.nombre AS nivel, c.nombre AS calendario, s.nombre AS sucursalCaptura, 
						 s.nombre AS sucursalEgreso, MONTHNAME(e.created_at) AS mes, 
						 DATE_FORMAT(e.created_at, '%Y-%m-%d') fecha, DATE_FORMAT(e.created_at,'%H:%i:%s')  hora,
						 re.nombre AS rubro, te.nombre AS tipo, e.concepto, fp.nombre AS forma, 
						 e.monto
						FROM egresos e, calendarios c, niveles n, sucursales s, rubrosegresos re, tiposegresos te, 
						formaspagos fp 
						WHERE e.idCalendario = c.id AND e.idNivel = n.id AND e.idSucursal = s.id AND e.idRubro = re.id
						AND e.idTipo = te.id AND e.idFormaPago = fp.id AND e.idCalendario = $idCalendario AND e.idNivel = $idNivel;";
			$registros = DB::select($consulta, array());
			return $registros;
		} catch (Exception $e) {
			return null;
		}
	}

	function departamentoEgreso($idEgreso){
		try {
			$consulta = "SELECT d.nombre AS departamento 
						FROM nominaegresos ne, nominas n, departamentos d
						WHERE ne.idNomina = n.id AND ne.idEgreso = $idEgreso AND d.id = n.idDepartamento";
			$registros = DB::select($consulta, array());
			return $registros;
		} catch (Exception $e) {
			return null;
		}
	}

	function empleadoEgreso($idEgreso){
		try {
			$consulta = "SELECT e.nombre AS empleado 
						FROM nominaegresos ne, nominas n, empleados e
						WHERE ne.idNomina = n.id AND ne.idEgreso = $idEgreso AND e.id = n.idEmpleado";
			$registros = DB::select($consulta, array());
			return $registros;
		} catch (Exception $e) {
			return null;
		}
	}

	function ingresosGenerales($idCalendario, $idNivel){
		try {
			$consulta = "SELECT i.id, i.folio, n.nombre AS nivel, c.nombre AS calendario, s.nombre AS sucursal, 
						 MONTHNAME(i.created_at) AS mes, 
						 DATE_FORMAT(i.created_at, '%Y-%m-%d') fecha, DATE_FORMAT(i.created_at,'%H:%i:%s')  hora,
						 ri.nombre AS rubro,  i.concepto, fp.nombre AS forma, 
						 IF((i.idCuenta = 0 OR ISNULL(i.idCuenta)), 'N/A', (SELECT nombre FROM cuentas WHERE id = i.idCuenta)) ,
						 i.monto, i.activo
						FROM ingresos i, calendarios c, niveles n, sucursales s, rubros ri, formaspagos fp
						WHERE i.idCalendario = c.id AND i.idNivel = n.id AND i.idSucursal = s.id AND i.idRubro = ri.id
						AND i.idFormaPago = fp.id AND i.idCalendario = $idCalendario AND i.idNivel = $idNivel;";
			$registros = DB::select($consulta, array());
			return $registros;
		} catch (Exception $e) {
			return null;
		}
	}

	function traerPorcentajesFicha($idFicha){
		try {
			$consulta = "SELECT DISTINCT ep.nombre, ep.id
						FROM examenporcentajes ep, calificaciones c, seccionesporcentajes sp
						WHERE sp.idPorcentaje = ep.id AND c.idFicha = $idFicha 
						AND c.id = sp.idSeccion";
			$registros = DB::select($consulta, array());
			return $registros;
		} catch (Exception $e) {
			return null;
		}
	}

	function traerSeccionesFicha($idFicha){
		try {
			$consulta = "SELECT s.nombre, s.id 
						FROM secciones s, calificaciones c
						WHERE s.id = c.idSeccion AND c.idFicha = $idFicha AND s.valido = 0";
			$registros = DB::select($consulta, array());
			return $registros;
		} catch (Exception $e) {
			return null;
		}
	}

	function traerRespuestasSeccion($idFicha, $idExamen, $idSeccion){
		try {
			$consulta = "SELECT r.respuesta, p.respuesta AS correcta, p.pregunta, p.indice,
						IF(r.respuesta = p.respuesta, true, false) AS esCorrecta
						FROM respuestas r, preguntas p
						WHERE r.idPregunta = p.id AND r.idFicha = $idFicha AND r.idExamen = $idExamen AND 
						r.idSeccion = $idSeccion";
			$registros = DB::select($consulta, array());
			return $registros;
		} catch (Exception $e) {
			return null;
		}
	}

	function existeProspecto($celular, $apellidoPaterno, $apellidoMaterno, $nombre){
		try {
			$consulta = "SELECT * FROM prospectos where celular = '$celular' OR (nombre = '$nombre' AND apellidoPaterno = '$apellidoPaterno' AND apellidoMaterno = '$apellidoMaterno')";
            $prospecto = DB::select($consulta, array());
            return $prospecto;
		} catch (Exception $e) {
			return null;
		}
	}

	function existeSeguimiento($prospecto){
		try {
			$consulta = "SELECT * FROM seguimientos WHERE (estatus = 0 OR estatus = 1) AND idProspecto = $prospecto";
			$registro = DB::select($consulta, array());
			return $registro;
		} catch (Exception $e) {
			return null;
		}
	}

	function comisionesUsuario($mes, $year, $usuario, $esColbach){
		try {
			$consulta = "";
			if($esColbach){
				$consulta = "SELECT tp.comision as porcentaje, f.fecha, f.idNivel, c.nombre AS calendario, n.nombre AS nivel, m.nombre AS modalidad, cu.nombre AS curso, 
						s.nombre AS plantelImparticion, ca.nombre AS categoria, CONCAT(al.apellidoPaterno, ' ', al.apellidoMaterno) AS apellidos,
						al.nombre AS nombres, ac.precio AS precio,
						(SELECT SUM(monto) FROM alumnodescuentos WHERE idFicha = f.id) AS descuentos,
						(SELECT SUM(cantidad) FROM alumnodescuentos WHERE idFicha = f.id AND tipo = 1) AS cantidad,
						((ac.precio) - (SELECT SUM(monto) FROM alumnodescuentos WHERE idFicha = f.id)) AS final,
						(SELECT SUM(monto) FROM alumnoextras WHERE idFicha = f.id AND eliminado = 0) as extras,
						(SELECT SUM(monto) FROM alumnoabonos WHERE idFicha = f.id) abonos,
						(((ac.precio) - (SELECT SUM(monto) FROM alumnodescuentos WHERE idFicha = f.id)) - (SELECT SUM(monto) FROM alumnoabonos WHERE idFicha = f.id)) AS saldo
						FROM fichas f, grupos g, altacursos ac, calendarios c, niveles n, modalidades m, cursos cu, sucursales s, categorias ca,
						alumnos al, tipopagos tp
						WHERE f.idGrupo = g.id AND ac.id = g.idAltaCurso AND c.id = ac.idCalendario AND n.id = ac.idNivel 
						AND m.id = ac.idModalidad AND cu.id = ac.idCurso AND f.idSucursalImparticion = s.id AND ca.id = ac.idCategoria
						AND al.id = f.idAlumno AND tp.id = f.idTipoPago
						AND f.idUsuarioInformacion = $usuario AND MONTH(fecha) = $mes AND YEAR(fecha) = $year AND ac.idNivel = 2";
			}else{
				$consulta = "SELECT f.fecha, f.idNivel, c.nombre AS calendario, n.nombre AS nivel, m.nombre AS modalidad, cu.nombre AS curso, 
						s.nombre AS plantelImparticion, ca.nombre AS categoria, CONCAT(al.apellidoPaterno, ' ', al.apellidoMaterno) AS apellidos,
						al.nombre AS nombres, ac.precio AS precio,
						(SELECT SUM(monto) FROM alumnodescuentos WHERE idFicha = f.id AND eliminado = 0) AS descuentos,
						(SELECT SUM(cantidad) FROM alumnodescuentos WHERE idFicha = f.id AND tipo = 1 AND eliminado = 0) AS cantidad,
						((ac.precio) - (SELECT SUM(monto) FROM alumnodescuentos WHERE idFicha = f.id AND eliminado = 0)) AS final,
						(SELECT SUM(monto) FROM alumnoextras WHERE idFicha = f.id AND eliminado = 0) as extras,
						(SELECT SUM(monto) FROM alumnoabonos WHERE idFicha = f.id AND eliminado = 0) abonos,
						(((ac.precio) - (SELECT SUM(monto) FROM alumnodescuentos WHERE idFicha = f.id AND eliminado = 0)) - (SELECT SUM(monto) FROM alumnoabonos WHERE idFicha = f.id AND eliminado = 0)) AS saldo, cc.comision as porcentaje, cc.tipo as tipo
						FROM fichas f, grupos g, altacursos ac, calendarios c, niveles n, modalidades m, cursos cu, sucursales s, categorias ca, alumnos al, comisioncursos cc 
						WHERE f.idGrupo = g.id AND ac.id = g.idAltaCurso AND c.id = ac.idCalendario AND n.id = ac.idNivel 
						AND m.id = ac.idModalidad AND cu.id = ac.idCurso AND f.idSucursalImparticion = s.id AND ca.id = ac.idCategoria
						AND al.id = f.idAlumno
						AND f.idUsuarioInformacion = $usuario AND MONTH(fecha) = $mes AND YEAR(fecha) = $year AND ac.idNivel <> 2 AND cc.idCalendario = ac.idCalendario AND cc.idCurso = ac.idCurso";	
			}
			$registros = DB::select($consulta, array());
			return $registros;
		} catch (Exception $e) {
			return $e;
		}
	}

	function fichasUsuarioMes($mes, $yaer, $usuario){
		try {
			$fichas = Ficha::join('grupos', 'idGrupo', '=', 'grupos.id')->
			join('altacursos', 'grupos.idAltaCurso', '=', 'altacursos.id')->
			join('calendarios', 'altacursos.idCalendario', '=', 'calendarios.id')->
			join('niveles', 'altacursos.idNivel', '=', 'niveles.id')->
			join('cursos', 'altacursos.idCurso', '=', 'cursos.id')->
			join('alumnos', 'idAlumno', '=', 'alumnos.id')->
			join('comisioncursos', function($join){
    			$join->on('comisioncursos.idCurso', '=', 'cursos.id');
    			$join->on('comisioncursos.idCalendario', '=', 'calendarios.id');
    		})->
    		select('fichas.*')->
    		where('fichas.idUsuarioInformacion', '=', $usuario)->get();
    		return $fichas;
		} catch (Exception $e) {
			return $e;
		}
	}

	function inscritosGeneralesEstadisticas($dia, $plantel, $calendario, $subnivel, $aldia){
		try {
			if($aldia){
				$consulta = "SELECT COUNT(*) as inscritos 
				FROM fichas f, grupos g, altacursos a 
				WHERE f.fecha <= '$dia' AND f.idSucursalInscripcion = $plantel AND 
				f.idCalendario = $calendario AND a.idSubnivel = $subnivel AND 
				g.id = f.idGrupo AND g.idAltaCurso = a.id LIMIT 1";
			}else{
				$consulta = "SELECT COUNT(*) as inscritos 
				FROM fichas f, grupos g, altacursos a 
				WHERE f.idSucursalInscripcion = $plantel AND 
				f.idCalendario = $calendario AND a.idSubnivel = $subnivel AND 
				g.id = f.idGrupo AND g.idAltaCurso = a.id LIMIT 1";
			}
			
			
            $resultado = DB::select($consulta, array());
            return $resultado[0]->inscritos;
		} catch (Exception $e) {
			return $e;
		}
	}

	function inscritosMesEstadisticas($mes, $calendario, $subnivel, $aldia, $dia, $plantel){
		try {
			$consultaPlantel = (intval($plantel) === -1) ? '' : 'AND f.idSucursalInscripcion = '.$plantel;
			if($aldia){
				$consulta = "SELECT COUNT(*) as inscritos FROM fichas f, grupos g, altacursos a WHERE MONTH(f.fecha) = $mes AND f.idCalendario = $calendario AND f.idGrupo = g.id AND g.idAltacurso = a.id AND a.idSubnivel = $subnivel AND f.fecha <= '$dia' ".$consultaPlantel." LIMIT 1";
			}else{
				$consulta = "SELECT COUNT(*) as inscritos FROM fichas f, grupos g, altacursos a WHERE MONTH(f.fecha) = $mes AND f.idCalendario = $calendario AND f.idGrupo = g.id AND g.idAltacurso = a.id AND a.idSubnivel = $subnivel ".$consultaPlantel." LIMIT 1";
			}
            $resultado = DB::select($consulta, array());
            return $resultado[0]->inscritos;
		} catch (Exception $e) {
			return $e;
		}
	}

	function inscritosSemanaEstadisticas($semana, $calendario, $subnivel, $aldia, $dia, $plantel){
		try {
			$consultaPlantel = (intval($plantel) === -1) ? '' : 'AND f.idSucursalInscripcion = '.$plantel;
			if($aldia){
				$consulta = "SELECT COUNT(*) as inscritos FROM fichas f, grupos g, altacursos a WHERE f.semana = $semana AND f.idCalendario = $calendario AND f.idGrupo = g.id AND g.idAltacurso = a.id AND a.idSubnivel = $subnivel ".$consultaPlantel."  AND f.fecha <= '$dia' LIMIT 1";	
			}else{
				$consulta = "SELECT COUNT(*) as inscritos FROM fichas f, grupos g, altacursos a WHERE f.semana = $semana AND f.idCalendario = $calendario AND f.idGrupo = g.id AND g.idAltacurso = a.id AND a.idSubnivel = $subnivel ". $consultaPlantel." LIMIT 1";	
			}
            $resultado = DB::select($consulta, array());
            return $resultado[0]->inscritos;
		} catch (Exception $e) {
			return $e;
		}
	}

	function inscritosCursoEstadisticas($curso, $calendario, $subnivel, $aldia, $dia, $plantel){
		try {
			$consultaPlantel = (intval($plantel) === -1) ? '' : 'AND f.idSucursalInscripcion = '.$plantel;
			if($aldia){
				$consulta = "SELECT COUNT(*) as inscritos FROM fichas f, grupos g, altacursos a WHERE f.idGrupo = g.id AND g.idAltaCurso = a.id AND a.idCurso = $curso AND a.idCalendario = $calendario AND a.idSubnivel = $subnivel AND f.fecha <= '$dia' ".$consultaPlantel;	
			}else{
				$consulta = "SELECT COUNT(*) as inscritos FROM fichas f, grupos g, altacursos a WHERE f.idGrupo = g.id AND g.idAltaCurso = a.id AND a.idCurso = $curso AND a.idCalendario = $calendario AND a.idSubnivel = $subnivel ".$consultaPlantel;	
			}
            $resultado = DB::select($consulta, array());
            return $resultado[0]->inscritos;
		} catch (Exception $e) {
			return $e;
		}
	}

	function inscritosSexos($sexo, $calendario, $aldia, $dia, $plantel){
		try {
			$consultaPlantel = (intval($plantel) === -1) ? '' : 'AND f.idSucursalInscripcion = '.$plantel;
			if($aldia){
				$consulta = "SELECT COUNT(*) as inscritos FROM fichas f, alumnos a WHERE f.idCalendario = $calendario AND a.id = f.idAlumno AND a.idSexo = $sexo AND f.fecha <= '$dia' ".$consultaPlantel;	
			}else{
				$consulta = "SELECT COUNT(*) as inscritos FROM fichas f, alumnos a WHERE f.idCalendario = $calendario AND a.id = f.idAlumno AND a.idSexo = $sexo ".$consultaPlantel;
			}
            $resultado = DB::select($consulta, array());
            return $resultado[0]->inscritos;
		} catch (Exception $e) {
			return $e;
		}
	}

	function cursosLunesAViernesSubnivel($calendario, $calendario2, $subnivel){
		try {
			$consulta = "SELECT a.idCurso AS id, c.nombre FROM altacursos a, cursos c WHERE (idCalendario = $calendario OR idCalendario = $calendario2) AND a.idCurso = c.id AND a.idSubnivel = $subnivel AND a.idModalidad = 1 AND a.idCategoria = 1 GROUP BY idCurso, nombre";
			$resultado = DB::select($consulta, array());
			return $resultado;
		} catch (Exception $e) {
			return $e;
		}
	}

	function cursosFinesDeSemanaSubnivel($calendario, $calendario2, $subnivel){
		try {
			$consulta = "SELECT a.idCurso AS id, c.nombre FROM altacursos a, cursos c WHERE (idCalendario = $calendario OR idCalendario = $calendario2) AND a.idCurso = c.id AND a.idSubnivel = $subnivel AND a.idModalidad > 1 AND a.idModalidad < 5 AND a.idCategoria = 1 GROUP BY idCurso, nombre";
			$resultado = DB::select($consulta, array());
			return $resultado;
		} catch (Exception $e) {
			return $e;
		}
	}

	function cursosADistanciaSubnivel($calendario, $calendario2, $subnivel){
		try {
			$consulta = "SELECT a.idCurso AS id, c.nombre FROM altacursos a, cursos c WHERE (idCalendario = $calendario OR idCalendario = $calendario2) AND a.idCurso = c.id AND a.idSubnivel = $subnivel AND a.idModalidad > 1 AND a.idModalidad < 5 AND a.idCategoria = 1 GROUP BY idCurso, nombre";
			$resultado = DB::select($consulta, array());
			return $resultado;
		} catch (Exception $e) {
			return $e;
		}
	}

	function cursosOnlineSubnivel($calendario, $calendario2, $subnivel){
		try {
			$consulta = "SELECT a.idCurso AS id, c.nombre FROM altacursos a, cursos c WHERE (idCalendario = $calendario OR idCalendario = $calendario2) AND a.idCurso = c.id AND a.idSubnivel = $subnivel AND a.idModalidad > 1 AND a.idModalidad = 5 AND a.idCategoria = 3 GROUP BY idCurso, nombre";
			$resultado = DB::select($consulta, array());
			return $resultado;
		} catch (Exception $e) {
			return $e;
		}
	}

	function inscritosMediosContacto($calendario, $aldia, $dia, $plantel){
		try {
			$consultaPlantel = (intval($plantel) === -1) ? '' : 'AND f.idSucursalInscripcion = '.$plantel;
			if($aldia){
				$consulta = "SELECT m.nombre, (SELECT COUNT(*) FROM publicitarios p, fichas f WHERE idMedioContacto = m.id  AND f.idCalendario = '$calendario' AND f.id = p.idFicha AND f.fecha <= '$dia' $consultaPlantel) AS cantidad FROM publicitarios p, medioscontactos m WHERE p.idMedioContacto = m.id GROUP BY nombre, m.id ORDER BY m.id desc";
			}else{
				$consulta = "SELECT m.nombre, (SELECT COUNT(*) FROM publicitarios p, fichas f WHERE idMedioContacto = m.id  AND f.idCalendario = '$calendario' AND f.id = p.idFicha  $consultaPlantel) AS cantidad FROM publicitarios p, medioscontactos m WHERE p.idMedioContacto = m.id GROUP BY nombre, m.id ORDER BY m.id desc";
			}
			
			$resultado = DB::select($consulta, array());
			return $resultado;
		} catch (Exception $e) {
			return $e;
		}
	}

	function inscritosMotivosInscripcion($calendario, $aldia, $dia, $plantel){
		try {
			$consultaPlantel = (intval($plantel) === -1) ? '' : 'AND f.idSucursalInscripcion = '.$plantel;
			if($aldia){
				$consulta = "SELECT m.nombre, (SELECT COUNT(*) FROM publicitarios p, fichas f WHERE idMotivoInscripcion = m.id  AND f.idCalendario = '$calendario' AND f.id = p.idFicha AND f.fecha <= '$dia' $consultaPlantel) AS cantidad FROM publicitarios p, motivosinscripciones m WHERE p.idMotivoInscripcion = m.id GROUP BY nombre, m.id ORDER BY m.id desc";
			}else{
				$consulta = "SELECT m.nombre, (SELECT COUNT(*) FROM publicitarios p, fichas f WHERE idMotivoInscripcion = m.id  AND f.idCalendario = '$calendario' AND f.id = p.idFicha $consultaPlantel) AS cantidad FROM publicitarios p, motivosinscripciones m WHERE p.idMotivoInscripcion = m.id GROUP BY nombre, m.id ORDER BY m.id desc";
			}
			
			$resultado = DB::select($consulta, array());
			return $resultado;
		} catch (Exception $e) {
			return $e;
		}
	}

	function inscritosPublicitarios($calendario, $aldia, $dia, $plantel){
		try {
			$consultaPlantel = (intval($plantel) === -1) ? '' : 'AND f.idSucursalInscripcion = '.$plantel;
			if($aldia){
				$consulta = "SELECT m.nombre, (SELECT COUNT(*) FROM publicitarios p, fichas f WHERE idMedioPublicitario = m.id  AND f.idCalendario = '$calendario' AND f.id = p.idFicha AND f.fecha <= '$dia' $consultaPlantel) AS cantidad FROM publicitarios p, mediospublicitarios m WHERE p.idMedioPublicitario = m.id GROUP BY nombre, m.id ORDER BY m.id desc";
			}else{
				$consulta = "SELECT m.nombre, (SELECT COUNT(*) FROM publicitarios p, fichas f WHERE idMedioPublicitario = m.id  AND f.idCalendario = '$calendario' AND f.id = p.idFicha $consultaPlantel) AS cantidad FROM publicitarios p, mediospublicitarios m WHERE p.idMedioPublicitario = m.id GROUP BY nombre, m.id ORDER BY m.id desc";
			}
			
			$resultado = DB::select($consulta, array());
			return $resultado;
		} catch (Exception $e) {
			return $e;
		}
	}

	function inscritosVias($calendario, $aldia, $dia, $plantel){
		try {
			$consultaPlantel = (intval($plantel) === -1) ? '' : 'AND f.idSucursalInscripcion = '.$plantel;
			if($aldia){
				$consulta = "SELECT m.nombre, (SELECT COUNT(*) FROM publicitarios p, fichas f WHERE idViaPublicitaria = m.id  AND f.idCalendario = '$calendario' AND f.id = p.idFicha AND f.fecha <= '$dia' $consultaPlantel) AS cantidad FROM publicitarios p, viaspublicitarias m WHERE p.idViaPublicitaria = m.id GROUP BY nombre, m.id ORDER BY m.id desc";
			}else{
				$consulta = "SELECT m.nombre, (SELECT COUNT(*) FROM publicitarios p, fichas f WHERE idViaPublicitaria = m.id  AND f.idCalendario = '$calendario' AND f.id = p.idFicha $consultaPlantel) AS cantidad FROM publicitarios p, viaspublicitarias m WHERE p.idViaPublicitaria = m.id GROUP BY nombre, m.id ORDER BY m.id desc";
			}
			
			$resultado = DB::select($consulta, array());
			return $resultado;
		} catch (Exception $e) {
			return $e;
		}
	}

	function escuelasPrincipalesCalendario($calendario){
		try {
			$consulta = "SELECT e.id, e.nombre, COUNT(f.id) AS inscritos FROM fichas f,datosescolares d, escuelas e WHERE f.idAlumno = d.idAlumno AND d.idEscuela = e.id AND f.idCalendario = $calendario GROUP BY id, nombre ORDER BY inscritos DESC LIMIT 15";
			$resultado = DB::select($consulta, array());
			return $resultado;
		} catch (Exception $e) {
			return $e;
		}
	}

	function inscritosEscuela($escuela, $calendario, $aldia, $dia, $plantel){
		try {
			$consultaPlantel = (intval($plantel) === -1) ? '' : 'AND f.idSucursalInscripcion = '.$plantel;
			if($aldia){
				$consulta = "SELECT COUNT(f.id) AS inscritos FROM fichas f,datosescolares d, escuelas e WHERE f.idAlumno = d.idAlumno AND d.idEscuela = e.id AND f.idCalendario = $calendario AND f.fecha <= '$dia' AND e.id = $escuela $consultaPlantel LIMIT 1";
			}else{
				$consulta = "SELECT COUNT(f.id) AS inscritos FROM fichas f,datosescolares d, escuelas e WHERE f.idAlumno = d.idAlumno AND d.idEscuela = e.id AND f.idCalendario = $calendario AND e.id = $escuela $consultaPlantel LIMIT 1";
			}
            $resultado = DB::select($consulta, array());
            return $resultado[0]->inscritos;
		} catch (Exception $e) {
			return $e;
		}
	}

	function carrerasPrincipalesCalendario($calendario){
		try {
			$consulta = "SELECT c.id, c.nombre, COUNT(f.id) AS inscritos FROM fichas f, aspiraciones a, carreras c WHERE f.id = a.idFicha AND a.idCarrera = c.id AND f.idCalendario = $calendario GROUP BY id, nombre ORDER BY inscritos DESC LIMIT 15";
			$resultado = DB::select($consulta, array());
			return $resultado;
		} catch (Exception $e) {
			return null;
		}
	}

	function inscritosCarrera($carrera, $calendario, $aldia, $dia, $plantel){
		try {
			$consultaPlantel = (intval($plantel) === -1) ? '' : 'AND f.idSucursalInscripcion = '.$plantel;
			if($aldia){
				$consulta = "SELECT COUNT(f.id) AS inscritos FROM fichas f,aspiraciones a, carreras c WHERE f.id = a.idFicha AND a.idCarrera = c.id AND f.idCalendario = $calendario AND f.fecha <= '$dia' AND c.nombre = '$carrera' $consultaPlantel LIMIT 1";
			}else{
				$consulta = "SELECT COUNT(f.id) AS inscritos FROM fichas f,aspiraciones a, carreras c WHERE f.id = a.idFicha AND a.idCarrera = c.id AND f.idCalendario = $calendario AND c.nombre = '$carrera' $consultaPlantel LIMIT 1";
			}
            $resultado = DB::select($consulta, array());
            return $resultado[0]->inscritos;
		} catch (Exception $e) {
			return $e;
		}
	}

	function centrosPrincipalesCalendario($calendario){
		try {
			$consulta = "SELECT c.id, c.nombre, COUNT(f.id) AS inscritos FROM fichas f, aspiraciones a, centrosuniversitarios c WHERE f.id = a.idFicha AND a.idCentroUniversitario = c.id AND f.idCalendario = $calendario GROUP BY id, nombre ORDER BY inscritos DESC LIMIT 15";
			$resultado = DB::select($consulta, array());
			return $resultado;
		} catch (Exception $e) {
			return null;
		}
	}

	function inscritosCentro($centro, $calendario, $aldia, $dia, $plantel){
		try {
			$consultaPlantel = (intval($plantel) === -1) ? '' : 'AND f.idSucursalInscripcion = '.$plantel;
			if($aldia){
				$consulta = "SELECT COUNT(f.id) AS inscritos FROM fichas f,aspiraciones a, centrosuniversitarios c WHERE f.id = a.idFicha AND a.idCentroUniversitario = c.id AND f.idCalendario = $calendario AND f.fecha <= '$dia' AND c.id = $centro $consultaPlantel LIMIT 1";
			}else{
				$consulta = "SELECT COUNT(f.id) AS inscritos FROM fichas f,aspiraciones a, centrosuniversitarios c WHERE f.id = a.idFicha AND a.idCentroUniversitario = c.id AND f.idCalendario = $calendario AND c.id = $centro $consultaPlantel LIMIT 1";
			}
            $resultado = DB::select($consulta, array());
            return $resultado[0]->inscritos;
		} catch (Exception $e) {
			return $e;
		}
	}

	function ingresosSucursal($sucursal, $calendario, $aldia, $dia){
		try {
			if($aldia){
				$consulta = "SELECT SUM(monto) as ingreso FROM ingresos WHERE activo = 1 AND idSucursal = $sucursal AND idCalendario = $calendario AND fecha <= '$dia' LIMIT 1";
			}else{
				$consulta = "SELECT SUM(monto) as ingreso FROM ingresos WHERE activo = 1 AND idSucursal = $sucursal AND idCalendario = $calendario LIMIT 1";
			}
            $resultado = DB::select($consulta, array());
            return $resultado[0]->ingreso;
		} catch (Exception $e) {
			return $e;
		}
	}

	function ingresosMes($mes, $calendario, $aldia, $dia, $plantel){
		try {
			$consultaPlantel = (intval($plantel) === -1) ? '' : 'AND idSucursal = '.$plantel;
			if($aldia){
				$consulta = "SELECT SUM(monto) as ingreso FROM ingresos WHERE activo = 1 AND MONTH(fecha) = $mes AND idCalendario = $calendario AND fecha <= '$dia' $consultaPlantel LIMIT 1";
			}else{
				$consulta = "SELECT SUM(monto) as ingreso FROM ingresos WHERE activo = 1 AND MONTH(fecha) = $mes AND idCalendario = $calendario $consultaPlantel LIMIT 1";
			}
            $resultado = DB::select($consulta, array());
            return $resultado[0]->ingreso;
		} catch (Exception $e) {
			return $e;
		}
	}

	function ingresosModalidad($modalidad, $calendario, $aldia, $dia, $plantel){
		try {
			$consultaPlantel = (intval($plantel) === -1) ? '' : 'AND i.idSucursal = '.$plantel;
			if($aldia){
				$consulta = "SELECT SUM(i.monto) as ingreso FROM ingresos i, alumnoabonos a, fichas f, grupos g, altacursos c WHERE i.id = a.idIngreso AND a.idFicha = f.id AND g.id = f.idGrupo AND g.idAltaCurso = c.id AND c.idModalidad = $modalidad AND c.idCalendario = $calendario AND i.fecha <= '$dia' $consultaPlantel LIMIT 1";
			}else{
				$consulta = "SELECT SUM(i.monto) as ingreso FROM ingresos i, alumnoabonos a, fichas f, grupos g, altacursos c WHERE i.id = a.idIngreso AND a.idFicha = f.id AND g.id = f.idGrupo AND g.idAltaCurso = c.id AND c.idModalidad = $modalidad AND c.idCalendario = $calendario $consultaPlantel LIMIT 1";
			}
            $resultado = DB::select($consulta, array());
            return $resultado[0]->ingreso;
		} catch (Exception $e) {
			return $e;
		}
	}

	function ingresosCurso($curso, $calendario, $aldia, $dia, $plantel, $modalidad){
		try {
			$consultaPlantel = (intval($plantel) === -1) ? '' : 'AND i.idSucursal = '.$plantel;
			$consultaModalidad = ($modalidad) ? 'AND c.idModalidad = 1' : 'AND c.idModalidad > 1 AND c.idModalidad < 5';
			if($aldia){
				$consulta = "SELECT SUM(i.monto) as ingreso FROM ingresos i, alumnoabonos a, fichas f, grupos g, altacursos c WHERE i.id = a.idIngreso AND a.idFicha = f.id AND g.id = f.idGrupo AND g.idAltaCurso = c.id AND c.idCurso = $curso AND c.idCalendario = $calendario AND i.fecha <= '$dia' $consultaPlantel $consultaModalidad LIMIT 1";
			}else{
				$consulta = "SELECT SUM(i.monto) as ingreso FROM ingresos i, alumnoabonos a, fichas f, grupos g, altacursos c WHERE i.id = a.idIngreso AND a.idFicha = f.id AND g.id = f.idGrupo AND g.idAltaCurso = c.id AND c.idCurso = $curso AND c.idCalendario = $calendario $consultaPlantel $consultaModalidad LIMIT 1";
			}
            $resultado = DB::select($consulta, array());
            return $resultado[0]->ingreso;
		} catch (Exception $e) {
			return $e;
		}
	}

	function saldoActualPlantel($sucursal, $aldia, $dia, $calendario){
		try {
			$consultaAD = ($aldia) ? "AND f.fecha = '$dia'" : '';
			$consulta = "SELECT 
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
            ) as saldo
        	FROM fichas f
       		WHERE f.idSucursalImparticion = $sucursal AND f.idCalendario = $calendario $consultaAD";
       		$fichas = DB::select($consulta, array());
           	$saldo = 0;
           	foreach ($fichas as $ficha) {
           		$saldo = $saldo + floatval($ficha->saldo);
           	}
            return $saldo;
		} catch (Exception $e) {
			return $e;
		}
	}

	function saldoActualPlantelCurso($sucursal, $aldia, $dia, $calendario, $curso){
		try {
			$consultaAD = ($aldia) ? "AND f.fecha = '$dia'" : '';
			$consultaPlantel = (intval($sucursal) === -1) ? "" : "f.idSucursalImparticion = $sucursal";
			
			$consulta = "SELECT 
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
            ) as saldo
        	FROM fichas f, grupos g, altacursos a
       		WHERE f.idGrupo = g.id AND g.idAltaCurso = a.id AND f.idCalendario = $calendario AND a.idCurso = $curso $consultaAD $consultaPlantel";
       		$fichas = DB::select($consulta, array());
           	$saldo = 0;
           	foreach ($fichas as $ficha) {
           		$saldo = $saldo + floatval($ficha->saldo);
           	}
            return $saldo;
		} catch (Exception $e) {
			return $e;
		}
	}
?>