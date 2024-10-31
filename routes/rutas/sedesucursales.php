<?php  
	$router->get('sedeSucursales/mostrar', ['uses' => 'SedesucursalesController@mostrar']);
	$router->post('sedeSucursales/nuevo', ['uses' => 'SedesucursalesController@nuevo']);
	$router->post('sedeSucursales/eliminar', ['uses' => 'SedesucursalesController@eliminar']);
?>