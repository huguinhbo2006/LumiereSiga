<?php
	$router->get('conceptosDescuentos/mostrar', ['uses' => 'ConceptosdescuentosController@mostrar']);
	$router->post('conceptosDescuentos/nuevo', ['uses' => 'ConceptosdescuentosController@nuevo']);
	$router->post('conceptosDescuentos/activar', ['uses' => 'ConceptosdescuentosController@activar']);
	$router->post('conceptosDescuentos/desactivar', ['uses' => 'ConceptosdescuentosController@desactivar']);
	$router->post('conceptosDescuentos/eliminar', ['uses' => 'ConceptosdescuentosController@eliminar']);
	$router->post('conceptosDescuentos/modificar', ['uses' => 'ConceptosdescuentosController@modificar']);
?>