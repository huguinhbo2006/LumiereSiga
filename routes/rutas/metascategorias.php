<?php
	$router->post('metascategorias/nuevo', ['uses' => 'MetascategoriasController@nuevo']);
	$router->post('metascategorias/modificar', ['uses' => 'MetascategoriasController@modificar']);
	$router->post('metascategorias/eliminar', ['uses' => 'MetascategoriasController@eliminar']);
	$router->post('metascategorias/mostrar', ['uses' => 'MetascategoriasController@mostrar']);
?>