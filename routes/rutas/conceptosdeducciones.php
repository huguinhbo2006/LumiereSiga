<?php
	$router->get('conceptosDeducciones/mostrar', ['uses' => 'ConceptosdeduccionesController@mostrar']);
	$router->post('conceptosDeducciones/nuevo', ['uses' => 'ConceptosdeduccionesController@nuevo']);
	$router->post('conceptosDeducciones/activar', ['uses' => 'ConceptosdeduccionesController@activar']);
	$router->post('conceptosDeducciones/desactivar', ['uses' => 'ConceptosdeduccionesController@desactivar']);
	$router->post('conceptosDeducciones/eliminar', ['uses' => 'ConceptosdeduccionesController@eliminar']);
	$router->post('conceptosDeducciones/modificar', ['uses' => 'ConceptosdeduccionesController@modificar']);
?>