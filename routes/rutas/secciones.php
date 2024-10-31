<?php
	$router->post('secciones/nuevo', ['uses' => 'SeccionesController@nuevo']);
	$router->post('secciones/mostrar', ['uses' => 'SeccionesController@mostrar']);
	$router->post('secciones/activar', ['uses' => 'SeccionesController@activar']);
	$router->post('secciones/desactivar', ['uses' => 'SeccionesController@desactivar']);
	$router->post('secciones/eliminar', ['uses' => 'SeccionesController@eliminar']);
	$router->post('secciones/modificar', ['uses' => 'SeccionesController@modificar']);
?>