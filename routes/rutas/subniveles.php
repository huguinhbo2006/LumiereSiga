<?php
	$router->get('subniveles/mostrar', ['uses' => 'SubnivelesController@mostrar']);
	$router->post('subniveles/nuevo', ['uses' => 'SubnivelesController@nuevo']);
	$router->post('subniveles/eliminar', ['uses' => 'SubnivelesController@eliminar']);
	$router->post('subniveles/activar', ['uses' => 'SubnivelesController@activar']);
	$router->post('subniveles/desactivar', ['uses' => 'SubnivelesController@desactivar']);
	$router->post('subniveles/modificar', ['uses' => 'SubnivelesController@modificar']);
?>