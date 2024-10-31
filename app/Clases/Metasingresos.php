<?php  

  namespace App\Clases;
  use App\Metasingreso;
  use App\Ficha;
  use App\Clases\Fichas;
  use Illuminate\Support\Facades\DB;

  class Metasingresos{
    function metasCalendario($calendario){
      try {
        $metas = Metasingreso::join('calendarios', 'idCalendario', '=', 'calendarios.id')->
        join('sucursales', 'idSucursal', '=', 'sucursales.id')->
        select([
            'metasingresos.idMes',
            'metasingresos.idSucursal',
            'metasingresos.idCalendario',
            'metasingresos.meta',
            DB::raw("CONCAT('Meta ',
                            sucursales.nombre,
                            ' ',
                            ELT(metasingresos.idMes, 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'),
                            ' Calendario ',
                            calendarios.nombre
            ) as texto")
        ])->where('idCalendario', '=', $calendario)->
        whereRaw('MONTH(NOW()) = metasingresos.idMes')->get();
        return $metas;
      } catch (Exception $e) {
        return null;
      }
    }

    function fichas($meta){
      try {
        $fichas = Ficha::where('idCalendario', '=', $meta->idCalendario)->
        where('idSucursalInscripcion', '=', $meta->idSucursal)->
        whereRaw('MONTH(DATE_SUB(fichas.created_at, INTERVAL 6 HOUR)) = '.$meta->idMes)->get();

        return $fichas;
      } catch (Exception $e) {
        return null;
      }
    }

    function ventas($metas){
      try {
        $funcionesFichas = new Fichas();
        $final = array();
        foreach ($metas as $meta) {
          $fichas = $this->fichas($meta);
          $ingreso = 0;
          foreach ($fichas as $ficha) {
            $ingreso = $ingreso + $funcionesFichas->costo($ficha->id);
          }
          $final[] = array(
            'meta' => $meta->meta,
            'ingreso' => $ingreso,
            'texto' => $meta->texto
          );
        }
        return $final;
      } catch (Exception $e) {
        return null;
      }
    }
  }

?>