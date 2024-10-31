<?php
	$router->get('tipoEscuelas/mostrar', ['uses' => 'TipoescuelasController@mostrar']);
	$router->post('tipoEscuelas/nuevo', ['uses' => 'TipoescuelasController@nuevo']);
	$router->post('tipoEscuelas/eliminar', ['uses' => 'TipoescuelasController@eliminar']);
	$router->post('tipoEscuelas/modificar', ['uses' => 'TipoescuelasController@modificar']);
	$router->post('tipoEscuelas/activar', ['uses' => 'TipoescuelasController@activar']);
	$router->post('tipoEscuelas/desactivar', ['uses' => 'TipoescuelasController@desactivar']);
?>