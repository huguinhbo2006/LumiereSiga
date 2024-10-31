<?php
	$router->get('marcaComputadoras/mostrar', ['uses' => 'MarcacomputadorasController@mostrar']);
	$router->post('marcaComputadoras/nuevo', ['uses' => 'MarcacomputadorasController@nuevo']);
	$router->post('marcaComputadoras/eliminar', ['uses' => 'MarcacomputadorasController@eliminar']);
	$router->post('marcaComputadoras/modificar', ['uses' => 'MarcacomputadorasController@modificar']);
	$router->post('marcaComputadoras/activar', ['uses' => 'MarcacomputadorasController@activar']);
	$router->post('marcaComputadoras/desactivar', ['uses' => 'MarcacomputadorasController@desactivar']);
?>