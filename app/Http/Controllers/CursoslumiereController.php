<?php

namespace App\Http\Controllers;

use App\Altacurso;
use App\Webmenu;
use App\Websubmenu;
use App\Webpagina;
use App\Sedesucursale;
use App\Sede;
use App\Grupo;
use App\Centrosuniversitario;
use App\Webpaginaconfiguracione;
use App\Webbannerconfiguracione;
use App\Webtituloconfiguracione;
use App\Websubtituloconfiguracione;
use App\Webvideoconfiguracione;
use App\Webparrafoconfiguracione;
use App\Weblistadoconfiguracione;
use App\Websucursalruta;
use App\Websucursalescuela;
use App\Webblognoticia;
use App\Webblogconfiguracione;
use App\Webvigencia;
use App\Webcursoconfiguracione;
use App\Webcursobeneficio;
use App\Webcursoextra;
use App\Carrera;
use App\Sucursale;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
include "funciones/FuncionesGenerales.php";

class CursoslumiereController extends BaseController
{
    function pagina(){
        try{
            $respuesta = array();

            //Menus
            $menus = Webmenu::where('eliminado', '=', 0)->where('activo', '=', 1)->get();
            $registros = array();
            foreach ($menus as $menu) {
                if($menu->submenu){
                    $submenus = Websubmenu::join('webpaginas', 'idPagina', '=', 'webpaginas.id')->
                                           select('websubmenus.*', 'webpaginas.url as url')->
                                           where('websubmenus.idMenu', '=', $menu->id)->where('websubmenus.activo', '=', 1)->get();
                    $menu->submenus = $submenus;
                }else{
                    $pagina = Webpagina::find($menu->idPagina);
                    $menu->url = $pagina->url;
                }
                $registros[] = $menu;
            }
            $respuesta['menus'] = $registros;

            $hay = Webvigencia::all();
            $vigencia = (count($hay) > 0) ? $hay[0]->vigencia : 'N/A';
            $respuesta['vigencia'] = $vigencia;

            //Sucursales
            $sucursales = Sedesucursale::join('sucursales', 'idSucursal', '=', 'sucursales.id')->
                                select('sucursales.*')->
                                where('sedesucursales.idSede', '=', 1)->
                                where('sucursales.eliminado', '=', 0)->
                                where('sucursales.activo', '=', 1)->get();
            
            $resSuc = array();
            foreach ($sucursales as $sucursal) {
                $rutas = Websucursalruta::where('idSucursal', '=', $sucursal->id)->get();
                $route = "";
                foreach ($rutas as $ruta) {
                    $route = $route.','.$ruta->ruta;
                }
                $sucursal->rutas = $route;

                $escuelas = Websucursalescuela::where('idSucursal', '=', $sucursal->id)->get();
                $school = "";
                foreach ($escuelas as $escuela) {
                    $school = $school.','.$escuela->escuela;
                }
                $sucursal->escuelas = $school;

                $resSuc[] = $sucursal;
            }
            $respuesta['planteles'] = $resSuc;

            $consulta = "SELECT * FROM sedes where eliminado = 0 AND imagen IS NOT NULL AND length(imagen) > 0 AND activo = 1";
            $respuesta['sedes'] = DB::select($consulta, array());


            $paginas = Webpagina::all();
            foreach ($paginas as $pagina) {
                $configuraciones = Webpaginaconfiguracione::where('idPagina', '=', $pagina->id)->orderBy('posicion', 'ASC')->get();
                $configs = array();
                foreach ($configuraciones as $configuracion) {
                    if(intval($configuracion->idComponente) === 1){
                        $componentes = Webbannerconfiguracione::where('idConfiguracion', '=', $configuracion->id)->orderBy('posicion', 'ASC')->get();
                        $configuracion->componentes = $componentes;
                    }
                    if(intval($configuracion->idComponente) === 2){
                        $componentes = Webtituloconfiguracione::where('idConfiguracion', '=', $configuracion->id)->get();
                        $configuracion->titulo = $componentes[0]->texto;
                        $configuracion->clase = $componentes[0]->clase;
                        
                    }
                    /*if(intval($configuracion->idComponente) === 3){
                        $componentes = Websubtituloconfiguracione::where('idConfiguracion', '=', $configuracion->id)->get();
                        $configuracion->titulo = $componentes[0]->texto;
                    }*/
                    if(intval($configuracion->idComponente) === 4){
                        $componentes = Webvideoconfiguracione::where('idConfiguracion', '=', $configuracion->id)->get();
                        $configuracion->texto = $componentes[0]->texto;
                        $configuracion->url = $componentes[0]->video;
                        $configuracion->pagina = Webpagina::find($componentes[0]->idPagina)->url;
                    }
                    if(intval($configuracion->idComponente) === 7){
                        $c = $configuracion->id;
                        /*$componentes = Websubtituloconfiguracione::where('idConfiguracion', '=', $configuracion->id)->get();
                        $configuracion->subtitulo = (count($componentes) > 0) ? $componentes[0]->texto : '';*/
                        $consulta = "SELECT cc.descuento as descuentazo, c.icono AS imagen, c.nombre AS nombre, s.nombre AS subnivel, i.inicio AS inicio,
                                    cc.semanas AS duracion, m.nombre AS modalidad, cat.nombre AS vigencia, i.precio AS precio, i.id as idAltaCurso, 
                                    ((i.precio) - (i.precio * (cc.descuento/100))) AS descuento, cc.liga AS referencia, cat.nombre as categoria
                                    FROM niveles n, subniveles s, modalidades m, calendarios ca, cursos c, altacursos i,
                                    categorias cat, sedes se, webaltasconfiguraciones w, webcursoconfiguraciones cc
                                    WHERE i.eliminado = 0 AND i.idModalidad = m.id AND i.idNivel = n.id AND i.idSubnivel = s.id AND i.idCalendario = ca.id 
                                    AND i.idCurso = c.id AND i.idCategoria = cat.id AND i.idSede = se.id AND w.idAltaCurso = i.id AND cc.idAltaCurso = i.id
                                    AND w.idConfiguracion = $c ORDER BY w.posicion ASC";
                        $cursos = array();                                   
                        $resultado = DB::select($consulta, array());
                        foreach ($resultado as $curso) {
                            $curso->inicio = formatearFechaMes($curso->inicio);
                            $cursos[] = $curso;
                        }
                        $configuracion->cursos = $cursos;
                    }
                    /*if(intval($configuracion->idComponente) === 9){
                        $componentes = Webblogconfiguracione::where('idConfiguracion', '=', $configuracion->id)->get();
                        $noticias = array();
                        if(count($componentes) > 0){
                            $primerNoticia = Webblognoticia::find($componentes[0]->idNoticia1);
                            $primerNoticia->fecha = formatearFecha($primerNoticia->fecha);
                            $noticias[] = $primerNoticia;

                            $segundaNoticia = Webblognoticia::find($componentes[0]->idNoticia2);
                            $segundaNoticia->fecha = formatearFecha($segundaNoticia->fecha);
                            $noticias[] = $segundaNoticia;

                            $tercerNoticia = Webblognoticia::find($componentes[0]->idNoticia3);
                            $tercerNoticia->fecha = formatearFecha($tercerNoticia->fecha);
                            $noticias[] = $tercerNoticia;
                        }
                        $configuracion->noticias = $noticias;
                    }
                    if(intval($configuracion->idComponente) === 13){
                        $componentes = Webparrafoconfiguracione::where('idConfiguracion', '=', $configuracion->id)->get();
                        $configuracion->parrafo = str_replace('\n', '<br>', $componentes[0]->parrafo);
                    }*/
                    if(intval($configuracion->idComponente) === 14){
                        $componentes = Weblistadoconfiguracione::where('idConfiguracion', '=', $configuracion->id)->get();
                        $configuracion->componentes = $componentes;
                    }
                    $configs[] = $configuracion;
                }
                $pagina->configuraciones = $configs;
                $respuesta['paginas'][] = $pagina;
                //$noticias = Webblognoticia::orderBy('created_at', 'DESC')->take(10)->get();
                //$respuesta['noticias'] = $noticias;
            }


            $centros = Centrosuniversitario::where('idUniversidad', '=', 7)->where('imagen', '<>', NULL)->get();
            $listaCentros = array();
            foreach ($centros as $centro) {
                $centro->carreras = Carrera::where('idCentroUniversitario', '=', $centro->id)->where('idCalendario', '=', 6)->get();
                $listaCentros[] = $centro;
            }
            $respuesta['centros'] = $listaCentros;
            return response()->json($respuesta, 200);
        }catch(Exception $e){
            return response()->json('Error al mostrar cursos', 400);
        }
    }

