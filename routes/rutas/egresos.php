<?php
	$router->post('egresos/nuevo', ['uses' => 'EgresosController@nuevo']);
	$router->post('egresos/mostrar', ['uses' => 'EgresosController@mostrar']);
	$router->post('egresos/modificar', ['uses' => 'EgresosController@modificar']);
	$router->post('egresos/eliminar', ['uses' => 'EgresosController@eliminar']);
	$router->post('egresos/buscar', ['uses' => 'EgresosController@buscar']);
	$router->post('egresos/actualizarVoucher', ['uses' => 'EgresosController@actualizarVoucher']);
	$router->post('egresos/traerVoucher', ['uses' => 'EgresosController@traerVoucher']);
	$router->post('egresos/actualizarFecha', ['uses' => 'EgresosController@actualizarFecha']);

	$router->post('egresos/buscarGerentes', ['uses' => 'EgresosController@buscarGerentes']);
	$router->post('egresos/traerComprobante', ['uses' => 'EgresosController@traerComprobante']);
	$router->post('egresos/actualizarComprobante', ['uses' => 'EgresosController@actualizarComprobante']);
	$router->post('egresos/solicitarModificacion', ['uses' => 'EgresosController@solicitarModificacion']);
	$router->get('egresos/mostrarSolicitudes', ['uses' => 'EgresosController@mostrarSolicitudes']);
	$router->post('egresos/aceptarModificacion', ['uses' => 'EgresosController@aceptarModificacion']);
	$router->post('egresos/rechazarModificacion', ['uses' => 'EgresosController@rechazarModificacion']);
?>