<?php
	$router->post('metasCursos/mostrar', ['uses' => 'MetascursosController@mostrar']);
	$router->post('metasCursos/nuevo', ['uses' => 'MetascursosController@nuevo']);
	$router->post('metasCursos/modificar', ['uses' => 'MetascursosController@modificar']);
	$router->post('metasCursos/eliminar', ['uses' => 'MetascursosController@eliminar']);
?>