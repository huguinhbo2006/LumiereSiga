<?php  

  namespace App\Clases;
  use App\Clases\Fechas;
  use App\Altacurso;
  use App\Calendario;
  use App\Nivele;
  use App\Subnivele;
  use App\Curso;
  use App\Categoria;
  use App\Modalidade;
  use App\Sede;

  class Altacursos{

    function validarCurso($curso){
      try {
        $fechas = new Fechas();
        $calendario= Calendario::find($curso['idCalendario']);

        if($this->existe($curso)) {
          return array('error' => true, 'mensaje' => "El curso ya fue dado de alta anteriormente");
        }else if($fechas->mayor($curso['inicio'], $curso['fin'])){
          return array('error' => true, 'mensaje' => "La fecha de inicio debe ser menor a la fecha de fin del curso");
        }else if(!$fechas->mayorigual($curso['inicio'], $calendario->inicio) || !$fechas->menor($curso['inicio'], $calendario->fin)){
          return array('error' => true, 'mensaje' => "La fecha de inicio no se encuentra entre las fechas de inicio y fin del calendario seleccionado");
        }else if(!$fechas->mayorigual($curso['fin'], $calendario->inicio) || !$fechas->menorigual($curso['fin'], $calendario->fin)){
          return array('error' => true, 'mensaje' => "La fecha de fin del curso no se encuentra entre las fechas de inicio y fin del calendario");
        }else{
          return array('error' => false, 'mensaje' => 'Todo correcto');
        }
      } catch (Exception $e) {
        return null;
      }
    }

    function existe($dato){
      try {
        $existe = Altacurso::where('idNivel', '=', $dato['idNivel'])->where('idSubnivel', '=', $dato['idSubnivel'])->where('idCurso', '=', $dato['idCurso'])->where('idModalidad', '=', $dato['idModalidad'])->where('idCategoria', '=', $dato['idCategoria'])->where('idCalendario', '=', $dato['idCalendario'])->where('idSede', '=', $dato['idSede'])->get();
        return (count($existe) > 0);
      } catch (Exception $e) {
        return null;
      }
    }

    function listas(){
      try {
        $listas = array();
        $listas['niveles'] = Nivele::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
        $listas['subniveles'] = Subnivele::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
        $listas['cursos'] = Curso::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
        $listas['modalidades'] = Modalidade::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
        $listas['calendarios'] = Calendario::whereRaw('fin > NOW()')->where('eliminado', '=', 0)->get();
        $listas['categorias'] = Categoria::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
        $listas['sedes'] = Sede::where('eliminado', '=', 0)->where('activo', '=', 1)->get();

        return $listas;
      } catch (Exception $e) {
        return null;
      }
    }

    function complementarCurso($curso){
      try {
        $curso->calendario = Calendario::find($curso->idCalendario)->nombre;
        $curso->niveles = Nivele::find($curso->idNivel)->nombre;
        $curso->subnivel = Subnivele::find($curso->idSubnivel)->nombre;
        $curso->curso = Curso::find($curso->idCurso)->nombre;
        $curso->modalidad = Modalidade::find($curso->idModalidad)->nombre;
        $curso->categoria = Categoria::find($curso->idCategoria)->nombre;
        $curso->sede = Sede::find($curso->idSede)->nombre;

        return $curso;
      } catch (Exception $e) {
        return  null;
      }
    }
    
  }

?>