<?php
	$router->get('motivosInscripciones/mostrar', ['uses' => 'MotivosinscripcionesController@mostrar']);
	$router->post('motivosInscripciones/nuevo', ['uses' => 'MotivosinscripcionesController@nuevo']);
	$router->post('motivosInscripciones/activar', ['uses' => 'MotivosinscripcionesController@activar']);
	$router->post('motivosInscripciones/desactivar', ['uses' => 'MotivosinscripcionesController@desactivar']);
	$router->post('motivosInscripciones/eliminar', ['uses' => 'MotivosinscripcionesController@eliminar']);
	$router->post('motivosInscripciones/modificar', ['uses' => 'MotivosinscripcionesController@modificar']);
?>