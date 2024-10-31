<?php
	$router->get('tiposEgresos/mostrar', ['uses' => 'TiposegresosController@mostrar']);
	$router->post('tiposEgresos/activos', ['uses' => 'TiposegresosController@activos']);
	$router->post('tiposEgresos/nuevo', ['uses' => 'TiposegresosController@nuevo']);
	$router->post('tiposEgresos/activar', ['uses' => 'TiposegresosController@activar']);
	$router->post('tiposEgresos/desactivar', ['uses' => 'TiposegresosController@desactivar']);
	$router->post('tiposEgresos/eliminar', ['uses' => 'TiposegresosController@eliminar']);
	$router->post('tiposEgresos/modificar', ['uses' => 'TiposegresosController@modificar']);
?>