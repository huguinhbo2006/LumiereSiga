<?php
	$router->post('preguntas/nuevo', ['uses' => 'PreguntasController@nuevo']);
	$router->post('preguntas/imagen', ['uses' => 'PreguntasController@imagen']);
	$router->post('preguntas/mostrar', ['uses' => 'PreguntasController@mostrar']);
	$router->post('preguntas/modificar', ['uses' => 'PreguntasController@modificar']);
?>