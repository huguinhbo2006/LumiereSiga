<?php
	$router->get('estados/mostrar', ['uses' => 'EstadosController@mostrar']);
	$router->post('estados/nuevo', ['uses' => 'EstadosController@nuevo']);
	$router->post('estados/activar', ['uses' => 'EstadosController@activar']);
	$router->post('estados/desactivar', ['uses' => 'EstadosController@desactivar']);
	$router->post('estados/eliminar', ['uses' => 'EstadosController@eliminar']);
	$router->post('estados/modificar', ['uses' => 'EstadosController@modificar']);
?>