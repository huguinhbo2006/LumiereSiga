<?php
	$router->get('sucursales/mostrar', ['uses' => 'SucursalesController@mostrar']);
	$router->post('sucursales/nuevo', ['uses' => 'SucursalesController@nuevo']);
	$router->post('sucursales/modificar', ['uses' => 'SucursalesController@modificar']);
	$router->post('sucursales/activar', ['uses' => 'SucursalesController@activar']);
	$router->post('sucursales/desactivar', ['uses' => 'SucursalesController@desactivar']);
	$router->post('sucursales/eliminar', ['uses' => 'SucursalesController@eliminar']);
?>