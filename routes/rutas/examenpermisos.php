<?php
	$router->post('examenPermisos/nuevo', ['uses' => 'ExamenpermisosController@nuevo']);
	$router->post('examenPermisos/mostrar', ['uses' => 'ExamenpermisosController@mostrar']);
	$router->post('examenPermisos/eliminar', ['uses' => 'ExamenpermisosController@eliminar']);
	$router->get('examenPermisos/selectores', ['uses' => 'ExamenpermisosController@selectores']);
?>