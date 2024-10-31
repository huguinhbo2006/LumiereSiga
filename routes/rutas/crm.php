<?php
	$router->post('crm/nuevoProspecto', ['uses' => 'CRMController@nuevoProspecto']);
	$router->post('crm/mostrarProspectos', ['uses' => 'CRMController@mostrarProspectos']);
	$router->post('crm/modificarProspecto', ['uses' => 'CRMController@modificarProspecto']);
	$router->post('crm/traerProspecto', ['uses' => 'CRMController@traerProspecto']);
	$router->post('crm/buscarProspecto', ['uses' => 'CRMController@buscarProspecto']);
	$router->post('crm/eliminarProspecto', ['uses' => 'CRMController@eliminarProspecto']);
	$router->post('crm/guardarSeguimiento', ['uses' => 'CRMController@guardarSeguimiento']);
	$router->post('crm/modificarSeguimiento', ['uses' => 'CRMController@modificarSeguimiento']);
	$router->post('crm/traerSeguimiento', ['uses' => 'CRMController@traerSeguimiento']);
	$router->post('crm/guardarDescripcionSeguimiento', ['uses' => 'CRMController@guardarDescripcionSeguimiento']);
	$router->post('crm/modificarEstatusSeguimiento', ['uses' => 'CRMController@modificarEstatusSeguimiento']);
	$router->post('crm/guardarCita', ['uses' => 'CRMController@guardarCita']);
	$router->post('crm/modificarEstatusCita', ['uses' => 'CRMController@modificarEstatusCita']);
	$router->post('crm/mostrarCitas', ['uses' => 'CRMController@mostrarCitas']);
	$router->post('crm/esVentas', ['uses' => 'CRMController@esVentas']);
	$router->post('crm/buscarFicha', ['uses' => 'CRMController@buscarFicha']);
	$router->post('crm/confirmarPassword', ['uses' => 'CRMController@confirmarPassword']);
	$router->post('crm/asignarFicha', ['uses' => 'CRMController@asignarFicha']);
?>