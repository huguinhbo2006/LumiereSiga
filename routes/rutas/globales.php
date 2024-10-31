<?php
	$router->post('solicitudes', ['uses' => 'FormulariosController@nsolicitudes']);
	$router->post('guia', ['uses' => 'FormulariosController@nguia']);
	$router->post('vocacional', ['uses' => 'FormulariosController@nvocacional']);
	$router->post('promocion', ['uses' => 'FormulariosController@npromocion']);
	$router->post('cupon', ['uses' => 'FormulariosController@ncupon']);
	$router->post('empresas', ['uses' => 'FormulariosController@nempresas']);
	$router->get('cucs', ['uses' => 'PaginaController@cucs']);
	$router->get('cuaad', ['uses' => 'PaginaController@cuaad']);
	$router->get('cualtos', ['uses' => 'PaginaController@cualtos']);
	$router->get('cucba', ['uses' => 'PaginaController@cucba']);
	$router->get('cucea', ['uses' => 'PaginaController@cucea']);
	$router->get('cucei', ['uses' => 'PaginaController@cucei']);
	$router->get('cucosta', ['uses' => 'PaginaController@cucosta']);
	$router->get('cucsh', ['uses' => 'PaginaController@cucsh']);
	$router->get('cusur', ['uses' => 'PaginaController@cusur']);
	$router->get('cutonala', ['uses' => 'PaginaController@cutonala']);
?>