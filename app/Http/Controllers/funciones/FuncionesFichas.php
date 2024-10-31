<?php 
	use Illuminate\Support\Facades\DB;
	use App\Ficha;
	use App\Ingreso;
	use App\Altacurso;
	use App\Calendario;
	use App\Nivele;
	use App\Subnivele;
	use App\Categoria;
	use App\Modalidade;
	use App\Curso;
	use App\Turno;
	use App\Grupo;
	use App\Horario;
	use App\Sedesucursale;
	use App\Sucursale;
    use App\Sede;
    use App\Alumnodescuento;

	function traerNiveles($calendario){
		try {
			$consulta = "SELECT idNivel FROM altacursos WHERE idCalendario = $calendario AND eliminado = 0 GROUP BY idNivel";
            $niveles = DB::select($consulta, array());
            $respuesta = array();
            foreach ($niveles as $registro) {
                $nivele = Nivele::find($registro->idNivel);
                $respuesta[] = $nivele;
            }
            return $respuesta;
		} catch (Exception $e) {
			return null;
		}
	}

	function traerSubniveles($calendario, $nivel){
		try {
			$consulta = "SELECT idSubnivel 
                         FROM altacursos 
                         WHERE idCalendario = $calendario AND idNivel = $nivel AND eliminado = 0
                         GROUP BY idSubnivel";
            $subniveles = DB::select($consulta, array());
            $respuesta = array();
            foreach ($subniveles as $registro) {
                $subnivele = Subnivele::find($registro->idSubnivel);
                $respuesta[] = $subnivele;
            }
            return $respuesta;
		} catch (Exception $e) {
			return null;
		}
	}

	function traerCategorias($calendario, $nivel, $subnivel){
		try {
			$consulta = "SELECT idCategoria 
                         FROM altacursos 
                         WHERE idCalendario = $calendario AND idNivel = $nivel AND idSubnivel = $subnivel AND eliminado = 0
                         GROUP BY idCategoria";
            $categorias = DB::select($consulta, array());
            $respuesta = array();
            foreach ($categorias as $registro) {
                $categoria = Categoria::find($registro->idCategoria);
                $respuesta[] = $categoria;
            }
            return $respuesta;			
		} catch (Exception $e) {
			return null;
		}
	}

	function traerModalidades($calendario, $nivel, $subnivel, $categoria){
		try {
			$consulta = "SELECT idModalidad 
                         FROM altacursos 
                         WHERE idCalendario = $calendario AND idNivel = $nivel AND idSubnivel = $subnivel AND idCategoria = $categoria AND eliminado = 0
                         GROUP BY idModalidad";
            $modalidades = DB::select($consulta, array());
            $respuesta = array();
            foreach ($modalidades as $registro) {
                $modalidad = Modalidade::find($registro->idModalidad);
                $respuesta[] = $modalidad;
            }
            return $respuesta;
		} catch (Exception $e) {
			return null;
		}
	}

	function traerCursos($calendario, $nivel, $subnivel, $categoria, $modalidad){
		try {
			$consulta = "SELECT idCurso 
                         FROM altacursos 
                         WHERE idCalendario = $calendario AND idNivel = $nivel AND idSubnivel = $subnivel 
                         AND idCategoria = $categoria
                         AND idModalidad = $modalidad AND eliminado = 0
                         GROUP BY idCurso";
            $cursos = DB::select($consulta, array());
            $respuesta = array();
            foreach ($cursos as $registro) {
                $curse = Curso::find($registro->idCurso);
                $respuesta[] = $curse;
            }
            return $respuesta;
		} catch (Exception $e) {
			return null;
		}
	}

    function traerSedes($calendario, $nivel, $subnivel, $categoria, $modalidad, $curso){
        try {
            $consulta = "SELECT idSede 
                         FROM altacursos 
                         WHERE idCalendario = $calendario AND idNivel = $nivel AND idSubnivel = $subnivel AND idCategoria = $categoria 
                         AND idModalidad = $modalidad AND idCurso = $curso AND eliminado = 0
                         GROUP BY idSede";
            $sedes = DB::select($consulta, array());
            $respuesta = array();
            foreach ($sedes as $registro) {
                $sede = Sede::find($registro->idSede);
                $respuesta[] = $sede;
            }
            return $respuesta;
        } catch (Exception $e) {
            return null;
        }
    }

	function traerAltaCurso($calendario, $nivel, $subnivel, $categoria, $modalidad, $curso, $sede){
		try {
			$consulta = "SELECT id 
                         FROM altacursos 
                         WHERE idCalendario = $calendario AND idNivel = $nivel AND idSubnivel = $subnivel AND idCategoria = $categoria
                         AND idModalidad = $modalidad AND idCurso = $curso AND idSede = $sede AND eliminado = 0";
            $altaCurso = DB::select($consulta, array());
            return $altaCurso[0]->id;
		} catch (Exception $e) {
			return null;
		}
	}

	function traerTurnos($altaCurso){
		try {
			$consulta = "SELECT idTurno FROM grupos WHERE idAltaCurso = $altaCurso AND eliminado = 0 GROUP BY idTurno";
            $grupos = DB::select($consulta, array());
            foreach ($grupos as $registro) {
                $turno = Turno::find($registro->idTurno);
                $respuesta[] = $turno;
            }
            return $respuesta;
		} catch (Exception $e) {
			return null;
		}
	}

	function traerHorarios($altaCurso, $turno){
		try {
			$consulta = "SELECT idHorario 
                         FROM grupos 
                         WHERE idAltaCurso = $altaCurso AND idTurno = $turno AND eliminado = 0
                         GROUP BY idHorario";
            $horarios = DB::select($consulta, array());
            foreach ($horarios as $registro) {
                $horario = Horario::find($registro->idHorario);
                $horario['nombre'] = $horario->inicio. '-'. $horario->fin;
                $respuesta[] = $horario;
            }
            return $respuesta;
		} catch (Exception $e) {
			return null;
		}
	}

	function traerSucursales($sede){
        try {
            $respuesta = array();
            $sucursales = Sedesucursale::where('idSede', '=', $sede)->get();
            foreach ($sucursales as $registro) {
                $sucursal = Sucursale::find($registro->idSucursal);
                $respuesta[] = $sucursal;
            }
            return $respuesta;
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function traerGrupo($altaCurso, $turno, $horario){
    	try {
    		$grupo = Grupo::where('idAltaCurso', '=', $altaCurso)->
                            where('idTurno', '=', $turno)->
                            where('idHorario', '=', $horario)->
                            where('eliminado', '=', 0)->get();
            if(count($grupo) > 0){
                return response()->json($grupo[0], 200);
            }
    	} catch (Exception $e) {
    		return null;
    	}
    }

    function costoFinal($ficha){
        try {
            $descuento = Alumnodescuento::where('idFicha', '=', $ficha->id)->sum('monto');
            $grupo = Grupo::find($ficha->idGrupo);
            $alta = Altacurso::find($grupo->idAltaCurso);
            return (floatval($alta->precio) - floatval($descuento));
        } catch (Exception $e) {
            return null;
        }
    }
?>