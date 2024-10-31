<?php
	$router->get('tipoObjetos/mostrar', ['uses' => 'TipoobjetosController@mostrar']);
	$router->post('tipoObjetos/nuevo', ['uses' => 'TipoobjetosController@nuevo']);
	$router->post('tipoObjetos/eliminar', ['uses' => 'TipoobjetosController@eliminar']);
	$router->post('tipoObjetos/modificar', ['uses' => 'TipoobjetosController@modificar']);
	$router->post('tipoObjetos/activar', ['uses' => 'TipoobjetosController@activar']);
	$router->post('tipoObjetos/desactivar', ['uses' => 'TipoobjetosController@desactivar']);
?>