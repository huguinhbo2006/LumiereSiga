<?php
	$router->get('paridades/mostrar', ['uses' => 'ParidadesController@mostrar']);
	$router->post('paridades/nuevo', ['uses' => 'ParidadesController@nuevo']);
	$router->post('paridades/activar', ['uses' => 'ParidadesController@activar']);
	$router->post('paridades/desactivar', ['uses' => 'ParidadesController@desactivar']);
	$router->post('paridades/eliminar', ['uses' => 'ParidadesController@eliminar']);
	$router->post('paridades/modificar', ['uses' => 'ParidadesController@modificar']);
?>	