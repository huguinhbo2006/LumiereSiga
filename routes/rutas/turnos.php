<?php
	$router->get('turnos/mostrar', ['uses' => 'TurnosController@mostrar']);
	$router->post('turnos/nuevo', ['uses' => 'TurnosController@nuevo']);
	$router->post('turnos/activar', ['uses' => 'TurnosController@activar']);
	$router->post('turnos/desactivar', ['uses' => 'TurnosController@desactivar']);
	$router->post('turnos/eliminar', ['uses' => 'TurnosController@eliminar']);
	$router->post('turnos/modificar', ['uses' => 'TurnosController@modificar']);
?>