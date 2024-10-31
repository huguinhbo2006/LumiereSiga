<?php  

namespace App\Clases;
use Carbon\Carbon;

class Fechas{
  
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

  function mayorigual($fecha1, $fecha2){
    try {
      $fechaUno = Carbon::parse($fecha1);
      $fechaDos = Carbon::parse($fecha2);
      if($fechaUno->gte($fechaDos)){
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
      if($fechaUno < $fechaDos){
        return true;
      }
      return false;
    } catch (Exception $e) {
      return false;
    }
  }

  function menorigual($fecha1, $fecha2){
    try {
      $fechaUno = Carbon::parse($fecha1);
      $fechaDos = Carbon::parse($fecha2);
      if($fechaUno->lte($fechaDos)){
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

  function formatearFechaNombre($fecha){
    try {
      $date = Carbon::parse($fecha)->locale('es');
      return ucfirst($date->dayName).' '.$date->day.' de '.ucfirst($date->monthName).' del '.$date->year;
    } catch (Exception $e) {
      return null;
    }
  }

  function formatearFechaHoraNombre($fecha){
    try {
      $date = Carbon::parse($fecha)->locale('es');
      return ucfirst($date->dayName).' '.$date->day.' de '.ucfirst($date->monthName).' del '.
      $date->year.' a las '.$date->format('h:i:s');
    } catch (Exception $e) {
      return null;
    }
  }

  function dia($fecha){
    try {
      $date = Carbon::parse($fecha)->locale('es');
      return $date->day;
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

  function formatearFechaHora($fecha){
    try {
      $date = Carbon::parse($fecha);
      return $date->format('d-m-Y h:i:s');
    } catch (Exception $e) {
      
    }
  }
  
}?>