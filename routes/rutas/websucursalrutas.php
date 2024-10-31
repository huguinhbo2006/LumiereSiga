<?php
	$router->post('sucursalRuta/nuevo', ['uses' => 'WebsucursalrutasController@nuevo']);
	$router->post('sucursalRuta/mostrar', ['uses' => 'WebsucursalrutasController@mostrar']);
	$router->post('sucursalRuta/eliminar', ['uses' => 'WebsucursalrutasController@eliminar']);
?>