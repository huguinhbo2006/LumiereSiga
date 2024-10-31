<?php
	$router->post('reservaciongrupos/posibles', ['uses' => 'ReservacionesgruposController@posibles']);
	$router->post('reservaciongrupos/nueva', ['uses' => 'ReservacionesgruposController@nueva']);
	$router->post('reservaciongrupos/agregados', ['uses' => 'ReservacionesgruposController@agregados']);
	$router->post('reservaciongrupos/eliminar', ['uses' => 'ReservacionesgruposController@eliminar']);
?>