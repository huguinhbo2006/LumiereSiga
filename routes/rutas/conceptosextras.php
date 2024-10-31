<?php
	$router->get('conceptosExtras/mostrar', ['uses' => 'ConceptosextrasController@mostrar']);
	$router->post('conceptosExtras/nuevo', ['uses' => 'ConceptosextrasController@nuevo']);
	$router->post('conceptosExtras/activar', ['uses' => 'ConceptosextrasController@activar']);
	$router->post('conceptosExtras/desactivar', ['uses' => 'ConceptosextrasController@desactivar']);
	$router->post('conceptosExtras/eliminar', ['uses' => 'ConceptosextrasController@eliminar']);
	$router->post('conceptosExtras/modificar', ['uses' => 'ConceptosextrasController@modificar']);
?>