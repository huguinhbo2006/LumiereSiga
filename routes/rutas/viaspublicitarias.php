<?php
	$router->get('viasPublicitarias/mostrar', ['uses' => 'ViaspublicitariasController@mostrar']);
	$router->post('viasPublicitarias/nuevo', ['uses' => 'ViaspublicitariasController@nuevo']);
	$router->post('viasPublicitarias/activar', ['uses' => 'ViaspublicitariasController@activar']);
	$router->post('viasPublicitarias/desactivar', ['uses' => 'ViaspublicitariasController@desactivar']);
	$router->post('viasPublicitarias/eliminar', ['uses' => 'ViaspublicitariasController@eliminar']);
	$router->post('viasPublicitarias/modificar', ['uses' => 'ViaspublicitariasController@modificar']);
?>