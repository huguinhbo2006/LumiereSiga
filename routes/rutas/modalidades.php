<?php
	$router->get('modalidades/mostrar', ['uses' => 'ModalidadesController@mostrar']);
	$router->post('modalidades/nuevo', ['uses' => 'ModalidadesController@nueva']);
	$router->post('modalidades/activar', ['uses' => 'ModalidadesController@activar']);
	$router->post('modalidades/desactivar', ['uses' => 'ModalidadesController@desactivar']);
	$router->post('modalidades/eliminar', ['uses' => 'ModalidadesController@eliminar']);
	$router->post('modalidades/modificar', ['uses' => 'ModalidadesController@modificar']);
?>