<?php
	$router->get('vales/recibidos', ['uses' => 'ValesController@recibidos']);
	$router->post('vales/aceptar', ['uses' => 'ValesController@aceptar']);
	$router->post('vales/nuevo', ['uses' => 'ValesController@nuevo']);
	$router->post('vales/eliminar', ['uses' => 'ValesController@eliminar']);
	$router->post('vales/modificar', ['uses' => 'ValesController@modificar']);
	$router->post('vales/mostrarCreados', ['uses' => 'ValesController@mostrarCreados']);
	$router->post('vales/buscar', ['uses' => 'ValesController@buscar']);
?>