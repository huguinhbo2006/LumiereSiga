<?php  

  namespace App\Clases;
  use App\Sucursale;
  use App\Formaspago;
  use App\Ingreso;
  use App\Egreso;
  use App\Vale;
  use App\Valeadministrativo;
  use App\Ficha;
  use App\Clases\Ingresos;
  use App\Clases\Egresos;
  use Illuminate\Support\Facades\DB;
  use Carbon\Carbon;

  class Balances{
    
    function listas(){
       try {
         $listas['sucursales'] = Sucursale::where('eliminado', '=', 0)->get();
         return $listas;
       } catch (Exception $e) {
         return null;
       }
    }

    function ingresos($sucursal){
      try {
        $formas = Formaspago::select('id', 'nombre as forma')->get();
        foreach ($formas as $forma) {
          $forma->cantidad = number_format(Ingreso::where('idFormaPago', '=', $forma->id)->where('idSucursal', '=', $sucursal)->sum('monto'), 2, '.', ',');
        }
        return $formas;
      } catch (Exception $e) {
        return null;
      }
    }

    function egresos($sucursal){
      try {
        $formas = Formaspago::select('id', 'nombre as forma')->get();
        foreach ($formas as $forma) {
          $forma->cantidad = number_format(Egreso::where('idFormaPago', '=', $forma->id)->where('idSucursal', '=', $sucursal)->sum('monto'), 2, '.', ',');
        }
        return $formas;
      } catch (Exception $e) {
        return null;
      }
    }

    function total($sucursal){
      try {
        $ingresos = new Ingresos();
        $egresos = new Egresos();
        return floatval($ingresos->totalEfectivo($sucursal)) - floatval($egresos->totalEfectivo($sucursal));
      } catch (Exception $e) {
        return null;
      }
    }

    function vales($sucursal){
      try {
        return Vale::where('eliminado', '=', 0)->where('idSucursalSalida', '=', $sucursal)->where('aceptado', '=', 0)->sum('monto');
      } catch (Exception $e) {
        return null;
      }
    }

    function administrativo($sucursal){
      try {
        return Valeadministrativo::where('idSucursal', '=', $sucursal)->sum('monto');
      } catch (Exception $e) {
        return null;
      }
    }

    function existeValeAdministrativo(){
      try {
        $existe = Valeadministrativo::where('idSucursal', '=', $sucursal)->get();
        return ($existe > 0);
      } catch (Exception $e) {
        return null;
      }
    }
    
    function ingresosFormaPago($sucursalID){
      try {
        $formas = Formaspago::select(
          'formaspagos.nombre',
          DB::raw("(SELECT SUM(monto) FROM ingresos WHERE idFormaPago = formaspagos.id AND DATE_FORMAT(created_at,'%y-%m-%d') = CURDATE() AND idSucursal = $sucursalID LIMIT 1) as monto")
        )->where('eliminado', '=', 0)->get();
        foreach ($formas as $forma) {
          if(is_null($forma)){
            $forma->monto = '$0.00';
          }else{
            $forma->monto = '$'.number_format($forma->monto, 2, '.', ',');
          }
        }
        return $formas;
      } catch (Exception $e) {
        return null;
      }
    }

    function egresosFormaPago($sucursalID){
      try {
        $formas = Formaspago::select(
          'formaspagos.nombre',
          DB::raw("(SELECT SUM(monto) FROM egresos WHERE idFormaPago = formaspagos.id AND DATE_FORMAT(created_at,'%y-%m-%d') = CURDATE() AND idSucursal = $sucursalID LIMIT 1) as monto")
        )->where('eliminado', '=', 0)->get();
        foreach ($formas as $forma) {
          if(is_null($forma)){
            $forma->monto = '$0.00';
          }else{
            $forma->monto = '$'.number_format($forma->monto, 2, '.', ',');
          }
        }
        return $formas;
      } catch (Exception $e) {
        return null;
      }
    }

    function fichas($usuarioID, $sucursalID){
      try {
        $fichas = Ficha::join('alumnos', 'idAlumno', '=', 'alumnos.id')->
        select(
          DB::raw("CONCAT(alumnos.nombre, ' ', alumnos.apellidoPaterno, ' ', alumnos.apellidoMaterno) as alumno"),
          'fichas.folio',
          DB::raw('DATE_FORMAT(fichas.created_at, "%d-%m-%Y %H:%i:%s") as fecha')
        )->
        where('fichas.idUsuario', '=', $usuarioID)->
        where('fichas.idSucursalInscripcion', '=', $sucursalID)->
        whereRaw("DATE_FORMAT(fichas.created_at,'%y-%m-%d') = CURDATE()")->get();
      } catch (Exception $e) {
        return null;
      }
    }

    function valeAdministrativo($sucursalID){
      try {
        $vale = Valeadministrativo::where('idSucursal', '=', $sucursalID)->get();
        return (count($vale) > 0) ? $vale[0]->monto : 0;
      } catch (Exception $e) {
        return null;
      }
    }
  }


?>