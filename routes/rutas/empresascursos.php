<?php
	$router->get('empresasCursos/mostrar', ['uses' => 'EmpresascursosController@mostrar']);
	$router->post('empresasCursos/nuevo', ['uses' => 'EmpresascursosController@nuevo']);
	$router->post('empresasCursos/activar', ['uses' => 'EmpresascursosController@activar']);
	$router->post('empresasCursos/desactivar', ['uses' => 'EmpresascursosController@desactivar']);
	$router->post('empresasCursos/eliminar', ['uses' => 'EmpresascursosController@eliminar']);
	$router->post('empresasCursos/modificar', ['uses' => 'EmpresascursosController@modificar']);
?>