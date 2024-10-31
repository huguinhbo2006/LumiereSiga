<?php  
	use App\Altacurso;
	use App\Grupo;
	use App\Horario;
	use App\Turno;
	use App\Curso;
	use App\Modalidade;
	use Carbon\Carbon;
	use App\Paridade;
	use App\Cursosparidade;
	use App\Calendario;
	use Illuminate\Support\Facades\DB;

	function altaGrupo($id){
		try {
			$grupo = Grupo::find($id);
			$alta = Altacurso::find($grupo->idAltaCurso);
			return $alta;
		} catch (Exception $e) {
			return null;
		}
	}

	function imagenGrupo($id){
		try {
			$grupo = Grupo::find($id);
			$alta = Altacurso::find($grupo->idAltaCurso);
			$curso = Curso::find($alta->idCurso);
			return $curso->icono;
		} catch (Exception $e) {
			return response()->json('Error en el servidor', 400);
		}
	}

	function fechaInicioGrupo($id){
		try {
			$grupo = Grupo::find($id);
			$alta = Altacurso::find($grupo->idAltaCurso);
			return $alta->inicio;
		} catch (Exception $e) {
			return response()->json('Error en el servidor', 400);
		}
	}

	function horarioGrupo($id) {
		try {
			$grupo = Grupo::find($id);
			$horario = Horario::find($grupo->idHorario);
			return $horario;
		} catch (Exception $e) {
			return null;
		}
	}

	function turnoGrupo($id) {
		try {
			$grupo = Grupo::find($id);
			$turno = Turno::find($grupo->idHorario);
			return $turno;
		} catch (Exception $e) {
			return null;
		}
	}

	function modalidadGrupo($id){
		try {
			$grupo = Grupo::find($id);
			$alta = Altacurso::find($grupo->idAltaCurso);
			$modalidad = Modalidade::find($alta->idModalidad);
			return $modalidad;
		} catch (Exception $e) {
			return null;
		}
	}

	function calendarioGrupo($id){
		try {
			$grupo = Grupo::find($id);
			$alta = Altacurso::find($grupo->idAltaCurso);
			$calendario = Calendario::find($alta->idCalendario);
			return $calendario;
		} catch (Exception $e) {
			return null;
		}
	}

	function traerGruposParidad($id) {
		try {
			$grupo = Grupo::find($id);
			$turno = $grupo->idTurno;
			$horario = $grupo->idHorario;
			$alta = Altacurso::find($grupo->idAltaCurso);
			$calendario = $alta->idCalendario;
			$nivel = $alta->idNivel;
			$subnivel = $alta->idSubnivel;
			$modalidad = $alta->idModalidad;
			$categoria = $alta->idCategoria;
			$sede = $alta->idSede;

			$consulta = "SELECT  g.id, cr.id as curso
            FROM altacursos ac, grupos g, calendarios c, turnos t, niveles n, subniveles s, modalidades m, categorias cat, cursos cr, horarios h, sedes se
            WHERE ac.idCalendario = c.id AND ac.idCurso = cr.id AND ac.idNivel = n.id AND ac.idSubnivel = s.id AND ac.idModalidad = m.id AND ac.idCategoria = cat.id AND ac.idSede = se.id AND g.idHorario = h.id AND g.idTurno = t.id AND g.idAltaCurso = ac.id AND g.eliminado = 0 AND ac.idCalendario = $calendario AND g.idTurno AND g.idHorario = $horario AND ac.idNivel = $nivel AND ac.idSubnivel = $subnivel AND ac.idModalidad = $modalidad AND ac.idCategoria = $categoria AND ac.idSede = $sede";
            $registros = DB::select($consulta, array());
            $respuesta = array();

            $paridades = Cursosparidade::where('idCurso', '=', $alta->idCurso)->get();
            if(count($paridades) > 0){
            	$paridad = $paridades[0]->idParidad;
            	foreach ($registros as $datos){
            		$existe = Cursosparidade::where('idParidad', '=', $paridad)->where('idCurso', '=', $datos->curso)->get();
            		if(count($existe) > 0){
            			$respuesta[] = $datos->id;
            		}
            	}
            }else{
            	$respuesta[] = $id;
            }
            return $respuesta;
		} catch (Exception $e) {
			return $e;
		}
	}
?>