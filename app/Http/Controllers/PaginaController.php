<?php

namespace App\Http\Controllers;
use App\Webpagina;
use App\Webmenu;
use App\Websubmenu;
use App\Webcomponente;
use App\Webpaginaconfiguracione;
use App\Webbannerconfiguracione;
use App\Webtituloconfiguracione;
use App\Webvideoconfiguracione;
use App\Webcursoconfiguracione;
use App\Webcursobeneficio;
use App\Webcursoextra;
use App\Webaltasconfiguracione;
use App\Webvigencia;
use App\Webtestimoniosconfiguracione;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PaginaController extends BaseController
{
    //Paginas
    function traerPaginas(Request $request){
        try {
            $paginas = Webpagina::all();
            return response()->json($paginas, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function nuevaPagina(Request $request){
        try {
            $pagina = Webpagina::create([
                'nombre' => $request['nombre'],
                'url' => $request['url'],
                'eliminado' => 0,
                'activo' => 1
            ]);
            return response()->json($pagina, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificarPagina(Request $request){
        try {
            $pagina = Webpagina::find($request['id']);
            $pagina->nombre = $request['nombre'];
            $pagina->url = $request['url'];
            $pagina->save();
            return response()->json($pagina, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    //Menus
    function traerMenus(){
        try {
            $menus = Webmenu::all();
            $respuesta = array();
            foreach ($menus as $menu) {
                $pagina = Webpagina::where('id', '=', $menu->idPagina)->get();
                $menu->pagina = (count($pagina) > 0) ? $pagina[0]->nombre : '-';
            }
            return response()->json($menus, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function nuevoMenu(Request $request){
        try {
            $menu = Webmenu::create([
                'nombre' => $request['nombre'],
                'idPagina' => $request['idPagina'],
                'submenu' => $request['submenu'],
                'activo' => 1,
                'eliminado' => 0
            ]);
            return response()->json($menu, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificarMenu(Request $request){
        try {
            $menu = Webmenu::find($request['id']);
            $menu->nombre = $request['nombre'];
            $menu->idPagina = $request['idPagina'];
            $menu->submenu = $request['submenu'];
            $menu->save();
            return response()->json($menu, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function activarMenu(Request $request){
        try {
            $menu = Webmenu::find($request['id']);
            $menu->activo = 1;
            $menu->save();
            return response()->json($menu, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function desactivarMenu(Request $request){
        try {
            $menu = Webmenu::find($request['id']);
            $menu->activo = 0;
            $menu->save();
            return response()->json($menu, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    //Submenus
    function traerSubmenus(Request $request){
        try {
            $submenus = Websubmenu::join('webpaginas', 'idPagina', '=', 'webpaginas.id')->
                                    select('websubmenus.*', 'webpaginas.nombre as pagina')->
                                    where('websubmenus.idMenu', '=', $request['idMenu'])->get();
            return response()->json($submenus, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function nuevoSubmenu(Request $request){
        try {
            $submenu = Websubmenu::create([
                'nombre' => $request['nombre'],
                'idPagina' => $request['idPagina'],
                'idMenu' => $request['idMenu'],
                'activo' => 1,
                'eliminado' => 0
            ]);
            return response()->json($submenu, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificarSubmenu(Request $request){
        try {
            $submenu = Websubmenu::find($request['id']);
            $submenu->nombre = $request['nombre'];
            $submenu->idPagina = $request['idPagina'];
            $submenu->save();
            return response()->json($submenu, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function activarSubmenu(Request $request){
        try {
            $submenu = Websubmenu::find($request['id']);
            $submenu->activo = 1;
            $submenu->save();
            return response()->json($submenu, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function desactivarSubmenu(Request $request){
        try {
            $submenu = Websubmenu::find($request['id']);
            $submenu->activo = 0;
            $submenu->save();
            return response()->json($submenu, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    //Componentes
    function traerComponentes(Request $request){
        try {
            $componentes = Webcomponente::orderBy('nombre', 'ASC')->get();
            return response()->json($componentes, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function nuevoComponente(Request $request){
        try {
            //return response()->json($request['configuracion'], 400);
            $componente = Webcomponente::create([
                'nombre' => $request['nombre'],
                'configuracion' => $request['configuracion'],
                'activo' => 1,
                'eliminado' => 0
            ]);
            return response()->json($componente, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificarComponente(Request $request){
        try {
            $componente = Webcomponente::find($request['id']);
            $componente->nombre = $request['nombre'];
            $componente->configuracion = $request['configuracion'];
            $componente->save();
            return response()->json($componente, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    //Configuraciones de pagina
    function nuevaConfiguracionPagina(Request $request){
        try {
            $totalConfiguraciones = Webpaginaconfiguracione::where('idPagina', '=', $request['idPagina'])->get();
            $configuracion = Webpaginaconfiguracione::create([
                'idPagina' => $request['idPagina'],
                'idComponente' => $request['idComponente'],
                'posicion' => (count($totalConfiguraciones)+1),
                'eliminado' => 0,
                'activo' => 1
            ]);
            return response()->json($configuracion, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminarConfiguracionPagina(Request $request){
        try {
            $configuracion = Webpaginaconfiguracione::find($request['id']);
            if(intval($configuracion->idComponente) === 1){
                $banners = Webbannerconfiguracione::where('idConfiguracion', '=', $configuracion->id)->orderBy('posicion', 'ASC')->get();
                foreach ($banners as $banner) {
                    $eliminar = Webbannerconfiguracione::find($banner->id);
                    $eliminar->delete();
                }
            }
            if(intval($configuracion->idComponente) === 2){
                $titulos = Webtituloconfiguracione::where('idConfiguracion', '=', $configuracion->id)->get();
                foreach ($titulos as $titulo) {
                    $eliminar = Webtituloconfiguracione::find($titulo->id);
                    $eliminar->delete();
                }
            }
            if(intval($configuracion->idComponente) === 8){
                $testimonios = Webtestimoniosconfiguracione::where('idConfiguracion', '=', $configuracion->id)->get();
                foreach ($testimonios as $testimonio) {
                    $eliminar = Webtestimoniosconfiguracione::find($testimonio->id);
                    $eliminar->delete();
                }
            }
            if(intval($configuracion->idComponente) === 9){
                $altas = Webaltasconfiguracione::where('idConfiguracion', '=', $configuracion->id)->get();
                foreach ($altas as $alta) {
                    $eliminar = Webaltasconfiguracione::find($alta->id);
                    $eliminar->delete();
                }
            }
            if(intval($configuracion->idComponente) === 10){
                $videos = Webvideoconfiguracione::where('idConfiguracion', '=', $configuracion->id)->get();
                foreach ($videos as $video) {
                    $eliminar = Webvideoconfiguracione::find($video->id);
                    $eliminar->delete();
                }
            }
            $configuracion->delete();
            $configuraciones = Webpaginaconfiguracione::join('webcomponentes', 'idComponente', '=', 'webcomponentes.id')->
                                select('webpaginaconfiguraciones.*', 'webcomponentes.nombre as componente', 'webcomponentes.configuracion as configuracion')->
                                where('idPagina', '=', $request['idPagina'])->orderBy('posicion', 'ASC')->get();
            for ($i=0; $i < count($configuraciones); $i++) { 
                $configuracion = Webpaginaconfiguracione::find($configuraciones[$i]['id']);
                $configuracion->posicion = ($i+1);
                $configuracion->save();
            }
            return response()->json($configuracion, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function traerConfiguracionPagina(Request $request){
        try {
            $configuraciones = Webpaginaconfiguracione::join('webcomponentes', 'idComponente', '=', 'webcomponentes.id')->
                                select('webpaginaconfiguraciones.*', 'webcomponentes.nombre as componente', 'webcomponentes.configuracion as configuracion')->
                                where('idPagina', '=', $request['idPagina'])->orderBy('posicion', 'ASC')->get();
            return response()->json($configuraciones, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function guardarConfiguracion(Request $request){
        try {
            $configuraciones = $request['configuraciones'];
            for ($i=0; $i < count($configuraciones); $i++) { 
                $configuracion = Webpaginaconfiguracione::find($configuraciones[$i]['id']);
                $configuracion->posicion = ($i+1);
                $configuracion->save();
            }
            return response()->json('Todo correcto', 200);   
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    //Configuraciones de Banners
    function nuevoBanner(Request $request){
        try {
            $totalBanners = Webbannerconfiguracione::where('idConfiguracion', '=', $request['idConfiguracion'])->get();
            $banner = Webbannerconfiguracione::create([
                'idConfiguracion' => $request['idConfiguracion'],
                'imagen' => $request['banner'],
                'posicion' => count($totalBanners)+1,
                'activo' => 1,
                'eliminado' =>0
            ]);
            return response()->json($banner, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function traerBanners(Request $request){
        try {
            $banners = Webbannerconfiguracione::where('idConfiguracion', '=', $request['idConfiguracion'])->orderBy('posicion', 'ASC')->get();
            return response()->json($banners, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function actualizarPosicionesBanner(Request $request){
        try {
            $banners = $request['banners'];
            for ($i=0; $i < count($banners); $i++) { 
                $banner = Webbannerconfiguracione::find($banners[$i]['id']);
                $banner->posicion = ($i+1);
                $banner->save();
            }
            return response()->json('Todo correcto', 200);   
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminarBanner(Request $request){
        try {
            $banner = Webbannerconfiguracione::find($request['id']);
            $banner->delete();
            $banners = Webbannerconfiguracione::where('idConfiguracion', '=', $request['idConfiguracion'])->orderBy('posicion', 'ASC')->get();
            for ($i=0; $i < count($banners); $i++) { 
                $banner = Webbannerconfiguracione::find($banners[$i]['id']);
                $banner->posicion = ($i+1);
                $banner->save();
            }
            return response()->json('Todo correcto', 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    //Configuraciones de Titulo
    function guardarTitulo(Request $request){
        try {
            $existe = Webtituloconfiguracione::where('idConfiguracion', '=', $request['idConfiguracion'])->get();
            if(count($existe) > 0){
                $titulo = $existe[0];
                $titulo->texto = $request['texto'];
                $titulo->clase = $request['clase'];
                $titulo->save();
                return response()->json($titulo, 200);
            }else{
                $titulo = Webtituloconfiguracione::create([
                    'idConfiguracion' => $request['idConfiguracion'],
                    'texto' => $request['texto'],
                    'clase' => $request['clase'],
                    'activo' => 1,
                    'eliminado' => 0
                ]);
                return response()->json($titulo, 200);
            }
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function traerTitulo(Request $request){
        try {
            $titulo = Webtituloconfiguracione::where('idConfiguracion', '=', $request['idConfiguracion'])->get();
            return response()->json((count($titulo) > 0) ? $titulo[0]->texto : '', 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    //Configuraciones de Video
    function guardarVideo(Request $request){
        try {
            $final = str_replace("https://www.youtube.com/watch?v=", "", $request['video']);
            $final = explode("&", $final);
            $existe = Webvideoconfiguracione::where('idConfiguracion', '=', $request['idConfiguracion'])->get();
            if(count($existe) > 0){
                $video = $existe[0];
                $video->texto = $request['texto'];
                $video->idPagina = $request['idPagina'];
                $video->video = $final[0];
                $video->titulo = $request['titulo'];
                $video->save();
                return response()->json($video, 200);
            }else{
                $video = Webvideoconfiguracione::create([
                    'idConfiguracion' => $request['idConfiguracion'],
                    'titulo' => $request['titulo'],
                    'texto' => $request['texto'],
                    'video' => $final[0],
                    'idPagina' => $request['idPagina'],
                    'activo' => 1,
                    'eliminado' => 0
                ]);
                return response()->json($video, 200);
            }
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function traerVideo(Request $request){
        try {
            $video = Webvideoconfiguracione::where('idConfiguracion', '=', $request['idConfiguracion'])->get();
            return response()->json((count($video) > 0) ? $video[0] : null, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    //Configuracion de Curso
    function guardarConfiguracionCurso(Request $request){
        try {
            $existe = Webcursoconfiguracione::where('idAltaCurso', '=', $request['idAltaCurso'])->get();
            if(count($existe) > 0){
                $configuracion = $existe[0];
                $configuracion->liga = $request['liga'];
                $configuracion->descripcion = $request['descripcion'];
                $configuracion->descuento = $request['descuento'];
                $configuracion->semanas = $request['semanas'];
                $configuracion->horas = $request['horas'];
                $configuracion->banner = $request['banner'];
                $configuracion->save();
                return response()->json($configuracion, 200);
            }else{
                $configuracion = Webcursoconfiguracione::create([
                    'liga' => $request['liga'],
                    'idAltaCurso' => $request['idAltaCurso'],
                    'descripcion' => $request['descripcion'],
                    'descuento' => $request['descuento'],
                    'semanas' => $request['semanas'],
                    'horas' => $request['horas'],
                    'banner' => $request['banner'],
                    'eliminado' => 0,
                    'activo' => 1
                ]);
                return response()->json($configuracion, 200);
            }
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function traerConfiguracionCurso(Request $request){
        try {
            $configuracion = Webcursoconfiguracione::where('idAltaCurso', '=', $request['idAltaCurso'])->get();
            return response()->json((count($configuracion) > 0) ? $configuracion[0] : null);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    //Beneficios del Curso
    function guardarBeneficioCurso(Request $request){
        try {
            $beneficio = Webcursobeneficio::create([
                'beneficio' => $request['beneficio'],
                'idAltaCurso' => $request['idAltaCurso'],
                'eliminado' => 0,
                'activo' => 1
            ]);
            return response()->json($beneficio, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function traerBeneficiosCurso(Request $request){
        try {
            $beneficios = Webcursobeneficio::where('idAltaCurso', '=', $request['idAltaCurso'])->get();
            return response()->json($beneficios, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminarBeneficioCurso(Request $request){
        try {
            $beneficio = Webcursobeneficio::find($request['id']);
            $beneficio->delete();
            return response()->json($beneficio, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    //Extras del Curso
    function guardarExtraCurso(Request $request){
        try {
            $beneficio = Webcursoextra::create([
                'beneficio' => $request['beneficio'],
                'idAltaCurso' => $request['idAltaCurso'],
                'eliminado' => 0,
                'activo' => 1
            ]);
            return response()->json($beneficio, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function traerExtrasCurso(Request $request){
        try {
            $beneficios = Webcursoextra::where('idAltaCurso', '=', $request['idAltaCurso'])->get();
            return response()->json($beneficios, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminarExtraCurso(Request $request){
        try {
            $beneficio = Webcursoextra::find($request['id']);
            $beneficio->delete();
            return response()->json($beneficio, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    //Configuraciones de Altas de Cursos
    function traerCursos(Request $request){
        try {
            $configuracion = $request['idConfiguracion'];
            $consulta = "SELECT n.nombre as nivel, s.nombre as subnivel, m.nombre as modalidad, ca.nombre as calendario, c.nombre as curso, i.*, 
                        cat.nombre as categoria, se.nombre as sede, c.icono as icono 
                        FROM niveles n, subniveles s, modalidades m, calendarios ca, cursos c, altacursos i,
                        categorias cat, sedes se, webaltasconfiguraciones w 
                        WHERE i.eliminado = 0 AND i.idModalidad = m.id AND i.idNivel = n.id AND i.idSubnivel = s.id AND i.idCalendario = ca.id 
                        AND i.idCurso = c.id AND i.idCategoria = cat.id AND i.idSede = se.id AND w.idAltaCurso = i.id AND w.idConfiguracion = $configuracion";
            $respuesta = DB::select($consulta, array());
            $res = array();
            $res['cursos'] = $respuesta;
            return response()->json($res, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function guardarCursos(Request $request){
        try {
            $lista = $request['lista'];
            $configuracion = $request['idConfiguracion'];
            DB::table('webaltasconfiguraciones')->where('idConfiguracion', '=', $configuracion)->delete();
            $posicion = 1;
            foreach ($lista as $registro) {
                $curso = Webaltasconfiguracione::create([
                    'idConfiguracion' => $configuracion,
                    'idAltaCurso' => $registro['id'],
                    'posicion' => $posicion,
                    'eliminado' => 0,
                    'activo' => 1
                ]);
                $posicion++;
            }
            return response()->json('Cursos cargados correctamente', 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    //Configuracion de Vigencia
    function guardarVigencia(Request $request){
        try {
            $existe = Webvigencia::all();
            if(count($existe) > 0){
                $vigencia = $existe[0];
                $vigencia->vigencia = $request['vigencia'];
                $vigencia->save();
                return response()->json($vigencia, 200);
            }else{
                $vigencia = Webvigencia::create([
                    'vigencia' => $request['vigencia'],
                    'eliminado' => 0,
                    'activo' => 1
                ]);
                return response()->json($vigencia, 200);
            }
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function traerVigencia(Request $request){
        try {
            $existe = Webvigencia::all();
            return response()->json((count($existe) > 0) ? $existe[0] : null, 200); 
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    //Configuracion de Testimonios
    function traerTestimonios(Request $request){
        try {
            $testimonios = Webtestimoniosconfiguracione::where('idConfiguracion', '=', $request['idConfiguracion'])->get();
            return response()->json($testimonios, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function guardarTestimonio(Request $request){
        try {
            $testimonio = Webtestimoniosconfiguracione::create([
                'idConfiguracion' => $request['idConfiguracion'],
                'nombre' => $request['nombre'],
                'imagen' => $request['imagen'],
                'indice' => $request['indice'],
                'calificacion' => $request['calificacion'],
                'contenido' => $request['contenido'],
                'activo' => 1,
                'eliminado' => 0
            ]);

            return response()->json($testimonio, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminarTestimonio(Request $request){
        try {
            $testimonio = Webtestimoniosconfiguracione::find($request['id']);
            $testimonio->delete();
            return response()->json($testimonio, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}