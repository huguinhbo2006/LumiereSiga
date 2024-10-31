<?php
	$router->post('valesgerenciales/nuevo', ['uses' => 'ValesgerencialesController@nuevo']);
	$router->post('valesgerenciales/eliminar', ['uses' => 'ValesgerencialesController@eliminar']);
	$router->post('valesgerenciales/modificar', ['uses' => 'ValesgerencialesController@modificar']);
	$router->post('valesgerenciales/aceptar', ['uses' => 'ValesgerencialesController@aceptar']);
	$router->post('valesgerenciales/mostrar', ['uses' => 'ValesgerencialesController@mostrar']);
	$router->post('valesgerenciales/buscar', ['uses' => 'ValesgerencialesController@buscar']);
	$router->post('valesgerenciales/aceptarModificacion', ['uses' => 'ValesgerencialesController@aceptarModificacion']);
	$router->post('valesgerenciales/rechazarModificacion', ['uses' => 'ValesgerencialesController@rechazarModificacion']);

?>