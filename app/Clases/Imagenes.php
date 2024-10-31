<?php

	namespace App\Clases;
	use Carbon\Carbon;

	class Imagenes{
		function base64toImage($imagenEnBase64, $rutaImagenSalida){
			try {
				$imagenEnBase64 = explode(",", $imagenEnBase64, 2);
	            $imagenBinaria = base64_decode($imagenEnBase64[1]);
	            $bytes = file_put_contents($rutaImagenSalida, $imagenBinaria);
	            return true;
			} catch (Exception $e) {
				return null;
			}
		}

		function redimensionarImagenes($src, $ancho_forzado, $tipo){
			try {
				$rutaImagenSalida = "./final".$tipo;
				if (file_exists($src)) {
			      list($width, $height, $type, $attr)= getimagesize($src);
			      if ($ancho_forzado > $width) {
			         $max_width = $width;
			      } else {
			         $max_width = $ancho_forzado;
			      }
			      $proporcion = $width / $max_width;
			      if ($proporcion == 0) {
			         return -2;
			      }
			      $height_dyn = $height / $proporcion;
			   	} else {
			      return -1;
			   	}
			   	$image_resize = Image::make($src);
		        $image_resize->fit($max_width, $height_dyn);
		        $image_resize->save($rutaImagenSalida);

		        $contenidoBinario = file_get_contents($rutaImagenSalida);
				$imagenComoBase64 = base64_encode($contenidoBinario);
				
			   	return $imagenComoBase64;
			} catch (Exception $e) {
				return false;
			}
		}

		function esImagen($referencia){
			try {
				return (str_contains($referencia, 'jpg') || str_contains($referencia, 'jpeg') || str_contains($referencia, 'png'));
			} catch (Exception $e) {
				return null;
			}
		}

		function obtenerExtension($referencia){
			try {
				if(str_contains($referencia, 'jpg'))
					return ".jpg";
				else if(str_contains($referencia, 'jpeg'))
					return ".jpeg";
				else if(str_contains($referencia, 'png'))
					return ".png";
				else
					return 'desconocido';
			} catch (Exception $e) {
				return null;	
			}
		}

		function obtenerImagen($extension, $ruta){
			try {
				if($extension == ".jpg" || $extension == ".jpeg")  
	            	return imagecreatefromjpeg($ruta);  
	        	else if ($extension == ".png")  
	            	return imagecreatefrompng($ruta);
	            else
	            	return false;
			} catch (Exception $e) {
				return null;
			}
		}

		function obtenerAltoProporcional($alto, $ancho, $anchoProporcional){
			try {
				$referencia = floatval($anchoProporcional)/floatval($ancho);
				return ($referencia * floatval($alto));
			} catch (Exception $e) {
				return null;
			}
		}

		function crearImage($alto, $ancho, $altoNuevo, $anchoNuevo, $imagen, $extension){
			try {
				$imagen2 = imagecreatetruecolor($anchoNuevo, $altoNuevo);  
	        	imagecopyresized($imagen2, $imagen, 0, 0, 0, 0, floor($anchoNuevo), floor($altoNuevo), $ancho, $alto);

	        	if($extension === ".png")
	        		imagepng($imagen2, "imagen_optimizada.png");
	        	else if($extension === ".jpg" || $extension === ".jpeg")
	        		imagejpeg($imagen2, "imagen_optimizada.jpg");	
	        	
	        	return true;
			} catch (Exception $e) {
				return null;
			}
		}

		function obtenerBase64($extension){
			try {
				$ruta = "";
				if($extension === ".png")
					$ruta = "imagen_optimizada.png";
				else if($extension === ".jpg" || $extension === ".jpeg")
					$ruta = "imagen_optimizada.jpg";
				$contenidoBinario = file_get_contents($ruta);
				return base64_encode($contenidoBinario);
			} catch (Exception $e) {
				return null;
			}
		}

		function redimensionarImagen($base64Imagen){
			try {
				$anchoProporcional = env('ANCHO_IMAGENES', '0');
				$separadas = explode(";", $base64Imagen);

				$extension = $this->obtenerExtension($separadas[0]);
	            $this->base64toImage($base64Imagen, "imagen".$extension);
	            $imagen = $this->obtenerImagen($extension, "imagen".$extension);
	            $ancho = $this->imagesx($imagen);
	            $alto = $this->imagesy($imagen);
	            $altoProporcional = ($ancho > $anchoProporcional) ? $this->obtenerAltoProporcional($alto, $ancho, $anchoProporcional) : $alto;
	            $creo = $this->crearImage($alto, $ancho, $altoProporcional, $anchoProporcional, $imagen, $extension);
	            $base64 = ($creo) ? $separadas[0].";base64,".$this->obtenerBase64($extension) : $base64Imagen;
	            return $base64;
			} catch (Exception $e) {
				return null;
			}
		}
	}

	
?>