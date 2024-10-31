<?php
	$router->get('opciones/mostrar', ['uses' => 'OpcionesController@mostrar']);
	$router->post('opciones/nuevo', ['uses' => 'OpcionesController@nuevo']);
	$router->post('opciones/modificar', ['uses' => 'OpcionesController@modificar']);
	$router->post('opciones/activar', ['uses' => 'OpcionesController@activar']);
	$router->post('opciones/desactivar', ['uses' => 'OpcionesController@desactivar']);
	$router->post('opciones/eliminar', ['uses' => 'OpcionesController@eliminar']);
?>