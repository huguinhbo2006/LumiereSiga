<?php
	$router->get('profesores/mostrar', ['uses' => 'ProfesoresController@mostrar']);
	$router->post('profesores/nuevo', ['uses' => 'ProfesoresController@nuevo']);
	$router->post('profesores/modificar', ['uses' => 'ProfesoresController@modificar']);
	$router->post('profesores/activar', ['uses' => 'ProfesoresController@activar']);
	$router->post('profesores/desactivar', ['uses' => 'ProfesoresController@desactivar']);
	$router->post('profesores/eliminar', ['uses' => 'ProfesoresController@eliminar']);
?>