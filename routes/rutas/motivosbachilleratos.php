<?php
	$router->get('motivosBachilleratos/mostrar', ['uses' => 'MotivosbachilleratosController@mostrar']);
	$router->post('motivosBachilleratos/nuevo', ['uses' => 'MotivosbachilleratosController@nuevo']);
	$router->post('motivosBachilleratos/activar', ['uses' => 'MotivosbachilleratosController@activar']);
	$router->post('motivosBachilleratos/desactivar', ['uses' => 'MotivosbachilleratosController@desactivar']);
	$router->post('motivosBachilleratos/eliminar', ['uses' => 'MotivosbachilleratosController@eliminar']);
	$router->post('motivosBachilleratos/modificar', ['uses' => 'MotivosbachilleratosController@modificar']);
?>