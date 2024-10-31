<?php
	$router->get('metodosPagos/mostrar', ['uses' => 'MetodospagosController@mostrar']);
	$router->post('metodosPagos/nuevo', ['uses' => 'MetodospagosController@nuevo']);
	$router->post('metodosPagos/activar', ['uses' => 'MetodospagosController@activar']);
	$router->post('metodosPagos/desactivar', ['uses' => 'MetodospagosController@desactivar']);
	$router->post('metodosPagos/eliminar', ['uses' => 'MetodospagosController@eliminar']);
	$router->post('metodosPagos/modificar', ['uses' => 'MetodospagosController@modificar']);
?>