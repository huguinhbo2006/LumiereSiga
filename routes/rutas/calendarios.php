<?php
	$router->get('calendarios/mostrar', ['uses' => 'CalendariosController@mostrar']);
	$router->post('calendarios/nuevo', ['uses' => 'CalendariosController@nuevo']);
	$router->post('calendarios/activar', ['uses' => 'CalendariosController@activar']);
	$router->post('calendarios/desactivar', ['uses' => 'CalendariosController@desactivar']);
	$router->post('calendarios/eliminar', ['uses' => 'CalendariosController@eliminar']);
	$router->post('calendarios/modificar', ['uses' => 'CalendariosController@modificar']);
?>