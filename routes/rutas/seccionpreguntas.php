<?php
	$router->post('seccionPreguntas/nuevo', ['uses' => 'SeccionpreguntasController@nuevo']);
	$router->post('seccionPreguntas/mostrar', ['uses' => 'SeccionpreguntasController@mostrar']);
	$router->post('seccionPreguntas/existen', ['uses' => 'SeccionpreguntasController@existen']);
	$router->post('seccionPreguntas/modificar', ['uses' => 'SeccionpreguntasController@modificar']);
	$router->post('seccionPreguntas/eliminar', ['uses' => 'SeccionpreguntasController@eliminar']);
?>