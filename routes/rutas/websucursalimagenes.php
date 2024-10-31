<?php
	$router->post('sucursalImagen/nuevo', ['uses' => 'WebsucursalimagenesController@nuevo']);
	$router->post('sucursalImagen/mostrar', ['uses' => 'WebsucursalimagenesController@mostrar']);
	$router->post('sucursalImagen/eliminar', ['uses' => 'WebsucursalimagenesController@eliminar']);
?>