<?php
	$router->post('modulos/nuevo', ['uses' => 'ModulosController@nuevo']);
	$router->get('modulos/mostrar', ['uses' => 'ModulosController@traer']);
	$router->post('modulos/activar', ['uses' => 'ModulosController@activar']);
	$router->post('modulos/desactivar', ['uses' => 'ModulosController@desactivar']);
	$router->post('modulos/eliminar', ['uses' => 'ModulosController@eliminar']);
	$router->post('modulos/modificar', ['uses' => 'ModulosController@modificar']);
?>