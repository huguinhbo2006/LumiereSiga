<?php
	$router->get('calificador/selects', ['uses' => 'CalificadorController@selects']);
	$router->post('calificador/mostrar', ['uses' => 'CalificadorController@mostrar']);
	$router->post('calificador/grupos', ['uses' => 'CalificadorController@grupos']);
	$router->post('calificador/horarios', ['uses' => 'CalificadorController@horarios']);
	$router->post('calificador/alumnos', ['uses' => 'CalificadorController@alumnos']);
	$router->post('calificador/traerExamenes', ['uses' => 'CalificadorController@traerExamenes']);
	$router->post('calificador/traerSecciones', ['uses' => 'CalificadorController@traerSecciones']);
	$router->post('calificador/guardarSecciones', ['uses' => 'CalificadorController@guardarSecciones']);
?>