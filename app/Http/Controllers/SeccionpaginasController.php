<?php

namespace App\Http\Controllers;
use App\Seccionpagina;
use App\Seccione;
include "logs.php";

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeccionpaginasController extends BaseController
{
    function nuevo(Request $request){
        try{
            $base = explode(",", $request['imagen']);
            $data = base64_decode($base[1]);
            $filepath = "paginas/image.jpg";
            file_put_contents($filepath, $data);

            $image_name =  "paginas/image.jpg";
            $image = imagecreatefromjpeg($image_name);
            $img = imagescale( $image, 1787, 2205 );
            imagejpeg($img, "paginas/optimizada.jpg");

            $type = pathinfo("paginas/optimizada.jpg", PATHINFO_EXTENSION);
            $data = file_get_contents("paginas/optimizada.jpg");
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data); 

            unlink("paginas/image.jpg");
            unlink("paginas/optimizada.jpg");

            $pagina = Seccionpagina::create([
                'idSeccion' => $request['idSeccion'],
                'posicion' => $request['posicion'],
                'nombre' => $request['nombre'],
                'imagen' => $base64,
                'activo' => 1,
                'eliminado' => 0
            ]);
            return response()->json($request, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function mostrar(Request $request) {
        try{
            $resultado = Seccionpagina::where('idSeccion', '=', $request['seccion'])->where('eliminado', '=', 0)->get();
            return response()->json($resultado, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request) {
        try{
            $pagina = Seccionpagina::find($request['id']);
            $pagina->nombre = $request['nombre'];
            $pagina->posicion = $request['posicion'];
            $pagina->imagen = $request['imagen'];
            $pagina->save();
            return response()->json($pagina, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request) {
        try{
            $pagina = Seccionpagina::find($request['id']);
            $pagina->eliminado = 1;
            $pagina->save();
            return response()->json($pagina, 200);
        }catch(Exception $e){
            return response()->json('Error en el servidor', 400);
        }
    }
}