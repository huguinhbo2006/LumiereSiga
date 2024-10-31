<?php
	$router->get('aulasdisponibles/mostrar', ['uses' => 'AulasdisponiblesController@mostrar']);
	$router->post('aulasdisponibles/crear', ['uses' => 'AulasdisponiblesController@crear']);
	$router->post('aulasdisponibles/eliminar', ['uses' => 'AulasdisponiblesController@eliminar']);
?>