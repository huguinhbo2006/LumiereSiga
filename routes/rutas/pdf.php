<?php
	$router->post('pdf/fichaInscripcion', ['uses' => 'PDFController@fichaInscripcion']);
	$router->post('pdf/cartaCongelacion', ['uses' => 'PDFController@cartaCongelacion']);
	$router->post('pdf/reciboPago', ['uses' => 'PDFController@reciboPago']);
	$router->post('pdf/ingreso', ['uses' => 'PDFController@ingreso']);
	$router->post('pdf/egreso', ['uses' => 'PDFController@egreso']);
	$router->post('pdf/nomina', ['uses' => 'PDFController@nomina']);
	$router->post('pdf/corte', ['uses' => 'PDFController@corte']);
	$router->post('pdf/boletaAlumno', ['uses' => 'PDFController@boletaAlumno']);
	$router->post('pdf/boletaGrupo', ['uses' => 'PDFController@boletaGrupo']);
?>