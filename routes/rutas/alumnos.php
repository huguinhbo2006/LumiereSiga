<?php
	$router->post('alumnos/externos', ['uses' => 'AlumnosController@externos']);
	$router->post('alumnos/inscritos', ['uses' => 'AlumnosController@inscritos']);
	$router->post('alumnos/busquedaExternos', ['uses' => 'AlumnosController@busquedaExternos']);
	$router->post('alumnos/busquedaInscritos', ['uses' => 'AlumnosController@busquedaInscritos']);
	$router->post('alumnos/ventas', ['uses' => 'AlumnosController@ventas']);
	$router->post('alumnos/busquedaVentas', ['uses' => 'AlumnosController@busquedaVentas']);
	$router->post('alumnos/traerAlumno', ['uses' => 'AlumnosController@traerAlumno']);
	$router->post('alumnos/actualizarTutor', ['uses' => 'AlumnosController@actualizarTutor']);
	$router->post('alumnos/actualizarAlumno', ['uses' => 'AlumnosController@actualizarAlumno']);
	$router->post('alumnos/actualizarDomicilio', ['uses' => 'AlumnosController@actualizarDomicilio']);
	$router->post('alumnos/actualizarEstatusFicha', ['uses' => 'AlumnosController@actualizarEstatusFicha']);
	$router->post('alumnos/actualizarDatosEscolares', ['uses' => 'AlumnosController@actualizarDatosEscolares']);
	$router->post('alumnos/nuevaInscripcion', ['uses' => 'AlumnosController@nuevaInscripcion']);
	$router->post('alumnos/nuevoArchivoFicha', ['uses' => 'AlumnosController@nuevoArchivoFicha']);
	$router->post('alumnos/eliminarArchivoFicha', ['uses' => 'AlumnosController@eliminarArchivoFicha']);
	$router->post('alumnos/busquedaGeneral', ['uses' => 'AlumnosController@busquedaGeneral']);
	$router->post('alumnos/actualizarNumeroRegistro', ['uses' => 'AlumnosController@actualizarNumeroRegistro']);
?>