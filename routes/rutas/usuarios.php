<?php
	$router->post('usuarios/nuevo', ['uses' => 'UsuariosController@nuevo']);
	$router->post('usuarios/traer', ['uses' => 'UsuariosController@traer']);
	$router->post('usuarios/modificar', ['uses' => 'UsuariosController@modificar']);
	$router->post('usuarios/eliminar', ['uses' => 'UsuariosController@eliminar']);
	$router->post('usuarios/informacion', ['uses' => 'UsuariosController@informacion']);
	$router->post('usuarios/modificarImagen', ['uses' => 'UsuariosController@modificarImagen']);
	$router->post('usuarios/datosUsuario', ['uses' => 'UsuariosController@datosUsuario']);
	$router->post('usuarios/modificarPassword', ['uses' => 'UsuariosController@modificarPassword']);
?>