<?php
	$router->post('fichas/traerFicha', ['uses' => 'FichasController@traerFicha']);
	$router->get('fichas/catalogos', ['uses' => 'FichasController@catalogos']);
	$router->post('fichas/estadoCuenta', ['uses' => 'FichasController@estadoCuenta']);
	$router->post('fichas/agregarCargo', ['uses' => 'FichasController@agregarCargo']);
	$router->post('fichas/eliminarCargo', ['uses' => 'FichasController@eliminarCargo']);
	$router->post('fichas/agregarAbono', ['uses' => 'FichasController@agregarAbono']);
	$router->post('fichas/eliminarAbono', ['uses' => 'FichasController@eliminarAbono']);
	$router->post('fichas/agregarDescuento', ['uses' => 'FichasController@agregarDescuento']);
	$router->post('fichas/eliminarDescuento', ['uses' => 'FichasController@eliminarDescuento']);
	$router->post('fichas/agregarDevolucion', ['uses' => 'FichasController@agregarDevolucion']);
	$router->post('fichas/eliminarDevolucion', ['uses' => 'FichasController@eliminarDevolucion']);
	$router->post('fichas/agregarExtra', ['uses' => 'FichasController@agregarExtra']);
	$router->post('fichas/eliminarExtra', ['uses' => 'FichasController@eliminarExtra']);
	$router->post('fichas/agregarCupon', ['uses' => 'FichasController@agregarCupon']);
	$router->post('fichas/modificarFicha', ['uses' => 'FichasController@modificarFicha']);
	$router->post('fichas/modificarDatosAspiracion', ['uses' => 'FichasController@modificarDatosAspiracion']);
	$router->post('fichas/modificarTipoPago', ['uses' => 'FichasController@modificarTipoPago']);
	$router->post('fichas/modificarDatosPublicitarios', ['uses' => 'FichasController@modificarDatosPublicitarios']);
?>