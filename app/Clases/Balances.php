<?php  

  namespace App\Clases;
  use App\Sucursale;
  use App\Formaspago;
  use App\Ingreso;
  use App\Egreso;
  use App\Vale;
  use App\Valeadministrativo;
  use App\Clases\Ingresos;
  use App\Clases\Egresos;

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
  }
?>