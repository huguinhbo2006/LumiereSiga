<?php
	$router->get('rubros/mostrar', ['uses' => 'RubrosController@mostrar']);
	$router->post('rubros/nuevo', ['uses' => 'RubrosController@nuevo']);
	$router->post('rubros/activar', ['uses' => 'RubrosController@activar']);
	$router->post('rubros/desactivar', ['uses' => 'RubrosController@desactivar']);
	$router->post('rubros/eliminar', ['uses' => 'RubrosController@eliminar']);
	$router->post('rubros/modificar', ['uses' => 'RubrosController@modificar']);
?>