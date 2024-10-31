<?php
	$router->get('mediosContactos/mostrar', ['uses' => 'MedioscontactosController@mostrar']);
	$router->post('mediosContactos/nuevo', ['uses' => 'MedioscontactosController@nuevo']);
	$router->post('mediosContactos/activar', ['uses' => 'MedioscontactosController@activar']);
	$router->post('mediosContactos/desactivar', ['uses' => 'MedioscontactosController@desactivar']);
	$router->post('mediosContactos/eliminar', ['uses' => 'MedioscontactosController@eliminar']);
	$router->post('mediosContactos/modificar', ['uses' => 'MedioscontactosController@modificar']);
?>