    function noticia(Request $request){
        try {
            $respuesta = array();
            $noticia = Webblognoticia::where('url', '=', $request['noticia'])->get();
            $respuesta['noticia'] = array();
            if(count($noticia) > 0){
                $respuesta['noticia'] = $noticia[0];
            }
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function tipo(Request $request){
        try {
            $alta = Altacurso::find($request['curso']);
            return response()->json($alta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function curso(Request $request){
        try {
            $respuestas = array();
            $configuracion = Webcursoconfiguracione::where('idAltaCurso', '=', $request['curso'])->get()[0];
            $beneficios = Webcursobeneficio::where('idAltaCurso', '=', $request['curso'])->get();
            $extras = Webcursoextra::where('idAltaCurso', '=', $request['curso'])->get();

            $consulta = "SELECT c.icono AS imagen, c.nombre AS nombre, s.nombre AS subnivel, i.inicio AS inicio, i.fin as fin,
                                    cc.semanas AS duracion, m.nombre AS modalidad, cat.nombre AS vigencia, i.precio AS precio, i.id as idAltaCurso, 
                                    ((i.precio) - (i.precio * (cc.descuento/100))) AS descuento, cc.liga AS referencia, cat.nombre as categoria, i.idCategoria, i.idNivel 
                                    FROM niveles n, subniveles s, modalidades m, calendarios ca, cursos c, altacursos i,
                                    categorias cat, sedes se, webcursoconfiguraciones cc
                                    WHERE i.eliminado = 0 AND i.idModalidad = m.id AND i.idNivel = n.id AND i.idSubnivel = s.id AND i.idCalendario = ca.id 
                                    AND i.idCurso = c.id AND i.idCategoria = cat.id AND i.idSede = se.id 
                                    AND cc.idAltaCurso = i.id AND i.id = ".$request['curso'];
            $datos = DB::select($consulta, array())[0];
            $datos->inicio = formatearFechaMes($datos->inicio);
            $datos->fin = formatearFechaMes($datos->fin);
            $consultaHorarios = "SELECT CONCAT(h.inicio, ' ', 'hrs', ' ', '-', ' ', h.fin, ' ', 'hrs') as horario FROM horarios h, grupos g WHERE h.id = g.idHorario AND g.idAltaCurso = ".$request['curso'];
            $horarios = DB::select($consultaHorarios, array());


            $respuesta['beneficios'] = $beneficios;
            $respuesta['configuracion'] = $configuracion;
            $respuesta['extras'] = $extras;
            $respuesta['datos'] = $datos;
            $respuesta['horarios'] = $horarios;
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function proximos4(){
        try {
            $configuraciones = Webpaginaconfiguracione::where('idPagina', '=', 1)->where('idComponente', '=', 7)->orderBy('posicion', 'ASC')->get()[0];
            $c = $configuraciones->id;
            $componentes = Websubtituloconfiguracione::where('idConfiguracion', '=', $configuraciones->id)->get();
            $configuraciones->subtitulo = (count($componentes) > 0) ? $componentes[0]->texto : '';
            $consulta = "SELECT c.icono AS imagen, c.nombre AS nombre, s.nombre AS subnivel, i.inicio AS inicio,
                        cc.semanas AS duracion, m.nombre AS modalidad, cat.nombre AS vigencia, i.precio AS precio, i.id as idAltaCurso, 
                        ((i.precio) - (i.precio * (cc.descuento/100))) AS descuento, cc.liga AS referencia, cat.nombre as categoria
                        FROM niveles n, subniveles s, modalidades m, calendarios ca, cursos c, altacursos i,
                        categorias cat, sedes se, webaltasconfiguraciones w, webcursoconfiguraciones cc
                        WHERE i.eliminado = 0 AND i.idModalidad = m.id AND i.idNivel = n.id AND i.idSubnivel = s.id AND i.idCalendario = ca.id 
                        AND i.idCurso = c.id AND i.idCategoria = cat.id AND i.idSede = se.id AND w.idAltaCurso = i.id AND cc.idAltaCurso = i.id
                        AND w.idConfiguracion = $c ORDER BY w.posicion ASC";
            $cursos = array();                                   
            $resultado = DB::select($consulta, array());
            foreach ($resultado as $curso) {
                $curso->inicio = formatearFechaMes($curso->inicio);
                $cursos[] = $curso;
            }
            $final['cursos'] = $cursos;
            $final['sucursales'] = Sucursale::where('eliminado', '=', 0)->whereRaw('LENGTH(mapa) > 0')->get();
            return response()->json($final, 200);
        } catch (Exception $e) {
            return response()->json('No se encontraron cursos', 400);
        }
    }
}