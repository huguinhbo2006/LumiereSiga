<?php
	$router->post('cursos/nuevo', ['uses' => 'CursosController@nuevo']);
	$router->get('cursos/mostrar', ['uses' => 'CursosController@mostrar']);
	$router->post('cursos/activar', ['uses' => 'CursosController@activar']);
	$router->post('cursos/desactivar', ['uses' => 'CursosController@desactivar']);
	$router->post('cursos/eliminar', ['uses' => 'CursosController@eliminar']);
	$router->post('cursos/modificar', ['uses' => 'CursosController@modificar']);
?>