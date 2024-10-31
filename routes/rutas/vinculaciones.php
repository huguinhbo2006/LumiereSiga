<?php
	$router->post('vinculaciones/nuevaEmpresa', ['uses' => 'VinculacionesController@nuevaEmpresa']);
	$router->get('vinculaciones/mostrarEmpresas', ['uses' => 'VinculacionesController@mostrarEmpresas']);
	$router->post('vinculaciones/modificarEmpresa', ['uses' => 'VinculacionesController@modificarEmpresa']);
	$router->post('vinculaciones/traerEmpresa', ['uses' => 'VinculacionesController@traerEmpresa']);
	$router->post('vinculaciones/guardarSeguimientoEmpresa', ['uses' => 'VinculacionesController@guardarSeguimientoEmpresa']);
	$router->post('vinculaciones/cambiarEstatusEmpresa', ['uses' => 'VinculacionesController@cambiarEstatusEmpresa']);
	$router->post('vinculaciones/guardarConvenioEmpresa', ['uses' => 'VinculacionesController@guardarConvenioEmpresa']);
	$router->post('vinculaciones/cambiarEstatusConvenio', ['uses' => 'VinculacionesController@cambiarEstatusConvenio']);
?>