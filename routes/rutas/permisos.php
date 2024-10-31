<?php
	$router->post('permisos/mostrar', ['uses' => 'PermisosController@mostrar']);
	$router->post('permisos/activarModulo', ['uses' => 'PermisosController@activarModulo']);
	$router->post('permisos/desactivarModulo', ['uses' => 'PermisosController@desactivarModulo']);
	$router->post('permisos/activarOpcion', ['uses' => 'PermisosController@activarOpcion']);
	$router->post('permisos/desactivarOpcion', ['uses' => 'PermisosController@desactivarOpcion']);
?>