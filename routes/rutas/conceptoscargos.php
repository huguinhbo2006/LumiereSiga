<?php
	$router->get('conceptosCargos/mostrar', ['uses' => 'ConceptoscargosController@mostrar']);
	$router->post('conceptosCargos/nuevo', ['uses' => 'ConceptoscargosController@nuevo']);
	$router->post('conceptosCargos/activar', ['uses' => 'ConceptoscargosController@activar']);
	$router->post('conceptosCargos/desactivar', ['uses' => 'ConceptoscargosController@desactivar']);
	$router->post('conceptosCargos/eliminar', ['uses' => 'ConceptoscargosController@eliminar']);
	$router->post('conceptosCargos/modificar', ['uses' => 'ConceptoscargosController@modificar']);
?>