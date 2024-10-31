<?php
	$router->post('lecturas/nuevo', ['uses' => 'LecturasController@nuevo']);
	$router->post('lecturas/modificar', ['uses' => 'LecturasController@modificar']);
	$router->post('lecturas/mostrar', ['uses' => 'LecturasController@mostrar']);
	$router->post('lecturas/eliminar', ['uses' => 'LecturasController@eliminar']);
?>