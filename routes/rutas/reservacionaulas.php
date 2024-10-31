<?php
	$router->post('reservacionaulas/mostrar', ['uses' => 'ReservacionesaulasController@mostrar']);
	$router->post('reservacionaulas/reservacion', ['uses' => 'ReservacionesaulasController@reservacion']);
	$router->post('reservacionaulas/eliminar', ['uses' => 'ReservacionesaulasController@eliminar']);
	$router->post('reservacionaulas/mostrarReservaciones', ['uses' => 'ReservacionesaulasController@mostrarReservaciones']);
	$router->get('reservacionaulas/listas', ['uses' => 'ReservacionesaulasController@listas']);
	$router->post('reservacionaulas/horarios', ['uses' => 'ReservacionesaulasController@horarios']);
?>