<?php
	$router->get('rubrosEgresos/mostrar', ['uses' => 'RubrosegresosController@mostrar']);
	$router->post('rubrosEgresos/nuevo', ['uses' => 'RubrosegresosController@nuevo']);
	$router->post('rubrosEgresos/activar', ['uses' => 'RubrosegresosController@activar']);
	$router->post('rubrosEgresos/desactivar', ['uses' => 'RubrosegresosController@desactivar']);
	$router->post('rubrosEgresos/eliminar', ['uses' => 'RubrosegresosController@eliminar']);
	$router->post('rubrosEgresos/modificar', ['uses' => 'RubrosegresosController@modificar']);
?>