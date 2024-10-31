<?php
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
?>