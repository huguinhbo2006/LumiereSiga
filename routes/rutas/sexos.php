<?php
	$router->get('sexos/mostrar', ['uses' => 'SexosController@mostrar']);
	$router->post('sexos/nuevo', ['uses' => 'SexosController@nuevo']);
	$router->post('sexos/activar', ['uses' => 'SexosController@activar']);
	$router->post('sexos/desactivar', ['uses' => 'SexosController@desactivar']);
	$router->post('sexos/eliminar', ['uses' => 'SexosController@eliminar']);
	$router->post('sexos/modificar', ['uses' => 'SexosController@modificar']);
?>