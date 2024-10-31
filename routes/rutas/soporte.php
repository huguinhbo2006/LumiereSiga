<?php
	$router->post('soporte/datosAlumno', ['uses' => 'SoporteController@datosAlumno']);
	$router->post('soporte/modificarAlumno', ['uses' => 'SoporteController@modificarAlumno']);
?>