<?php
	$router->get('configuracionesVentas/traerComisionesColbach', ['uses' => 'ConfiguracionesventasController@traerComisionesColbach']);
	$router->post('configuracionesVentas/nuevaComisionColbach', ['uses' => 'ConfiguracionesventasController@nuevaComisionColbach']);
	$router->post('configuracionesVentas/eliminarComisionColbach', ['uses' => 'ConfiguracionesventasController@eliminarComisionColbach']);
	$router->get('configuracionesVentas/selects', ['uses' => 'ConfiguracionesventasController@selects']);
	$router->post('configuracionesVentas/nuevaComisionCurso', ['uses' => 'ConfiguracionesventasController@nuevaComisionCurso']);
	$router->post('configuracionesVentas/traerComisionesCurso', ['uses' => 'ConfiguracionesventasController@traerComisionesCurso']);
	$router->post('configuracionesVentas/modificarComisionCurso', ['uses' => 'ConfiguracionesventasController@modificarComisionCurso']);
	$router->post('configuracionesVentas/eliminarComisionCurso', ['uses' => 'ConfiguracionesventasController@eliminarComisionCurso']);
?>