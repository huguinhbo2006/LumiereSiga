<?php
	$router->get('escuelas/mostrar', ['uses' => 'EscuelasController@mostrar']);
	$router->post('escuelas/nuevo', ['uses' => 'EscuelasController@nuevo']);
	$router->post('escuelas/eliminar', ['uses' => 'EscuelasController@eliminar']);
	$router->post('escuelas/modificar', ['uses' => 'EscuelasController@modificar']);
	$router->post('escuelas/activar', ['uses' => 'EscuelasController@activar']);
	$router->post('escuelas/desactivar', ['uses' => 'EscuelasController@desactivar']);
?>