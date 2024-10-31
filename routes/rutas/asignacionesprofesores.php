<?php
	$router->post('asignacionProfesores/mostrar', ['uses' => 'AsignacionesprofesoresController@mostrar']);
	$router->post('asignacionProfesores/asignacion', ['uses' => 'AsignacionesprofesoresController@asignacion']);
	$router->post('asignacionProfesores/eliminar', ['uses' => 'AsignacionesprofesoresController@eliminar']);
	$router->get('asignacionProfesores/activos', ['uses' => 'AsignacionesprofesoresController@activos']);
?>