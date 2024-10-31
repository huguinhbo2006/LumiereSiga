<?php
	$router->post('reportes/reporteVentas', ['uses' => 'ReportesController@reporteVentas']);
	$router->post('reportes/reporteInscritos', ['uses' => 'ReportesController@reporteInscritos']);
	$router->post('reportes/reporteImpartidos', ['uses' => 'ReportesController@reporteImpartidos']);
	$router->post('reportes/ingresosGenerales', ['uses' => 'ReportesController@ingresosGenerales']);
	$router->post('reportes/egresosGenerales', ['uses' => 'ReportesController@egresosGenerales']);
	$router->post('reportes/inscripciones', ['uses' => 'ReportesController@inscripciones']);
	$router->post('reportes/buscar', ['uses' => 'ReportesController@buscar']);
	$router->post('reportes/comisiones', ['uses' => 'ReportesController@comisiones']);
	$router->get('reportes/selects', ['uses' => 'ReportesController@selects']);
	$router->get('reportes/empleadosVentas', ['uses' => 'ReportesController@empleadosVentas']);
	$router->post('reportes/prospectos', ['uses' => 'ReportesController@prospectos']);
?>