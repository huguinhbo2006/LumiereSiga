<?php
	$router->get('conceptosAbonos/mostrar', ['uses' => 'ConceptosabonosController@mostrar']);
	$router->post('conceptosAbonos/nuevo', ['uses' => 'ConceptosabonosController@nuevo']);
	$router->post('conceptosAbonos/activar', ['uses' => 'ConceptosabonosController@activar']);
	$router->post('conceptosAbonos/desactivar', ['uses' => 'ConceptosabonosController@desactivar']);
	$router->post('conceptosAbonos/eliminar', ['uses' => 'ConceptosabonosController@eliminar']);
	$router->post('conceptosAbonos/modificar', ['uses' => 'ConceptosabonosController@modificar']);
?>