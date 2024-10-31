<?php
	$router->post('sucursalEscuela/nuevo', ['uses' => 'WebsucursalescuelasController@nuevo']);
	$router->post('sucursalEscuela/mostrar', ['uses' => 'WebsucursalescuelasController@mostrar']);
	$router->post('sucursalEscuela/eliminar', ['uses' => 'WebsucursalescuelasController@eliminar']);
?>