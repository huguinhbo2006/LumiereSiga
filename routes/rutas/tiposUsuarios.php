<?php
	$router->post('tiposUsuario/nuevo', ['uses' => 'TiposusuarioController@nuevo']);
	$router->get('tiposUsuario/mostrar', ['uses' => 'TiposusuarioController@mostrar']);
	$router->post('tiposUsuario/modificar', ['uses' => 'TiposusuarioController@modificar']);
	$router->post('tiposUsuario/activar', ['uses' => 'TiposusuarioController@activar']);
	$router->post('tiposUsuario/desactivar', ['uses' => 'TiposusuarioController@desactivar']);
	$router->post('tiposUsuario/eliminar', ['uses' => 'TiposusuarioController@eliminar']);
?>