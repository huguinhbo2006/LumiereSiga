<?php
	$router->post('telegram/error', ['uses' => 'TelegramController@error']);
	$router->post('telegram/soporte', ['uses' => 'TelegramController@soporte']);
?>