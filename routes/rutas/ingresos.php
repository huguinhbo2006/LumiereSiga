<?php
	$router->post('ingresos/nuevo', ['uses' => 'IngresosController@nuevo']);
	$router->post('ingresos/mostrar', ['uses' => 'IngresosController@mostrar']);
	$router->post('ingresos/modificar', ['uses' => 'IngresosController@modificar']);
	$router->post('ingresos/eliminar', ['uses' => 'IngresosController@eliminar']);
	$router->post('ingresos/buscar', ['uses' => 'IngresosController@buscar']);
	$router->post('ingresos/gerentes', ['uses' => 'IngresosController@gerentes']);
	$router->post('ingresos/cargar', ['uses' => 'IngresosController@cargar']);
	$router->post('ingresos/voucher', ['uses' => 'IngresosController@voucher']);
	$router->get('ingresos/solicitudes', ['uses' => 'IngresosController@solicitudes']);
	$router->post('ingresos/solicitar', ['uses' => 'IngresosController@solicitar']);
	$router->post('ingresos/aceptar', ['uses' => 'IngresosController@aceptar']);
	$router->post('ingresos/rechazar', ['uses' => 'IngresosController@rechazar']);
?>