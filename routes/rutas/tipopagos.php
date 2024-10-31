<?php
	$router->get('tipoPagos/mostrar', ['uses' => 'TipopagosController@mostrar']);
	$router->post('tipoPagos/nuevo', ['uses' => 'TipopagosController@nuevo']);
	$router->post('tipoPagos/eliminar', ['uses' => 'TipopagosController@eliminar']);
	$router->post('tipoPagos/modificar', ['uses' => 'TipopagosController@modificar']);
	$router->post('tipoPagos/activar', ['uses' => 'TipopagosController@activar']);
	$router->post('tipoPagos/desactivar', ['uses' => 'TipopagosController@desactivar']);
?>