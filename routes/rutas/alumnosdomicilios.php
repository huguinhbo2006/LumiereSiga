<?php
	$router->post('alumnosdomicilios/buscar', ['uses' => 'AlumnosdomiciliosController@buscar']);
	$router->post('alumnosdomicilios/traer', ['uses' => 'AlumnosdomiciliosController@traer']);
	$router->post('alumnosdomicilios/nuevo', ['uses' => 'AlumnosdomiciliosController@nuevo']);
	$router->post('alumnosdomicilios/activar', ['uses' => 'AlumnosdomiciliosController@activar']);
	$router->post('alumnosdomicilios/desactivar', ['uses' => 'AlumnosdomiciliosController@desactivar']);
	$router->post('alumnosdomicilios/eliminar', ['uses' => 'AlumnosdomiciliosController@eliminar']);
	$router->post('alumnosdomicilios/modificar', ['uses' => 'AlumnosdomiciliosController@modificar']);
	$router->post('alumnosdomicilios/mostrar', ['uses' => 'AlumnosdomiciliosController@mostrar']);
	$router->post('alumnosdomicilios/activos', ['uses' => 'AlumnosdomiciliosController@activos']);
	$router->post('alumnosdomicilios/primer', ['uses' => 'AlumnosdomiciliosController@primer']);
?>