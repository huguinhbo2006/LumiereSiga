<?php
	$router->get('formasPagos/mostrar', ['uses' => 'FormaspagosController@mostrar']);
	$router->post('formasPagos/nuevo', ['uses' => 'FormaspagosController@nuevo']);
	$router->post('formasPagos/activar', ['uses' => 'FormaspagosController@activar']);
	$router->post('formasPagos/desactivar', ['uses' => 'FormaspagosController@desactivar']);
	$router->post('formasPagos/eliminar', ['uses' => 'FormaspagosController@eliminar']);
	$router->post('formasPagos/modificar', ['uses' => 'FormaspagosController@modificar']);
?>