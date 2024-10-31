<?php
	$router->post('estadisticasMaestras/id', ['uses' => 'EstadisticasMaestrasController@id']);
	$router->post('estadisticasMaestras/datos', ['uses' => 'EstadisticasMaestrasController@datos']);
	$router->post('estadisticasMaestras/alumnos', ['uses' => 'EstadisticasMaestrasController@alumnos']);
	$router->post('estadisticasMaestras/escolares', ['uses' => 'EstadisticasMaestrasController@escolares']);
	$router->post('estadisticasMaestras/publicitarios', ['uses' => 'EstadisticasMaestrasController@publicitarios']);
	$router->post('estadisticasMaestras/cuenta', ['uses' => 'EstadisticasMaestrasController@cuenta']);
?>