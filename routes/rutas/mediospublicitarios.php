<?php
	$router->get('mediosPublicitarios/mostrar', ['uses' => 'MediospublicitariosController@mostrar']);
	$router->post('mediosPublicitarios/nuevo', ['uses' => 'MediospublicitariosController@nuevo']);
	$router->post('mediosPublicitarios/activar', ['uses' => 'MediospublicitariosController@activar']);
	$router->post('mediosPublicitarios/desactivar', ['uses' => 'MediospublicitariosController@desactivar']);
	$router->post('mediosPublicitarios/eliminar', ['uses' => 'MediospublicitariosController@eliminar']);
	$router->post('mediosPublicitarios/modificar', ['uses' => 'MediospublicitariosController@modificar']);
?>