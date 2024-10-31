<?php
	$router->post('cursosParidades/mostrar', ['uses' => 'CursosparidadesController@mostrar']);
	$router->post('cursosParidades/nuevo', ['uses' => 'CursosparidadesController@nuevo']);
	$router->post('cursosParidades/eliminar', ['uses' => 'CursosparidadesController@eliminar']);
?>