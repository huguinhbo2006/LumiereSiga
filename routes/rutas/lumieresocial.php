<?php
	$router->post('lumieresocial/existeAlumno', ['uses' => 'LumieresocialController@existeAlumno']);
	$router->post('lumieresocial/traerAlumno', ['uses' => 'LumieresocialController@traerAlumno']);
	$router->post('lumieresocial/traerFichas', ['uses' => 'LumieresocialController@traerFichas']);
	$router->post('lumieresocial/traerExamenes', ['uses' => 'LumieresocialController@traerExamenes']);
	$router->post('lumieresocial/traerSecciones', ['uses' => 'LumieresocialController@traerSecciones']);
	$router->post('lumieresocial/traerPreguntas', ['uses' => 'LumieresocialController@traerPreguntas']);
	$router->post('lumieresocial/traerInstrucciones', ['uses' => 'LumieresocialController@traerInstrucciones']);
	$router->post('lumieresocial/guardarSeccion', ['uses' => 'LumieresocialController@guardarSeccion']);
	$router->post('lumieresocial/traerCalificaciones', ['uses' => 'LumieresocialController@traerCalificaciones']);
	$router->post('lumieresocial/traerEstadisticas', ['uses' => 'LumieresocialController@traerEstadisticas']);
	$router->post('lumieresocial/respuestasSeccion', ['uses' => 'LumieresocialController@respuestasSeccion']);
	$router->post('lumieresocial/verificarFechaExamen', ['uses' => 'LumieresocialController@verificarFechaExamen']);
	$router->post('lumieresocial/traerPregunta', ['uses' => 'LumieresocialController@traerPregunta']);
?>