<?php
	$router->get('municipios/mostrar', ['uses' => 'MunicipiosController@mostrar']);
	$router->post('municipios/nuevo', ['uses' => 'MunicipiosController@nuevo']);
	$router->post('municipios/activar', ['uses' => 'MunicipiosController@activar']);
	$router->post('municipios/desactivar', ['uses' => 'MunicipiosController@desactivar']);
	$router->post('municipios/eliminar', ['uses' => 'MunicipiosController@eliminar']);
	$router->post('municipios/modificar', ['uses' => 'MunicipiosController@modificar']);
?>