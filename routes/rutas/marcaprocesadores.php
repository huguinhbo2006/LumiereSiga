<?php
	$router->get('marcaProcesadores/mostrar', ['uses' => 'MarcaprocesadoresController@mostrar']);
	$router->post('marcaProcesadores/nuevo', ['uses' => 'MarcaprocesadoresController@nuevo']);
	$router->post('marcaProcesadores/eliminar', ['uses' => 'MarcaprocesadoresController@eliminar']);
	$router->post('marcaProcesadores/modificar', ['uses' => 'MarcaprocesadoresController@modificar']);
	$router->post('marcaProcesadores/activar', ['uses' => 'MarcaprocesadoresController@activar']);
	$router->post('marcaProcesadores/desactivar', ['uses' => 'MarcaprocesadoresController@desactivar']);
?>