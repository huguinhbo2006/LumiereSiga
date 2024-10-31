<?php
	$router->get('niveles/mostrar', ['uses' => 'NivelesController@mostrar']);
	$router->post('niveles/nuevo', ['uses' => 'NivelesController@nuevo']);
	$router->post('niveles/activar', ['uses' => 'NivelesController@activar']);
	$router->post('niveles/desactivar', ['uses' => 'NivelesController@desactivar']);
	$router->post('niveles/eliminar', ['uses' => 'NivelesController@eliminar']);
	$router->post('niveles/modificar', ['uses' => 'NivelesController@modificar']);
?>