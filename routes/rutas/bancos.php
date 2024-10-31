<?php
	$router->get('bancos/mostrar', ['uses' => 'BancosController@mostrar']);
	$router->post('bancos/nuevo', ['uses' => 'BancosController@nuevo']);
	$router->post('bancos/activar', ['uses' => 'BancosController@activar']);
	$router->post('bancos/desactivar', ['uses' => 'BancosController@desactivar']);
	$router->post('bancos/eliminar', ['uses' => 'BancosController@eliminar']);
	$router->post('bancos/modificar', ['uses' => 'BancosController@modificar']);
?>