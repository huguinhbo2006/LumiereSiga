<?php
	$router->get('campanias/mostrar', ['uses' => 'CampaniasController@mostrar']);
	$router->post('campanias/nuevo', ['uses' => 'CampaniasController@nuevo']);
	$router->post('campanias/activar', ['uses' => 'CampaniasController@activar']);
	$router->post('campanias/desactivar', ['uses' => 'CampaniasController@desactivar']);
	$router->post('campanias/eliminar', ['uses' => 'CampaniasController@eliminar']);
	$router->post('campanias/modificar', ['uses' => 'CampaniasController@modificar']);
?>