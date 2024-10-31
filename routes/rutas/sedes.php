<?php
	$router->get('sedes/mostrar', ['uses' => 'SedesController@mostrar']);
	$router->post('sedes/nuevo', ['uses' => 'SedesController@nuevo']);
	$router->post('sedes/activar', ['uses' => 'SedesController@activar']);
	$router->post('sedes/desactivar', ['uses' => 'SedesController@desactivar']);
	$router->post('sedes/eliminar', ['uses' => 'SedesController@eliminar']);
	$router->post('sedes/modificar', ['uses' => 'SedesController@modificar']);
?>