<?php
	$router->get('altacursos/mostrar', ['uses' => 'AltacursosController@mostrar']);
	$router->post('altacursos/nuevo', ['uses' => 'AltacursosController@nuevo']);
	$router->post('altacursos/eliminar', ['uses' => 'AltacursosController@eliminar']);
	$router->post('altacursos/modificar', ['uses' => 'AltacursosController@modificar']);
?>