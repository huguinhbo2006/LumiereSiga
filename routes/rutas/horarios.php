<?php
	$router->post('horarios/nuevo', ['uses' => 'HorariosController@nuevo']);
	$router->get('horarios/mostrar', ['uses' => 'HorariosController@mostrar']);
	$router->post('horarios/activar', ['uses' => 'HorariosController@activar']);
	$router->post('horarios/desactivar', ['uses' => 'HorariosController@desactivar']);
	$router->post('horarios/eliminar', ['uses' => 'HorariosController@eliminar']);
	$router->post('horarios/modificar', ['uses' => 'HorariosController@modificar']);
?>