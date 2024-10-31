<?php
	$router->post('carreras/nuevo', ['uses' => 'CarrerasController@nuevo']);
	$router->post('carreras/activar', ['uses' => 'CarrerasController@activar']);
	$router->post('carreras/desactivar', ['uses' => 'CarrerasController@desactivar']);
	$router->post('carreras/eliminar', ['uses' => 'CarrerasController@eliminar']);
	$router->post('carreras/modificar', ['uses' => 'CarrerasController@modificar']);
	$router->post('carreras/cargar', ['uses' => 'CarrerasController@cargar']);
	$router->get('carreras/mostrar', ['uses' => 'CarrerasController@mostrar']);
?>