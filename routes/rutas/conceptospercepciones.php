<?php
	$router->get('conceptosPercepciones/mostrar', ['uses' => 'ConceptospercepcionesController@mostrar']);
	$router->post('conceptosPercepciones/nuevo', ['uses' => 'ConceptospercepcionesController@nuevo']);
	$router->post('conceptosPercepciones/activar', ['uses' => 'ConceptospercepcionesController@activar']);
	$router->post('conceptosPercepciones/desactivar', ['uses' => 'ConceptospercepcionesController@desactivar']);
	$router->post('conceptosPercepciones/eliminar', ['uses' => 'ConceptospercepcionesController@eliminar']);
	$router->post('conceptosPercepciones/modificar', ['uses' => 'ConceptospercepcionesController@modificar']);
?>