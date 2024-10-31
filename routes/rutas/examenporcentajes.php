<?php
	$router->post('examenPorcentajes/nuevo', ['uses' => 'ExamenporcentajesController@nuevo']);
	$router->post('examenPorcentajes/mostrar', ['uses' => 'ExamenporcentajesController@mostrar']);
	$router->post('examenPorcentajes/traerSecciones', ['uses' => 'ExamenporcentajesController@traerSecciones']);
	$router->post('examenPorcentajes/agregarSeccion', ['uses' => 'ExamenporcentajesController@agregarSeccion']);
	$router->post('examenPorcentajes/eliminarSeccion', ['uses' => 'ExamenporcentajesController@eliminarSeccion']);
	$router->post('examenPorcentajes/eliminarPorcentaje', ['uses' => 'ExamenporcentajesController@eliminarPorcentaje']);
?>