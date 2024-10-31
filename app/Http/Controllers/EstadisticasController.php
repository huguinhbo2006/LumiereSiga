<?php

namespace App\Http\Controllers;
use App\Nivele;
use App\Calendario;
use App\Ficha;
use App\Sucursale;
use App\Subnivele;
use App\Categoria;
use App\Modalidade;
use App\Curso;
use App\Usuario;
use App\Medioscontacto;
use App\Sexo;
use Carbon\Carbon;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
include "funciones/funcionesBaseDatos.php";
include "funciones/FuncionesGenerales.php";


class EstadisticasController extends BaseController
{
    function selects(){
        try {
            $sucursal['nombre'] = "Todos";
            $sucursal['id'] = "-1";
            $sucursales = Sucursale::where('eliminado', '=', 0)->whereRaw('LENGTH(mapa) > 0')->get();
            $sucursales[] = $sucursal;
            $respuesta['listas']['sucursales'] = $sucursales;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function estadisticasLicenciatura(Request $request){
        try {
            $date = Carbon::parse($request['dia']);
            $fecha = $date->format('Y-m-d');
            $fecha2 = $date->subYear(1)->format('Y-m-d');
            $calendario = calendarioActualDia($fecha);
            $calendario2 = calendarioActualDia($fecha2);
            $sucursales = Sucursale::where('eliminado', '=', 0)->whereRaw('LENGTH(mapa) > 0')->get();

            //Calendarios
            $calendarioAnteriorDias['label'] = $calendario2['nombre'];
            $calendarioActualDias['label'] = $calendario['nombre'];
            $etiquetas = array();
            foreach ($sucursales as $sucursal) {
                $etiquetas[] = $sucursal->nombre;
                $calendarioAnteriorDias['data'][] = inscritosGeneralesEstadisticas($fecha2, $sucursal->id, $calendario2['id'], $request['idSubnivel'], $request['aldia']);
                $calendarioActualDias['data'][] = inscritosGeneralesEstadisticas($fecha, $sucursal->id, $calendario['id'], $request['idSubnivel'], $request['aldia']);
            }
            $diasCalendarios[] = $calendarioAnteriorDias;
            $diasCalendarios[] = $calendarioActualDias;
            $final['labelDias'] = $etiquetas;
            $final['dias'] = $diasCalendarios;

            //Meses
            $final['labelMeses'] = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
            $calendarioAnteriorMeses['label'] = $calendario2['nombre'];
            $calendarioActualMeses['label'] = $calendario['nombre'];
            for ($i=1; $i < 13; $i++) { 
                $calendarioAnteriorMeses['data'][] = inscritosMesEstadisticas($i, $calendario2['id'], $request['idSubnivel'], $request['aldia'], $fecha2, $request['idPlantelMeses']);
                $calendarioActualMeses['data'][] = inscritosMesEstadisticas($i, $calendario['id'], $request['idSubnivel'], $request['aldia'], $fecha, $request['idPlantelMeses']);
            }
            $mesesCalendarios[] = $calendarioAnteriorMeses;
            $mesesCalendarios[] = $calendarioActualMeses;
            $final['meses'] = $mesesCalendarios;

            //Semanas
            $calendarioAnteriorSemanas['label'] = $calendario2['nombre'];
            $calendarioActualSemanas['label'] = $calendario['nombre'];
            for ($i=1; $i < 53; $i++) { 
                $final['labelSemanas'][] = $i;
                $calendarioAnteriorSemanas['data'][] = inscritosSemanaEstadisticas($i, $calendario2['id'], $request['idSubnivel'], $request['aldia'], $fecha2, $request['idPlantelSemanas']);
                $calendarioActualSemanas['data'][] = inscritosSemanaEstadisticas($i, $calendario['id'], $request['idSubnivel'], $request['aldia'], $fecha, $request['idPlantelSemanas']);
            }
            $semanasCalendarios[] = $calendarioAnteriorSemanas;
            $semanasCalendarios[] = $calendarioActualSemanas;
            $final['semanas'] = $semanasCalendarios;

            //Cursos LV
            $cursosLV = cursosLunesAViernesSubnivel($calendario['id'], $calendario2['id'], $request['idSubnivel']);
            $calendarioAnteriorCursoLV['label'] = $calendario2['nombre'];
            $calendarioActualCursoLV['label'] = $calendario['nombre'];
            foreach ($cursosLV as $curso) {
                $final['labelCursosLV'][] = $curso->nombre;
                $calendarioAnteriorCursoLV['data'][] = inscritosCursoEstadisticas($curso->id, $calendario2['id'], $request['idSubnivel'], $request['aldia'], $fecha2, $request['idPlantelCursosLV']);
                $calendarioActualCursoLV['data'][] = inscritosCursoEstadisticas($curso->id, $calendario['id'], $request['idSubnivel'], $request['aldia'], $fecha, $request['idPlantelCursosLV']);
            }
            
            $cursosCalendariosLV[] = $calendarioAnteriorCursoLV;
            $cursosCalendariosLV[] = $calendarioActualCursoLV;
            $final['cursosLV'] = $cursosCalendariosLV;

            //Cursos FS
            $cursosFS = cursosFinesDeSemanaSubnivel($calendario['id'], $calendario2['id'], $request['idSubnivel']);
            $calendarioAnteriorCursoFS['label'] = $calendario2['nombre'];
            $calendarioActualCursoFS['label'] = $calendario['nombre'];
            foreach ($cursosFS as $curso) {
                $final['labelCursosFS'][] = $curso->nombre;
                $calendarioAnteriorCursoFS['data'][] = inscritosCursoEstadisticas($curso->id, $calendario2['id'], $request['idSubnivel'], $request['aldia'], $fecha2, $request['idPlantelCursosFS']);
                $calendarioActualCursoFS['data'][] = inscritosCursoEstadisticas($curso->id, $calendario['id'], $request['idSubnivel'], $request['aldia'], $fecha, $request['idPlantelCursosFS']);
            }
            
            $cursosCalendariosFS[] = $calendarioAnteriorCursoFS;
            $cursosCalendariosFS[] = $calendarioActualCursoFS;
            $final['cursosFS'] = $cursosCalendariosFS;

            //Cursos AD
            $cursosAD = cursosADistanciaSubnivel($calendario['id'], $calendario2['id'], $request['idSubnivel']);
            $calendarioAnteriorCursoAD['label'] = $calendario2['nombre'];
            $calendarioActualCursoAD['label'] = $calendario['nombre'];
            foreach ($cursosAD as $curso) {
                $final['labelCursosAD'][] = $curso->nombre;
                $calendarioAnteriorCursoAD['data'][] = inscritosCursoEstadisticas($curso->id, $calendario2['id'], $request['idSubnivel'], $request['aldia'], $fecha2, $request['idPlantelCursosAD']);
                $calendarioActualCursoAD['data'][] = inscritosCursoEstadisticas($curso->id, $calendario['id'], $request['idSubnivel'], $request['aldia'], $fecha, $request['idPlantelCursosAD']);
            }
            
            $cursosCalendariosAD[] = $calendarioAnteriorCursoAD;
            $cursosCalendariosAD[] = $calendarioActualCursoAD;
            $final['cursosAD'] = $cursosCalendariosAD;

            //Cursos ON
            $cursosON = cursosOnlineSubnivel($calendario['id'], $calendario2['id'], $request['idSubnivel']);
            $calendarioAnteriorCursoON['label'] = $calendario2['nombre'];
            $calendarioActualCursoON['label'] = $calendario['nombre'];
            foreach ($cursosON as $curso) {
                $final['labelCursosON'][] = $curso->nombre;
                $calendarioAnteriorCursoON['data'][] = inscritosCursoEstadisticas($curso->id, $calendario2['id'], $request['idSubnivel'], $request['aldia'], $fecha2, $request['idPlantelCursosON']);
                $calendarioActualCursoON['data'][] = inscritosCursoEstadisticas($curso->id, $calendario['id'], $request['idSubnivel'], $request['aldia'], $fecha, $request['idPlantelCursosON']);
            }
            
            $cursosCalendariosON[] = $calendarioAnteriorCursoON;
            $cursosCalendariosON[] = $calendarioActualCursoON;
            $final['cursosON'] = $cursosCalendariosON;
            return response()->json($final, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function estadisticasMeses(Request $request){
        try {
            $date = Carbon::parse($request['dia']);
            $fecha = $date->format('Y-m-d');
            $fecha2 = $date->subYear(1)->format('Y-m-d');
            $calendario = calendarioActualDia($fecha);
            $calendario2 = calendarioActualDia($fecha2);

            //Meses
            $final['labelMeses'] = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
            $calendarioAnteriorMeses['label'] = $calendario2['nombre'];
            $calendarioActualMeses['label'] = $calendario['nombre'];
            for ($i=1; $i < 13; $i++) { 
                $calendarioAnteriorMeses['data'][] = inscritosMesEstadisticas($i, $calendario2['id'], $request['idSubnivel'], $request['aldia'], $fecha2, $request['idPlantelMeses']);
                $calendarioActualMeses['data'][] = inscritosMesEstadisticas($i, $calendario['id'], $request['idSubnivel'], $request['aldia'], $fecha, $request['idPlantelMeses']);
            }
            $mesesCalendarios[] = $calendarioAnteriorMeses;
            $mesesCalendarios[] = $calendarioActualMeses;
            $final['meses'] = $mesesCalendarios;
            return response()->json($final, 200);
        } catch (Exception $e) {
            return response()->json('error en el servidor', 400);
        }
    }

    function estadisticasCursosLV(Request $request){
        try {
            $date = Carbon::parse($request['dia']);
            $fecha = $date->format('Y-m-d');
            $fecha2 = $date->subYear(1)->format('Y-m-d');
            $calendario = calendarioActualDia($fecha);
            $calendario2 = calendarioActualDia($fecha2);

            $cursos = cursosLunesAViernesSubnivel($calendario['id'], $calendario2['id'], $request['idSubnivel']);
            $calendarioAnteriorCurso['label'] = $calendario2['nombre'];
            $calendarioActualCurso['label'] = $calendario['nombre'];
            foreach ($cursos as $curso) {
                $final['labelCursosLV'][] = $curso->nombre;
                $calendarioAnteriorCurso['data'][] = inscritosCursoEstadisticas($curso->id, $calendario2['id'], $request['idSubnivel'], $request['aldia'], $fecha2, $request['idPlantelCursosLV']);
                $calendarioActualCurso['data'][] = inscritosCursoEstadisticas($curso->id, $calendario['id'], $request['idSubnivel'], $request['aldia'], $fecha, $request['idPlantelCursosLV']);
            }
            
            $cursosCalendarios[] = $calendarioAnteriorCurso;
            $cursosCalendarios[] = $calendarioActualCurso;
            $final['cursosLV'] = $cursosCalendarios;
            return response()->json($final, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function estadisticasCursosFS(Request $request){
        try {
            $date = Carbon::parse($request['dia']);
            $fecha = $date->format('Y-m-d');
            $fecha2 = $date->subYear(1)->format('Y-m-d');
            $calendario = calendarioActualDia($fecha);
            $calendario2 = calendarioActualDia($fecha2);

            $cursos = cursosFinesDeSemanaSubnivel($calendario['id'], $calendario2['id'], $request['idSubnivel']);
            $calendarioAnteriorCurso['label'] = $calendario2['nombre'];
            $calendarioActualCurso['label'] = $calendario['nombre'];
            foreach ($cursos as $curso) {
                $final['labelCursosFS'][] = $curso->nombre;
                $calendarioAnteriorCurso['data'][] = inscritosCursoEstadisticas($curso->id, $calendario2['id'], $request['idSubnivel'], $request['aldia'], $fecha2, $request['idPlantelCursosFS']);
                $calendarioActualCurso['data'][] = inscritosCursoEstadisticas($curso->id, $calendario['id'], $request['idSubnivel'], $request['aldia'], $fecha, $request['idPlantelCursosFS']);
            }
            
            $cursosCalendarios[] = $calendarioAnteriorCurso;
            $cursosCalendarios[] = $calendarioActualCurso;
            $final['cursosFS'] = $cursosCalendarios;
            return response()->json($final, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function estadisticasSemanas(Request $request){
        try {
            $date = Carbon::parse($request['dia']);
            $fecha = $date->format('Y-m-d');
            $fecha2 = $date->subYear(1)->format('Y-m-d');
            $calendario = calendarioActualDia($fecha);
            $calendario2 = calendarioActualDia($fecha2);

            $calendarioAnteriorSemanas['label'] = $calendario2['nombre'];
            $calendarioActualSemanas['label'] = $calendario['nombre'];
            for ($i=1; $i < 53; $i++) { 
                $final['labelSemanas'][] = $i;
                $calendarioAnteriorSemanas['data'][] = inscritosSemanaEstadisticas($i, $calendario2['id'], $request['idSubnivel'], $request['aldia'], $fecha2, $request['idPlantelSemanas']);
                $calendarioActualSemanas['data'][] = inscritosSemanaEstadisticas($i, $calendario['id'], $request['idSubnivel'], $request['aldia'], $fecha, $request['idPlantelSemanas']);
            }
            $semanasCalendarios[] = $calendarioAnteriorSemanas;
            $semanasCalendarios[] = $calendarioActualSemanas;
            $final['semanas'] = $semanasCalendarios;
            return response()->json($final, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function estadisticasMarketing(Request $request){
        try {
            $date = Carbon::parse($request['dia']);
            $fecha = $date->format('Y-m-d');
            $fecha2 = $date->subYear(1)->format('Y-m-d');
            $calendario = calendarioActualDia($fecha);
            $calendario2 = calendarioActualDia($fecha2);

            $medios1 = InscritosMediosContacto($calendario['id'], $request['aldia'], $fecha, $request['idPlantelMedios']);
            $medios2 = InscritosMediosContacto($calendario2['id'], $request['aldia'], $fecha2, $request['idPlantelMedios']);
            $mediosOne['label'] = $calendario['nombre'];
            foreach ($medios1 as $medio) {
                $final['labelMedios'][] = $medio->nombre;
                $mediosOne['data'][] = $medio->cantidad;
            }
            $mediosTwo['label'] = $calendario2['nombre'];
            foreach ($medios2 as $medio) {
                $mediosTwo['data'][] = $medio->cantidad;
            }
            $final['medios'][] = $mediosOne;
            $final['medios'][] = $mediosTwo;

            //Motivos de inscripcion
            $motivos1 = inscritosMotivosInscripcion($calendario['id'], $request['aldia'], $fecha, $request['idPlantelMotivo']);
            $motivos2 = inscritosMotivosInscripcion($calendario2['id'], $request['aldia'], $fecha2, $request['idPlantelMotivo']);
            $motivosOne['label'] = $calendario['nombre'];
            foreach ($motivos1 as $motivo) {
                $final['labelMotivos'][] = $motivo->nombre;
                $motivosOne['data'][] = $motivo->cantidad;
            }
            $motivosTwo['label'] = $calendario2['nombre'];
            foreach ($motivos2 as $motivo) {
                $motivosTwo['data'][] = $motivo->cantidad;
            }
            $final['motivos'][] = $motivosOne;
            $final['motivos'][] = $motivosTwo;

            //Medios Publicitarios
            $publicitarios1 = inscritosPublicitarios($calendario['id'], $request['aldia'], $fecha, $request['idPlantelPublicitarios']);
            $publicitarios2 = inscritosPublicitarios($calendario2['id'], $request['aldia'], $fecha2, $request['idPlantelPublicitarios']);
            $publicitariosOne['label'] = $calendario['nombre'];
            foreach ($publicitarios1 as $publicitario) {
                $final['labelPublicitarios'][] = $publicitario->nombre;
                $publicitariosOne['data'][] = $publicitario->cantidad;
            }
            $publicitariosTwo['label'] = $calendario2['nombre'];
            foreach ($publicitarios2 as $publicitario) {
                $publicitariosTwo['data'][] = $publicitario->cantidad;
            }
            $final['publicitarios'][] = $publicitariosOne;
            $final['publicitarios'][] = $publicitariosTwo;

            //Vias Publicitarios
            $vias1 = inscritosVias($calendario['id'], $request['aldia'], $fecha, $request['idPlantelVias']);
            $vias2 = inscritosVias($calendario2['id'], $request['aldia'], $fecha2, $request['idPlantelVias']);
            $viasOne['label'] = $calendario['nombre'];
            foreach ($vias1 as $via) {
                $final['labelVias'][] = $via->nombre;
                $viasOne['data'][] = $via->cantidad;
            }
            $viasTwo['label'] = $calendario2['nombre'];
            foreach ($vias2 as $via) {
                $viasTwo['data'][] = $via->cantidad;
            }
            $final['vias'][] = $viasOne;
            $final['vias'][] = $viasTwo;

            //Sexos
            $sexos = Sexo::where('eliminado', '=', 0)->get();
            $calendarioActualSexo['label'] = $calendario['nombre'];
            $calendarioAnteriorSexo['label'] = $calendario2['nombre'];
            foreach ($sexos as $sexo) {
                $final['labelSexos'][] = $sexo->nombre;
                $calendarioActualSexo['data'][] = inscritosSexos($sexo->id, $calendario['id'], $request['aldia'], $fecha, $request['idPlantelSexo']);
                $calendarioAnteriorSexo['data'][] = inscritosSexos($sexo->id, $calendario2['id'], $request['aldia'], $fecha2, $request['idPlantelSexo']);

            }
            $final['sexos'][] = $calendarioAnteriorSexo;
            $final['sexos'][] = $calendarioActualSexo;

            $escuelas = escuelasPrincipalesCalendario($calendario['id']);
            $calendarioAnteriorEscuelas['label'] = $calendario2['nombre'];
            $calendarioActualEscuelas['label'] = $calendario['nombre'];
            foreach($escuelas as $escuela) { 
                $final['labelEscuelas'][] = $escuela->nombre;
                $calendarioAnteriorEscuelas['data'][] = inscritosEscuela($escuela->id, $calendario2['id'], $request['aldia'], $fecha2, $request['idPlantelEscuela']);
                $calendarioActualEscuelas['data'][] = inscritosEscuela($escuela->id, $calendario['id'], $request['aldia'], $fecha, $request['idPlantelEscuela']);
            }
            $final['escuelas'][] = $calendarioAnteriorEscuelas;
            $final['escuelas'][] = $calendarioActualEscuelas;

            $carreras = carrerasPrincipalesCalendario($calendario['id']);
            $calendarioAnteriorCarreras['label'] = $calendario2['nombre'];
            $calendarioActualCarreras['label'] = $calendario['nombre'];
            foreach($carreras as $carrera) { 
                $final['labelCarreras'][] = $carrera->nombre;
                $calendarioAnteriorCarreras['data'][] = inscritosCarrera($carrera->nombre, $calendario2['id'], $request['aldia'], $fecha2, $request['idPlantelCarrera']);
                $calendarioActualCarreras['data'][] = inscritosCarrera($carrera->nombre, $calendario['id'], $request['aldia'], $fecha, $request['idPlantelCarrera']);
            }
            $final['carreras'][] = $calendarioAnteriorCarreras;
            $final['carreras'][] = $calendarioActualCarreras;

            $centros = centrosPrincipalesCalendario($calendario['id']);
            $calendarioAnteriorCentros['label'] = $calendario2['nombre'];
            $calendarioActualCentros['label'] = $calendario['nombre'];
            foreach($centros as $centro) { 
                $final['labelCentros'][] = $centro->nombre;
                $calendarioAnteriorCentros['data'][] = inscritosCentro($centro->id, $calendario2['id'], $request['aldia'], $fecha2, $request['idPlantelCentro']);
                $calendarioActualCentros['data'][] = inscritosCentro($centro->id, $calendario['id'], $request['aldia'], $fecha, $request['idPlantelCentro']);
            }
            $final['centros'][] = $calendarioAnteriorCentros;
            $final['centros'][] = $calendarioActualCentros;


            return response()->json($final, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function estadisticasMedios(Request $request){
        try {
            $date = Carbon::parse($request['dia']);
            $fecha = $date->format('Y-m-d');
            $fecha2 = $date->subYear(1)->format('Y-m-d');
            $calendario = calendarioActualDia($fecha);
            $calendario2 = calendarioActualDia($fecha2);

            //Medios de contacto
            $medios1 = InscritosMediosContacto($calendario['id'], $request['aldia'], $fecha, $request['idPlantelMedios']);
            $medios2 = InscritosMediosContacto($calendario2['id'], $request['aldia'], $fecha2, $request['idPlantelMedios']);
            $mediosOne['label'] = $calendario['nombre'];
            foreach ($medios1 as $medio) {
                $final['labelMedios'][] = $medio->nombre;
                $mediosOne['data'][] = $medio->cantidad;
            }
            $mediosTwo['label'] = $calendario['nombre'];
            foreach ($medios2 as $medio) {
                $mediosTwo['data'][] = $medio->cantidad;
            }
            $final['medios'][] = $mediosOne;
            $final['medios'][] = $mediosTwo;

            return response()->json($final, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function estadisticasMotivos(Request $request){
        try {
            $date = Carbon::parse($request['dia']);
            $fecha = $date->format('Y-m-d');
            $fecha2 = $date->subYear(1)->format('Y-m-d');
            $calendario = calendarioActualDia($fecha);
            $calendario2 = calendarioActualDia($fecha2);

            $motivos1 = inscritosMotivosInscripcion($calendario['id'], $request['aldia'], $fecha, $request['idPlantelMotivo']);
            $motivos2 = inscritosMotivosInscripcion($calendario2['id'], $request['aldia'], $fecha2, $request['idPlantelMotivo']);
            $motivosOne['label'] = $calendario['nombre'];
            foreach ($motivos1 as $motivo) {
                $final['labelMotivos'][] = $motivo->nombre;
                $motivosOne['data'][] = $motivo->cantidad;
            }
            $motivosTwo['label'] = $calendario['nombre'];
            foreach ($motivos2 as $motivo) {
                $motivosTwo['data'][] = $motivo->cantidad;
            }
            $final['motivos'][] = $motivosOne;
            $final['motivos'][] = $motivosTwo;
            return response()->json($final, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function estadisticasPublicitarios(Request $request){
        try {
            $date = Carbon::parse($request['dia']);
            $fecha = $date->format('Y-m-d');
            $fecha2 = $date->subYear(1)->format('Y-m-d');
            $calendario = calendarioActualDia($fecha);
            $calendario2 = calendarioActualDia($fecha2);

            $publicitarios1 = inscritosPublicitarios($calendario['id'], $request['aldia'], $fecha, $request['idPlantelPublicitarios']);
            $publicitarios2 = inscritosPublicitarios($calendario2['id'], $request['aldia'], $fecha2, $request['idPlantelPublicitarios']);
            $publicitariosOne['label'] = $calendario['nombre'];
            foreach ($publicitarios1 as $publicitario) {
                $final['labelPublicitarios'][] = $publicitario->nombre;
                $publicitariosOne['data'][] = $publicitario->cantidad;
            }
            $publicitariosTwo['label'] = $calendario['nombre'];
            foreach ($publicitarios2 as $publicitario) {
                $publicitariosTwo['data'][] = $publicitario->cantidad;
            }
            $final['publicitarios'][] = $publicitariosOne;
            $final['publicitarios'][] = $publicitariosTwo;
            return response()->json($final, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function estadisticasVias(Request $request){
        try {
            $date = Carbon::parse($request['dia']);
            $fecha = $date->format('Y-m-d');
            $fecha2 = $date->subYear(1)->format('Y-m-d');
            $calendario = calendarioActualDia($fecha);
            $calendario2 = calendarioActualDia($fecha2);

            $vias1 = inscritosVias($calendario['id'], $request['aldia'], $fecha, $request['idPlantelVias']);
            $vias2 = inscritosVias($calendario2['id'], $request['aldia'], $fecha2, $request['idPlantelVias']);
            $viasOne['label'] = $calendario['nombre'];
            foreach ($vias1 as $via) {
                $final['labelVias'][] = $via->nombre;
                $viasOne['data'][] = $via->cantidad;
            }
            $viasTwo['label'] = $calendario['nombre'];
            foreach ($vias2 as $via) {
                $viasTwo['data'][] = $via->cantidad;
            }
            $final['vias'][] = $viasOne;
            $final['vias'][] = $viasTwo;
            return response()->json($final, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function estadisticasSexos(Request $request){
        try {
            $date = Carbon::parse($request['dia']);
            $fecha = $date->format('Y-m-d');
            $fecha2 = $date->subYear(1)->format('Y-m-d');
            $calendario = calendarioActualDia($fecha);
            $calendario2 = calendarioActualDia($fecha2);

            $sexos = Sexo::where('eliminado', '=', 0)->get();
            $calendarioActualSexo['label'] = $calendario['nombre'];
            $calendarioAnteriorSexo['label'] = $calendario2['nombre'];
            foreach ($sexos as $sexo) {
                $final['labelSexos'][] = $sexo->nombre;
                $calendarioActualSexo['data'][] = inscritosSexos($sexo->id, $calendario['id'], $request['aldia'], $fecha, $request['idPlantelSexo']);
                $calendarioAnteriorSexo['data'][] = inscritosSexos($sexo->id, $calendario2['id'], $request['aldia'], $fecha2, $request['idPlantelSexo']);

            }
            $final['sexos'][] = $calendarioAnteriorSexo;
            $final['sexos'][] = $calendarioActualSexo;

            return response()->json($final, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function estadisticasEscuelas(Request $request){
        try {
            $date = Carbon::parse($request['dia']);
            $fecha = $date->format('Y-m-d');
            $fecha2 = $date->subYear(1)->format('Y-m-d');
            $calendario = calendarioActualDia($fecha);
            $calendario2 = calendarioActualDia($fecha2);

            $escuelas = escuelasPrincipalesCalendario($calendario['id']);
            $calendarioAnteriorEscuelas['label'] = $calendario2['nombre'];
            $calendarioActualEscuelas['label'] = $calendario['nombre'];
            foreach($escuelas as $escuela) { 
                $final['labelEscuelas'][] = $escuela->nombre;
                $calendarioAnteriorEscuelas['data'][] = inscritosEscuela($escuela->id, $calendario2['id'], $request['aldia'], $fecha2, $request['idPlantelEscuela']);
                $calendarioActualEscuelas['data'][] = inscritosEscuela($escuela->id, $calendario['id'], $request['aldia'], $fecha, $request['idPlantelEscuela']);
            }
            $final['escuelas'][] = $calendarioAnteriorEscuelas;
            $final['escuelas'][] = $calendarioActualEscuelas;

            return response()->json($final, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function estadisticasCarreras(Request $request){
        try {
            $date = Carbon::parse($request['dia']);
            $fecha = $date->format('Y-m-d');
            $fecha2 = $date->subYear(1)->format('Y-m-d');
            $calendario = calendarioActualDia($fecha);
            $calendario2 = calendarioActualDia($fecha2);

            $carreras = carrerasPrincipalesCalendario($calendario['id']);
            $calendarioAnteriorCarreras['label'] = $calendario2['nombre'];
            $calendarioActualCarreras['label'] = $calendario['nombre'];
            foreach($carreras as $carrera) { 
                $final['labelCarreras'][] = $carrera->nombre;
                $calendarioAnteriorCarreras['data'][] = inscritosCarrera($carrera->nombre, $calendario2['id'], $request['aldia'], $fecha2, $request['idPlantelCarrera']);
                $calendarioActualCarreras['data'][] = inscritosCarrera($carrera->nombre, $calendario['id'], $request['aldia'], $fecha, $request['idPlantelCarrera']);
            }
            $final['carreras'][] = $calendarioAnteriorCarreras;
            $final['carreras'][] = $calendarioActualCarreras;

            return response()->json($final, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function estadisticasCentros(Request $request){
        try {
            $date = Carbon::parse($request['dia']);
            $fecha = $date->format('Y-m-d');
            $fecha2 = $date->subYear(1)->format('Y-m-d');
            $calendario = calendarioActualDia($fecha);
            $calendario2 = calendarioActualDia($fecha2);

            $centros = centrosPrincipalesCalendario($calendario['id']);
            $calendarioAnteriorCentros['label'] = $calendario2['nombre'];
            $calendarioActualCentros['label'] = $calendario['nombre'];
            foreach($centros as $centro) { 
                $final['labelCentros'][] = $centro->nombre;
                $calendarioAnteriorCentros['data'][] = inscritosCentro($centro->id, $calendario2['id'], $request['aldia'], $fecha2, $request['idPlantelCentro']);
                $calendarioActualCentros['data'][] = inscritosCentro($centro->id, $calendario['id'], $request['aldia'], $fecha, $request['idPlantelCentro']);
            }
            $final['centros'][] = $calendarioAnteriorCentros;
            $final['centros'][] = $calendarioActualCentros;

            return response()->json($final, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function estadisticasFinancieras(Request $request){
        try {
            $date = Carbon::parse($request['dia']);
            $fecha = $date->format('Y-m-d');
            $fecha2 = $date->subYear(1)->format('Y-m-d');
            $calendario = calendarioActualDia($fecha);
            $calendario2 = calendarioActualDia($fecha2);

            $sucursales = Sucursale::where('eliminado', '=', 0)->whereRaw('LENGTH(mapa) > 0')->get();
            $anteriorSucursales['label'] = $calendario2['nombre'];
            $actualSucursales['label'] = $calendario['nombre'];
            foreach ($sucursales as $sucursal) {
                $final['labelSucursales'][] = $sucursal->nombre;
                $anteriorSucursales['data'][] = ingresosSucursal($sucursal->id, $calendario2['id'], $request['aldia'], $fecha2);
                $actualSucursales['data'][] = ingresosSucursal($sucursal->id, $calendario['id'], $request['aldia'], $fecha);
            }

            $final['sucursales'][] = $anteriorSucursales;
            $final['sucursales'][] = $actualSucursales;

            $anteriorMeses['label'] = $calendario2['nombre'];
            $actualMeses['label'] = $calendario['nombre'];
            for ($i=1; $i < 13; $i++) { 
                $final['labelMeses'][] = mesEntero($i);
                $anteriorMeses['data'][] = ingresosMes($i, $calendario2['id'], $request['aldia'], $fecha2, $request['idPlantelMeses']);
                $actualMeses['data'][] = ingresosMes($i, $calendario['id'], $request['aldia'], $fecha, $request['idPlantelMeses']);
            }
            $final['meses'][] = $anteriorMeses;
            $final['meses'][] = $actualMeses;

            $modalidades = Modalidade::where('eliminado', '=', 0)->get();
            $anteriorModalidades['label'] = $calendario2['nombre'];
            $actualModalidades['label'] = $calendario['nombre'];
            foreach ($modalidades as $modalidad) {
                $final['labelModalidades'][] = $modalidad->nombre;
                $anteriorModalidades['data'][] = ingresosModalidad($modalidad->id, $calendario2['id'], $request['aldia'], $fecha2, $request['idPlantelModalidades']);
                $actualModalidades['data'][] = ingresosModalidad($modalidad->id, $calendario['id'], $request['aldia'], $fecha, $request['idPlantelModalidades']);
            }
            $final['modalidades'][] = $anteriorModalidades;
            $final['modalidades'][] = $actualModalidades;

            $cursos = Ficha::join('grupos', 'fichas.idGrupo', '=', 'grupos.id')->
            join('altacursos', 'grupos.idAltaCurso', '=', 'altacursos.id')->
            join('cursos', 'altacursos.idCurso', '=', 'cursos.id')->
            select('cursos.id', 'cursos.nombre')->
            where('altacursos.idModalidad', '=', 1)->
            where('altacursos.idSubnivel', '=', 1)->
            groupBy('cursos.id', 'cursos.nombre')->get();
            $anteriorCursosLVL['label'] = $calendario2['nombre'];
            $actualCursosLVL['label'] = $calendario['nombre'];
            foreach ($cursos as $curso) {
                $final['labelCursosLVL'][] = $curso->nombre;
                $anteriorCursosLVL['data'][] = ingresosCurso($curso->id, $calendario2['id'], $request['aldia'], $fecha2, $request['idPlantelCursosLVL'], true);
                $actualCursosLVL['data'][] = ingresosCurso($curso->id, $calendario['id'], $request['aldia'], $fecha, $request['idPlantelCursosLVL'], true);
            }
            $final['cursosLVL'][] = $anteriorCursosLVL;
            $final['cursosLVL'][] = $actualCursosLVL;


            $cursos = Ficha::join('grupos', 'fichas.idGrupo', '=', 'grupos.id')->
            join('altacursos', 'grupos.idAltaCurso', '=', 'altacursos.id')->
            join('cursos', 'altacursos.idCurso', '=', 'cursos.id')->
            select('cursos.id', 'cursos.nombre')->
            where('altacursos.idModalidad', '>', 1)->
            where('altacursos.idModalidad', '<', 5)->
            where('altacursos.idSubnivel', '=', 1)->
            groupBy('cursos.id', 'cursos.nombre')->get();
            $anteriorCursosFSL['label'] = $calendario2['nombre'];
            $actualCursosFSL['label'] = $calendario['nombre'];
            foreach ($cursos as $curso) {
                $final['labelCursosFSL'][] = $curso->nombre;
                $anteriorCursosFSL['data'][] = ingresosCurso($curso->id, $calendario2['id'], $request['aldia'], $fecha2, $request['idPlantelCursosFSL'], false);
                $actualCursosFSL['data'][] = ingresosCurso($curso->id, $calendario['id'], $request['aldia'], $fecha, $request['idPlantelCursosFSL'], false);
            }
            $final['cursosFSL'][] = $anteriorCursosFSL;
            $final['cursosFSL'][] = $actualCursosFSL;


            $cursos = Ficha::join('grupos', 'fichas.idGrupo', '=', 'grupos.id')->
            join('altacursos', 'grupos.idAltaCurso', '=', 'altacursos.id')->
            join('cursos', 'altacursos.idCurso', '=', 'cursos.id')->
            select('cursos.id', 'cursos.nombre')->
            where('altacursos.idModalidad', '=', 1)->
            where('altacursos.idSubnivel', '=', 2)->
            groupBy('cursos.id', 'cursos.nombre')->get();
            $anteriorCursosLVP['label'] = $calendario2['nombre'];
            $actualCursosLVP['label'] = $calendario['nombre'];
            foreach ($cursos as $curso) {
                $final['labelCursosLVP'][] = $curso->nombre;
                $anteriorCursosLVP['data'][] = ingresosCurso($curso->id, $calendario2['id'], $request['aldia'], $fecha2, $request['idPlantelCursosLVP'], true);
                $actualCursosLVP['data'][] = ingresosCurso($curso->id, $calendario['id'], $request['aldia'], $fecha, $request['idPlantelCursosLVP'], true);
            }
            $final['cursosLVP'][] = $anteriorCursosLVP;
            $final['cursosLVP'][] = $actualCursosLVP;


            $cursos = Ficha::join('grupos', 'fichas.idGrupo', '=', 'grupos.id')->
            join('altacursos', 'grupos.idAltaCurso', '=', 'altacursos.id')->
            join('cursos', 'altacursos.idCurso', '=', 'cursos.id')->
            select('cursos.id', 'cursos.nombre')->
            where('altacursos.idModalidad', '=', 1)->
            where('altacursos.idSubnivel', '=', 2)->
            groupBy('cursos.id', 'cursos.nombre')->get();
            $anteriorCursosFSP['label'] = $calendario2['nombre'];
            $actualCursosFSP['label'] = $calendario['nombre'];
            foreach ($cursos as $curso) {
                $final['labelCursosFSP'][] = $curso->nombre;
                $anteriorCursosFSP['data'][] = ingresosCurso($curso->id, $calendario2['id'], $request['aldia'], $fecha2, $request['idPlantelCursosFSP'], true);
                $actualCursosFSP['data'][] = ingresosCurso($curso->id, $calendario['id'], $request['aldia'], $fecha, $request['idPlantelCursosFSP'], true);
            }
            $final['cursosFSP'][] = $anteriorCursosFSP;
            $final['cursosFSP'][] = $actualCursosFSP;


            $sucursales = Sucursale::where('eliminado', '=', 0)->whereRaw('LENGTH(mapa) > 0')->get();
            $anteriorSucursalesMorosos['label'] = $calendario2['nombre'];
            $actualSucursalesMorosos['label'] = $calendario['nombre'];
            foreach ($sucursales as $sucursal) {
                $final['labelSucursalesMorosos'][] = $sucursal->nombre;
                $anteriorSucursalesMorosos['data'][] = saldoActualPlantel($sucursal->id, $request['aldia'], $fecha2, $calendario2['id']);
                $actualSucursalesMorosos['data'][] = saldoActualPlantel($sucursal->id, $request['aldia'], $fecha, $calendario['id']);
            }

            $final['sucursalesMorosos'][] = $anteriorSucursalesMorosos;
            $final['sucursalesMorosos'][] = $actualSucursalesMorosos;

            $cursos = Curso::where('eliminado', '=', 0)->get();
            $anteriorCursosMorosos['label'] = $calendario2['nombre'];
            $actualCursosMorosos['label'] = $calendario['nombre'];
             foreach ($cursos as $curso) {
                 $final['labelCursosMorosos'][] = $curso->nombre;
                 $anteriorCursosMorosos['data'][] = saldoActualPlantelCurso($request['idPlantelCursosMorosos'], $request['aldia'], $fecha2, $calendario2['id'], $curso->id);
                 $actualCursosMorosos['data'][] = saldoActualPlantelCurso($request['idPlantelCursosMorosos'], $request['aldia'], $fecha, $calendario['id'], $curso->id);
             }
             $final['cursosMorosos'][] = $anteriorCursosMorosos;
             $final['cursosMorosos'][] = $actualCursosMorosos;

            return response()->json($final, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function estadisticasFinancierasMeses(Request $request){
        try {
            $date = Carbon::parse($request['dia']);
            $fecha = $date->format('Y-m-d');
            $fecha2 = $date->subYear(1)->format('Y-m-d');
            $calendario = calendarioActualDia($fecha);
            $calendario2 = calendarioActualDia($fecha2);

            $anteriorMeses['label'] = $calendario2['nombre'];
            $actualMeses['label'] = $calendario['nombre'];
            for ($i=1; $i < 13; $i++) { 
                $final['labelMeses'][] = mesEntero($i);
                $anteriorMeses['data'][] = ingresosMes($i, $calendario2['id'], $request['aldia'], $fecha2, $request['idPlantelMeses']);
                $actualMeses['data'][] = ingresosMes($i, $calendario['id'], $request['aldia'], $fecha, $request['idPlantelMeses']);
            }

            $final['meses'][] = $anteriorMeses;
            $final['meses'][] = $actualMeses;

            return response()->json($final, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function estadisticasFinancierasModalidades(Request $request){
        try {
            $date = Carbon::parse($request['dia']);
            $fecha = $date->format('Y-m-d');
            $fecha2 = $date->subYear(1)->format('Y-m-d');
            $calendario = calendarioActualDia($fecha);
            $calendario2 = calendarioActualDia($fecha2);

            $modalidades = Modalidade::where('eliminado', '=', 0)->get();
            $anteriorModalidades['label'] = $calendario2['nombre'];
            $actualModalidades['label'] = $calendario['nombre'];
            foreach ($modalidades as $modalidad) {
                $final['labelModalidades'][] = $modalidad->nombre;
                $anteriorModalidades['data'][] = ingresosModalidad($modalidad->id, $calendario2['id'], $request['aldia'], $fecha2, $request['idPlantelModalidades']);
                $actualModalidades['data'][] = ingresosModalidad($modalidad->id, $calendario['id'], $request['aldia'], $fecha, $request['idPlantelModalidades']);
            }
            $final['modalidades'][] = $anteriorModalidades;
            $final['modalidades'][] = $actualModalidades;
            return response()->json($final, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function estadisticasFinancierasCursosLVL(Request $request){
        try {
            $date = Carbon::parse($request['dia']);
            $fecha = $date->format('Y-m-d');
            $fecha2 = $date->subYear(1)->format('Y-m-d');
            $calendario = calendarioActualDia($fecha);
            $calendario2 = calendarioActualDia($fecha2);

            $cursos = Ficha::join('grupos', 'fichas.idGrupo', '=', 'grupos.id')->
            join('altacursos', 'grupos.idAltaCurso', '=', 'altacursos.id')->
            join('cursos', 'altacursos.idCurso', '=', 'cursos.id')->
            select('cursos.id', 'cursos.nombre')->
            where('altacursos.idModalidad', '=', 1)->
            where('altacursos.idSubnivel', '=', 1)->
            groupBy('cursos.id', 'cursos.nombre')->get();
            $anteriorCursosLVL['label'] = $calendario2['nombre'];
            $actualCursosLVL['label'] = $calendario['nombre'];
            foreach ($cursos as $curso) {
                $final['labelCursosLVL'][] = $curso->nombre;
                $anteriorCursosLVL['data'][] = ingresosCurso($curso->id, $calendario2['id'], $request['aldia'], $fecha2, $request['idPlantelCursosLVL'], true);
                $actualCursosLVL['data'][] = ingresosCurso($curso->id, $calendario['id'], $request['aldia'], $fecha, $request['idPlantelCursosLVL'], true);
            }
            $final['cursosLVL'][] = $anteriorCursosLVL;
            $final['cursosLVL'][] = $actualCursosLVL;

            return response()->json($final, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function estadisticasFinancierasCursosFSL(Request $request){
        try {
            $date = Carbon::parse($request['dia']);
            $fecha = $date->format('Y-m-d');
            $fecha2 = $date->subYear(1)->format('Y-m-d');
            $calendario = calendarioActualDia($fecha);
            $calendario2 = calendarioActualDia($fecha2);

            $cursos = Ficha::join('grupos', 'fichas.idGrupo', '=', 'grupos.id')->
            join('altacursos', 'grupos.idAltaCurso', '=', 'altacursos.id')->
            join('cursos', 'altacursos.idCurso', '=', 'cursos.id')->
            select('cursos.id', 'cursos.nombre')->
            where('altacursos.idModalidad', '>', 1)->
            where('altacursos.idModalidad', '<', 5)->
            where('altacursos.idSubnivel', '=', 1)->
            groupBy('cursos.id', 'cursos.nombre')->get();
            $anteriorCursosFSL['label'] = $calendario2['nombre'];
            $actualCursosFSL['label'] = $calendario['nombre'];
            foreach ($cursos as $curso) {
                $final['labelCursosFSL'][] = $curso->nombre;
                $anteriorCursosFSL['data'][] = ingresosCurso($curso->id, $calendario2['id'], $request['aldia'], $fecha2, $request['idPlantelCursosFSL'], false);
                $actualCursosFSL['data'][] = ingresosCurso($curso->id, $calendario['id'], $request['aldia'], $fecha, $request['idPlantelCursosFSL'], false);
            }
            $final['cursosFSL'][] = $anteriorCursosFSL;
            $final['cursosFSL'][] = $actualCursosFSL;

            return response()->json($final, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function estadisticasFinancierasCursosLVP(Request $request){
        try {
            $date = Carbon::parse($request['dia']);
            $fecha = $date->format('Y-m-d');
            $fecha2 = $date->subYear(1)->format('Y-m-d');
            $calendario = calendarioActualDia($fecha);
            $calendario2 = calendarioActualDia($fecha2);


            $cursos = Ficha::join('grupos', 'fichas.idGrupo', '=', 'grupos.id')->
            join('altacursos', 'grupos.idAltaCurso', '=', 'altacursos.id')->
            join('cursos', 'altacursos.idCurso', '=', 'cursos.id')->
            select('cursos.id', 'cursos.nombre')->
            where('altacursos.idModalidad', '=', 1)->
            where('altacursos.idSubnivel', '=', 2)->
            groupBy('cursos.id', 'cursos.nombre')->get();
            $anteriorCursosLVP['label'] = $calendario2['nombre'];
            $actualCursosLVP['label'] = $calendario['nombre'];
            foreach ($cursos as $curso) {
                $final['labelCursosLVP'][] = $curso->nombre;
                $anteriorCursosLVP['data'][] = ingresosCurso($curso->id, $calendario2['id'], $request['aldia'], $fecha2, $request['idPlantelCursosLVP'], true);
                $actualCursosLVP['data'][] = ingresosCurso($curso->id, $calendario['id'], $request['aldia'], $fecha, $request['idPlantelCursosLVP'], true);
            }
            $final['cursosLVP'][] = $anteriorCursosLVP;
            $final['cursosLVP'][] = $actualCursosLVP;

            return response()->json($final, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function estadisticasFinancierasCursosFSP(Request $request){
        try {
            $date = Carbon::parse($request['dia']);
            $fecha = $date->format('Y-m-d');
            $fecha2 = $date->subYear(1)->format('Y-m-d');
            $calendario = calendarioActualDia($fecha);
            $calendario2 = calendarioActualDia($fecha2);

            $cursos = Ficha::join('grupos', 'fichas.idGrupo', '=', 'grupos.id')->
            join('altacursos', 'grupos.idAltaCurso', '=', 'altacursos.id')->
            join('cursos', 'altacursos.idCurso', '=', 'cursos.id')->
            select('cursos.id', 'cursos.nombre')->
            where('altacursos.idModalidad', '>', 1)->
            where('altacursos.idModalidad', '<', 5)->
            where('altacursos.idSubnivel', '=', 2)->
            groupBy('cursos.id', 'cursos.nombre')->get();
            $anteriorCursosFSP['label'] = $calendario2['nombre'];
            $actualCursosFSP['label'] = $calendario['nombre'];
            foreach ($cursos as $curso) {
                $final['labelCursosFSP'][] = $curso->nombre;
                $anteriorCursosFSP['data'][] = ingresosCurso($curso->id, $calendario2['id'], $request['aldia'], $fecha2, $request['idPlantelCursosFSP'], false);
                $actualCursosFSP['data'][] = ingresosCurso($curso->id, $calendario['id'], $request['aldia'], $fecha, $request['idPlantelCursosFSP'], false);
            }
            $final['cursosFSP'][] = $anteriorCursosFSP;
            $final['cursosFSP'][] = $actualCursosFSP;

            return response()->json($final, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function estadisticasFinancierasCursosMorosos(Request $request){
        try {
            $date = Carbon::parse($request['dia']);
            $fecha = $date->format('Y-m-d');
            $fecha2 = $date->subYear(1)->format('Y-m-d');
            $calendario = calendarioActualDia($fecha);
            $calendario2 = calendarioActualDia($fecha2);

            $cursos = Curso::where('eliminado', '=', 0)->get();
            $anteriorCursosMorosos['label'] = $calendario2['nombre'];
            $actualCursosMorosos['label'] = $calendario['nombre'];
             foreach ($cursos as $curso) {
                 $final['labelCursosMorosos'][] = $curso->nombre;
                 $anteriorCursosMorosos['data'][] = saldoActualPlantelCurso($request['idPlantelCursosMorosos'], $request['aldia'], $fecha2, $calendario2['id'], $curso->id);
                 $actualCursosMorosos['data'][] = saldoActualPlantelCurso($request['idPlantelCursosMorosos'], $request['aldia'], $fecha, $calendario['id'], $curso->id);
             }
             $final['cursosMorosos'][] = $anteriorCursosMorosos;
             $final['cursosMorosos'][] = $actualCursosMorosos;

            return response()->json($final, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}