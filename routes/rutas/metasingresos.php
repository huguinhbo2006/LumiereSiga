<?php
	$router->post('metasIngresos/mostrar', ['uses' => 'MetasingresosController@mostrar']);
	$router->post('metasIngresos/nuevo', ['uses' => 'MetasingresosController@nuevo']);
	$router->post('metasIngresos/modificar', ['uses' => 'MetasingresosController@modificar']);
	$router->post('metasIngresos/eliminar', ['uses' => 'MetasingresosController@eliminar']);
	$router->post('metasIngresos/metas', ['uses' => 'MetasingresosController@metas']);
?>