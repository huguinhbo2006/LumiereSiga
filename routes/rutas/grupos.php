<?php
	$router->post('grupos/nuevo', ['uses' => 'GruposController@nuevo']);
	$router->post('grupos/mostrar', ['uses' => 'GruposController@mostrar']);
	$router->post('grupos/eliminar', ['uses' => 'GruposController@eliminar']);
	$router->post('grupos/obtener', ['uses' => 'GruposController@obtener']);
?>