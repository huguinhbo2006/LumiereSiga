<?php
	$router->get('tiposIngresos/mostrar', ['uses' => 'TiposingresosController@mostrar']);
	$router->post('tiposIngresos/nuevo', ['uses' => 'TiposingresosController@nuevo']);
	$router->post('tiposIngresos/activar', ['uses' => 'TiposingresosController@activar']);
	$router->post('tiposIngresos/desactivar', ['uses' => 'TiposingresosController@desactivar']);
	$router->post('tiposIngresos/eliminar', ['uses' => 'TiposingresosController@eliminar']);
	$router->post('tiposIngresos/modificar', ['uses' => 'TiposingresosController@modificar']);
?>