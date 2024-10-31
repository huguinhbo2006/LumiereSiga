<?php
	$router->get('estadisticas/selects', ['uses' => 'EstadisticasController@selects']);
	$router->post('estadisticas/estadisticasLicenciatura', ['uses' => 'EstadisticasController@estadisticasLicenciatura']);
	$router->post('estadisticas/estadisticasMeses', ['uses' => 'EstadisticasController@estadisticasMeses']);
	$router->post('estadisticas/estadisticasCursosLV', ['uses' => 'EstadisticasController@estadisticasCursosLV']);
	$router->post('estadisticas/estadisticasCursosFS', ['uses' => 'EstadisticasController@estadisticasCursosFS']);	
	$router->post('estadisticas/estadisticasSemanas', ['uses' => 'EstadisticasController@estadisticasSemanas']);
	$router->post('estadisticas/estadisticasMarketing', ['uses' => 'EstadisticasController@estadisticasMarketing']);
	$router->post('estadisticas/estadisticasMedios', ['uses' => 'EstadisticasController@estadisticasMedios']);
	$router->post('estadisticas/estadisticasMotivos', ['uses' => 'EstadisticasController@estadisticasMotivos']);
	$router->post('estadisticas/estadisticasPublicitarios', ['uses' => 'EstadisticasController@estadisticasPublicitarios']);
	$router->post('estadisticas/estadisticasVias', ['uses' => 'EstadisticasController@estadisticasVias']);
	$router->post('estadisticas/estadisticasSexos', ['uses' => 'EstadisticasController@estadisticasSexos']);
	$router->post('estadisticas/estadisticasEscuelas', ['uses' => 'EstadisticasController@estadisticasEscuelas']);
	$router->post('estadisticas/estadisticasCarreras', ['uses' => 'EstadisticasController@estadisticasCarreras']);
	$router->post('estadisticas/estadisticasCentros', ['uses' => 'EstadisticasController@estadisticasCentros']);
	$router->post('estadisticas/estadisticasFinancieras', ['uses' => 'EstadisticasController@estadisticasFinancieras']);
	$router->post('estadisticas/estadisticasFinancierasMeses', ['uses' => 'EstadisticasController@estadisticasFinancierasMeses']);
	$router->post('estadisticas/estadisticasFinancierasModalidades', ['uses' => 'EstadisticasController@estadisticasFinancierasModalidades']);
	$router->post('estadisticas/estadisticasFinancierasCursosLVL', ['uses' => 'EstadisticasController@estadisticasFinancierasCursosLVL']);
	$router->post('estadisticas/estadisticasFinancierasCursosFSL', ['uses' => 'EstadisticasController@estadisticasFinancierasCursosFSL']);
	$router->post('estadisticas/estadisticasFinancierasCursosLVP', ['uses' => 'EstadisticasController@estadisticasFinancierasCursosLVP']);
	$router->post('estadisticas/estadisticasFinancierasCursosFSP', ['uses' => 'EstadisticasController@estadisticasFinancierasCursosFSP']);
	$router->post('estadisticas/estadisticasFinancierasCursosMorosos', ['uses' => 'EstadisticasController@estadisticasFinancierasCursosMorosos']);
?>