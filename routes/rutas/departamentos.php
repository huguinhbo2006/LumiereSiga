<?php
	$router->get('departamentos/mostrar', ['uses' => 'DepartamentosController@mostrar']);
	$router->get('departamentos/activos', ['uses' => 'DepartamentosController@activos']);
	$router->get('departamentos/activosEmpleados', ['uses' => 'DepartamentosController@activosEmpleados']);
	$router->post('departamentos/nuevo', ['uses' => 'DepartamentosController@nuevo']);
	$router->post('departamentos/activar', ['uses' => 'DepartamentosController@activar']);
	$router->post('departamentos/desactivar', ['uses' => 'DepartamentosController@desactivar']);
	$router->post('departamentos/eliminar', ['uses' => 'DepartamentosController@eliminar']);
	$router->post('departamentos/modificar', ['uses' => 'DepartamentosController@modificar']);
?>