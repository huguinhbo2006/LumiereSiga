<?php
	$router->post('examenes/nuevo', ['uses' => 'ExamenesController@nuevo']);
	$router->post('examenes/modificar', ['uses' => 'ExamenesController@modificar']);
	$router->post('examenes/eliminar', ['uses' => 'ExamenesController@eliminar']);
	$router->post('examenes/mostrar', ['uses' => 'ExamenesController@mostrar']);
	$router->get('examenes/selectores', ['uses' => 'ExamenesController@selectores']);
	$router->post('examenes/copiar', ['uses' => 'ExamenesController@copiar']);
?>