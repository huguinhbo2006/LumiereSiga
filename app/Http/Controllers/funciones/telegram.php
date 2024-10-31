<?php

	function facturacion($cargo, $fiscales){
		$key = "bot1745152888:AAHjl-iaYtMSTZotXrTIZhCiVpDcXu8b3n0";
		$descripcion = '';
		if($fiscales->usoCFDI === 'P01')
			$descripcion = 'Por Definir';
		else if($fiscales->usoCFDI === 'G01')
			$descripcion = 'Adquisición de mercancias';
		else if($fiscales->usoCFDI === 'G03')
			$descripcion = 'Gastos en General';
		else if($fiscales->usoCFDI === 'D10')
			$descripcion = 'Pagos por servicios educativos (colegiaturas)';
		$monto = (floatval($cargo['monto']) * 100) / 116;
		$iva = (floatval($cargo['monto']) * 16) / 116;

		//$canal = "-578180186";
		$canal = '363760317';
		$mensaje = "Solicitud de facturacion\n\n".
		"Razon Social: ".$fiscales->razonSocial.
		"\nRFC: ".$fiscales->RFC.
		"\nDomicilio Fiscal: ".$fiscales->domicilio.
		"\nColonia: ".$fiscales->colonia.
		"\nCodigo Postal: ".$fiscales->codigoPostal.
		"\nTelefono: ".$fiscales->telefono.
		"\nConcepto: ".$cargo['concepto'].
		"\nUso de CFDI: ".$fiscales->usoCFDI.
		"\nDescripcion uso CFDI: ".$descripcion.
		"\nCorreo: ".$fiscales->correo.
		"\nMetodo de Pago: ".$cargo['valorMetodo'].
		"\nForma de Pago: ".$cargo['valorForma'].
		"\nMonto Inicial: $".number_format($monto, 2, '.', ',').
		"\nIVA: $".number_format($iva, 2, '.', ',').
		"\nMonto Total: $".number_format($cargo['monto'], 2, '.', ',').
		"\nSucursal: ".$fiscales->sucursal;
		$url = "https://api.telegram.org/" . $key . "/sendMessage?chat_id=" . $canal;
	    $url = $url . "&text=" . urlencode($mensaje);
		enviar($url);
		$canal = "5231936427";
		$url = "https://api.telegram.org/" . $key . "/sendMessage?chat_id=" . $canal;
	    $url = $url . "&text=" . urlencode($mensaje);
		enviar($url);
	}

	function errores($archivo, $linea, $error, $usuario, $url){
		$canal = '363760317';
		$key = 'bot1703711902:AAGEIoOxwUU_fOe4kx1QZC1_dMywKaPN9UE';
		$mensaje = "Archivo = ".$archivo.
			"\nLinea = ".$linea.
			"\nError = ".$error.
			"\nUsuario = ".$usuario.
			"\nURL = ".$url;
		$url = "https://api.telegram.org/" . $key . "/sendMessage?chat_id=" . $canal;
	    $url = $url . "&text=" . urlencode($mensaje);
		enviar($url);
	}

	function soporte($mensaje, $usuario){
		$canal = '363760317';
		$key = 'bot5185779857:AAE_t9usWfK_KtF0zUdhVxQFpr2aD_fck8k';
		$mensaje = $usuario."\n".$mensaje;
		$url = "https://api.telegram.org/" . $key . "/sendMessage?chat_id=" . $canal;
	    $url = $url . "&text=" . urlencode($mensaje);
		enviar($url);
	}

	function inscripcionTelegram($usuario, $asesor, $ficha, $subnivel){
		$canal = "363760317";
		$key = "bot5262220185:AAH3_GevDSicxKf7RQMsPRIpHOoVpNJsdUw";
		$mensaje = 'Asesor: '.$asesor."\n".
		'Usuario: '.$usuario."\n".
		'Subnivel: '.$subnivel."\n".
		$ficha;
		$url = "https://api.telegram.org/" . $key . "/sendMessage?chat_id=" . $canal;
	    $url = $url . "&text=" . urlencode($mensaje);
		enviar($url);
	}

	function erroresPagina($error, ){
		$canal = "363760317";
		$key = "bot5262220185:AAH3_GevDSicxKf7RQMsPRIpHOoVpNJsdUw";
		$mensaje = 'Error: '.$error;
		$url = "https://api.telegram.org/" . $key . "/sendMessage?chat_id=" . $canal;
	    $url = $url . "&text=" . urlencode($mensaje);
		enviar($url);
	}

	function enviar($url){
		$ch = curl_init();
        $optArray = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true
	    );
	    curl_setopt_array($ch, $optArray);
	    $result = curl_exec($ch);
	    curl_close($ch);
	}
?>