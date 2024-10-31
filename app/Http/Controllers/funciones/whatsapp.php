<?php  
	function mensajeWA($numero, $mensaje){
		try {
			$url = 'https://whatsappwebhapc.ngrok.app/taqueria/mensaje';
		   	$data = array(
		       'telefono' => $numero,
		       'mensaje' => $mensaje,
		   	);
		   	$payload = json_encode($data);

		   	$ch = curl_init();
		   	$headers = array('Content-Type: application/json');
		   	curl_setopt($ch, CURLOPT_URL, $url);
		   	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		   	curl_setopt($ch, CURLOPT_POST, 1);
		   	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		   	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		   	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		   	$st=curl_exec($ch);
		   	return $st;	
		} catch (Exception $e) {
			return "Error al enviar whatsapp";
		}
	}

	function imagenWA($numero, $imagen, $mensaje){
		try {
			$url = 'https://whatsappwebhapc.ngrok.app/taqueria/imagen';
		   	$data = array(
		       'telefono' => $numero,
		       'imagen' => $imagen,
		       'mensaje' => $mensaje
		   	);
		   	$payload = json_encode($data);

		   	$ch = curl_init();
		   	$headers = array('Content-Type: application/json');
		   	curl_setopt($ch, CURLOPT_URL, $url);
		   	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		   	curl_setopt($ch, CURLOPT_POST, 1);
		   	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		   	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		   	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		   	$st=curl_exec($ch);
		   	return $st;	
		} catch (Exception $e) {
			return "Error al enviar whatsapp";
		}
	}
?>