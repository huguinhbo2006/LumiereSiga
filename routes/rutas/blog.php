<?php
	$router->post('blog/nuevo', ['uses' => 'BlogController@nuevo']);
	$router->post('blog/modificar', ['uses' => 'BlogController@modificar']);
	$router->post('blog/eliminar', ['uses' => 'BlogController@eliminar']);
	$router->get('blog/traer', ['uses' => 'BlogController@traer']);
?>