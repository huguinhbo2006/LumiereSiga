<?php
	$router->post('seccionPaginas/nuevo', ['uses' => 'SeccionpaginasController@nuevo']);
	$router->post('seccionPaginas/mostrar', ['uses' => 'SeccionpaginasController@mostrar']);
	$router->post('seccionPaginas/modificar', ['uses' => 'SeccionpaginasController@modificar']);
	$router->post('seccionPaginas/eliminar', ['uses' => 'SeccionpaginasController@eliminar']);
?>