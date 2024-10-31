<?php
	$router->post('nominas/nuevo', ['uses' => 'NominasController@nuevo']);
	$router->post('nominas/mostrar', ['uses' => 'NominasController@mostrar']);
	$router->get('nominas/creadas', ['uses' => 'NominasController@creadas']);
	$router->post('nominas/autorizar', ['uses' => 'NominasController@autorizar']);
	$router->post('nominas/autorizadas', ['uses' => 'NominasController@autorizadas']);
	$router->post('nominas/cobrar', ['uses' => 'NominasController@cobrar']);
	$router->post('nominas/nomina', ['uses' => 'NominasController@nomina']);
	$router->post('nominas/modificar', ['uses' => 'NominasController@modificar']);
	$router->post('nominas/cuenta', ['uses' => 'NominasController@cuenta']);
	$router->post('nominas/agregarPercepcion', ['uses' => 'NominasController@agregarPercepcion']);
	$router->post('nominas/agregarDeduccion', ['uses' => 'NominasController@agregarDeduccion']);
	$router->post('nominas/eliminarPercepcion', ['uses' => 'NominasController@eliminarPercepcion']);
	$router->post('nominas/eliminarDeduccion', ['uses' => 'NominasController@eliminarDeduccion']);
	$router->get('nominas/mostrarSolicitudesNominas', ['uses' => 'NominasController@mostrarSolicitudesNominas']);
	$router->post('nominas/aceptarSolicitudNomina', ['uses' => 'NominasController@aceptarSolicitudNomina']);
	$router->post('nominas/rechazarSolicitudNomina', ['uses' => 'NominasController@rechazarSolicitudNomina']);
?>