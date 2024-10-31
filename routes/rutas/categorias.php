<?php
	$router->get('categorias/mostrar', ['uses' => 'CategoriasController@mostrar']);
	$router->post('categorias/nuevo', ['uses' => 'CategoriasController@nuevo']);
	$router->post('categorias/activar', ['uses' => 'CategoriasController@activar']);
	$router->post('categorias/desactivar', ['uses' => 'CategoriasController@desactivar']);
	$router->post('categorias/eliminar', ['uses' => 'CategoriasController@eliminar']);
	$router->post('categorias/modificar', ['uses' => 'CategoriasController@modificar']);
?>