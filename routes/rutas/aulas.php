<?php
	$router->post('aulas/mostrar', ['uses' => 'AulasController@mostrar']);
	$router->post('aulas/activos', ['uses' => 'AulasController@activos']);
	$router->post('aulas/nuevo', ['uses' => 'AulasController@nuevo']);
	$router->post('aulas/activar', ['uses' => 'AulasController@activar']);
	$router->post('aulas/desactivar', ['uses' => 'AulasController@desactivar']);
	$router->post('aulas/eliminar', ['uses' => 'AulasController@eliminar']);
	$router->post('aulas/modificar', ['uses' => 'AulasController@modificar']);
?>