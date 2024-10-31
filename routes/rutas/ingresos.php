<?php
	$router->post('ingresos/nuevo', ['uses' => 'IngresosController@nuevo']);
	$router->post('ingresos/mostrar', ['uses' => 'IngresosController@mostrar']);
	$router->post('ingresos/modificar', ['uses' => 'IngresosController@modificar']);
	$router->post('ingresos/eliminar', ['uses' => 'IngresosController@eliminar']);
	$router->post('ingresos/buscar', ['uses' => 'IngresosController@buscar']);
	$router->post('ingresos/buscarGerentes', ['uses' => 'IngresosController@buscarGerentes']);
	$router->post('ingresos/actualizarVoucher', ['uses' => 'IngresosController@actualizarVoucher']);
	$router->post('ingresos/traerVoucher', ['uses' => 'IngresosController@traerVoucher']);
	$router->get('ingresos/mostrarSolicitudes', ['uses' => 'IngresosController@mostrarSolicitudes']);
	$router->post('ingresos/solicitarModificacion', ['uses' => 'IngresosController@solicitarModificacion']);
	$router->post('ingresos/aceptarModificacion', ['uses' => 'IngresosController@aceptarModificacion']);
	$router->post('ingresos/rechazarModificacion', ['uses' => 'IngresosController@rechazarModificacion']);
?>