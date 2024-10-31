<?php
	$router->post('bloqueohorarios/mostrar', ['uses' => 'BloqueohorariosController@mostrar']);
	$router->post('bloqueohorarios/bloquear', ['uses' => 'BloqueohorariosController@bloquear']);
	$router->post('bloqueohorarios/desbloquear', ['uses' => 'BloqueohorariosController@desbloquear']);
?>