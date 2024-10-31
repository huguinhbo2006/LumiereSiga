<?php
	$router->get('comisionesvinculaciones/listas', ['uses' => 'ComisionesvinculacionesController@listas']);
	$router->post('comisionesvinculaciones/comisiones', ['uses' => 'ComisionesvinculacionesController@comisiones']);
?>