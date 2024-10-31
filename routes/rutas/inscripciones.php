<?php
	$router->post('inscripciones/nuevo', ['uses' => 'InscripcionesController@nuevo']);
	$router->post('inscripciones/existeAlumno', ['uses' => 'InscripcionesController@existeAlumno']);
	$router->post('inscripciones/mostrar', ['uses' => 'InscripcionesController@mostrar']);
	$router->post('inscripciones/cupo', ['uses' => 'InscripcionesController@cupo']);
	$router->post('inscripciones/horarioBloqueado', ['uses' => 'InscripcionesController@horarioBloqueado']);
	$router->post('inscripciones/canjearCupon', ['uses' => 'InscripcionesController@canjearCupon']);

	//Componentes Listas
	$router->get('inscripciones/listasInscripcion', ['uses' => 'InscripcionesController@listasInscripcion']);
	$router->get('inscripciones/listasComponenteAlumno', ['uses' => 'InscripcionesController@listasComponenteAlumno']);
	$router->get('inscripciones/listasComponenteDomicilio', ['uses' => 'InscripcionesController@listasComponenteDomicilio']);
	$router->get('inscripciones/listasComponenteEscolares', ['uses' => 'InscripcionesController@listasComponenteEscolares']);
	$router->get('inscripciones/listasComponentePublicitarios', ['uses' => 'InscripcionesController@listasComponentePublicitarios']);
	$router->get('inscripciones/listasConceptos', ['uses' => 'InscripcionesController@listasConceptos']);
?>