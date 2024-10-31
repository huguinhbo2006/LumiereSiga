<?php
	$router->get('conceptosDevoluciones/mostrar', ['uses' => 'ConceptosdevolucionesController@mostrar']);
	$router->post('conceptosDevoluciones/nuevo', ['uses' => 'ConceptosdevolucionesController@nuevo']);
	$router->post('conceptosDevoluciones/activar', ['uses' => 'ConceptosdevolucionesController@activar']);
	$router->post('conceptosDevoluciones/desactivar', ['uses' => 'ConceptosdevolucionesController@desactivar']);
	$router->post('conceptosDevoluciones/eliminar', ['uses' => 'ConceptosdevolucionesController@eliminar']);
	$router->post('conceptosDevoluciones/modificar', ['uses' => 'ConceptosdevolucionesController@modificar']);
?>