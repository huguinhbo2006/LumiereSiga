<?php
	$router->get('centrosUniversitarios/mostrar', ['uses' => 'CentrosuniversitariosController@mostrar']);
	$router->post('centrosUniversitarios/nuevo', ['uses' => 'CentrosuniversitariosController@nuevo']);
	$router->post('centrosUniversitarios/activar', ['uses' => 'CentrosuniversitariosController@activar']);
	$router->post('centrosUniversitarios/desactivar', ['uses' => 'CentrosuniversitariosController@desactivar']);
	$router->post('centrosUniversitarios/eliminar', ['uses' => 'CentrosuniversitariosController@eliminar']);
	$router->post('centrosUniversitarios/modificar', ['uses' => 'CentrosuniversitariosController@modificar']);
?>