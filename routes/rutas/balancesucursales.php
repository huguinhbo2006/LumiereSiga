<?php
	$router->post('balanceSucursales/mostrar', ['uses' => 'BalancesucursalesController@mostrar']);
	$router->post('balanceSucursales/mostrarAdministrativos', ['uses' => 'BalancesucursalesController@mostrarAdministrativos']);
	$router->post('balanceSucursales/nuevoValeAdministrativo', ['uses' => 'BalancesucursalesController@nuevoValeAdministrativo']);
	$router->post('balanceSucursales/agregarSaldoValeAdministrativo', ['uses' => 'BalancesucursalesController@agregarSaldoValeAdministrativo']);
	$router->post('balanceSucursales/quitarSaldoValeAdministrativo', ['uses' => 'BalancesucursalesController@quitarSaldoValeAdministrativo']);
?>