<?php
	$router->get('universidades/mostrar', ['uses' => 'UniversidadesController@mostrar']);
	$router->post('universidades/nuevo', ['uses' => 'UniversidadesController@nuevo']);
	$router->post('universidades/activar', ['uses' => 'UniversidadesController@activar']);
	$router->post('universidades/desactivar', ['uses' => 'UniversidadesController@desactivar']);
	$router->post('universidades/eliminar', ['uses' => 'UniversidadesController@eliminar']);
	$router->post('universidades/modificar', ['uses' => 'UniversidadesController@modificar']);
?>