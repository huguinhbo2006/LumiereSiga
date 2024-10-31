<?php
	$router->get('cursoslumiere/pagina', ['uses' => 'CursoslumiereController@pagina']);
	$router->post('cursoslumiere/noticia', ['uses' => 'CursoslumiereController@noticia']);
	$router->post('cursoslumiere/tipo', ['uses' => 'CursoslumiereController@tipo']);
	$router->post('cursoslumiere/curso', ['uses' => 'CursoslumiereController@curso']);
	$router->get('cursoslumiere/proximos4', ['uses' => 'CursoslumiereController@proximos4']);
?>