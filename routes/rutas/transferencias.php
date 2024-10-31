<?php
	$router->post('transferencias/nuevo', ['uses' => 'TransferenciasController@nuevo']);
	$router->post('transferencias/eliminar', ['uses' => 'TransferenciasController@eliminar']);
	$router->post('transferencias/modificar', ['uses' => 'TransferenciasController@modificar']);
	$router->post('transferencias/aceptar', ['uses' => 'TransferenciasController@aceptar']);
	$router->post('transferencias/rechazar', ['uses' => 'TransferenciasController@rechazar']);
	$router->post('transferencias/creados', ['uses' => 'TransferenciasController@creados']);
	$router->post('transferencias/recibidos', ['uses' => 'TransferenciasController@recibidos']);
?>