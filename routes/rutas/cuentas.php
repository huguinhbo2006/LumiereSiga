<?php
	$router->get('cuentas/mostrar', ['uses' => 'CuentasController@mostrar']);
	$router->post('cuentas/nuevo', ['uses' => 'CuentasController@nuevo']);
	$router->post('cuentas/activar', ['uses' => 'CuentasController@activar']);
	$router->post('cuentas/desactivar', ['uses' => 'CuentasController@desactivar']);
	$router->post('cuentas/eliminar', ['uses' => 'CuentasController@eliminar']);
	$router->post('cuentas/modificar', ['uses' => 'CuentasController@modificar']);
?>