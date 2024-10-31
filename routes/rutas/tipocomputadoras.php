<?php
	$router->get('tipoComputadoras/mostrar', ['uses' => 'TipocomputadorasController@mostrar']);
	$router->post('tipoComputadoras/nuevo', ['uses' => 'TipocomputadorasController@nuevo']);
	$router->post('tipoComputadoras/eliminar', ['uses' => 'TipocomputadorasController@eliminar']);
	$router->post('tipoComputadoras/modificar', ['uses' => 'TipocomputadorasController@modificar']);
	$router->post('tipoComputadoras/activar', ['uses' => 'TipocomputadorasController@activar']);
	$router->post('tipoComputadoras/desactivar', ['uses' => 'TipocomputadorasController@desactivar']);
?>