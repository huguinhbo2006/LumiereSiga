<?php
	$router->get('puestos/mostrar', ['uses' => 'PuestosController@mostrar']);
	$router->post('puestos/activos', ['uses' => 'PuestosController@activos']);
	$router->post('puestos/nuevo', ['uses' => 'PuestosController@nuevo']);
	$router->post('puestos/activar', ['uses' => 'PuestosController@activar']);
	$router->post('puestos/desactivar', ['uses' => 'PuestosController@desactivar']);
	$router->post('puestos/eliminar', ['uses' => 'PuestosController@eliminar']);
	$router->post('puestos/modificar', ['uses' => 'PuestosController@modificar']);
?>