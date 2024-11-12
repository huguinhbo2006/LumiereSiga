<?php
	$router->post('balanceSucursales/mostrar', ['uses' => 'BalancesucursalesController@mostrar']);
	$router->post('balanceSucursales/corte', ['uses' => 'BalancesucursalesController@corte']);
	$router->post('balanceSucursales/nuevoValeAdministrativo', ['uses' => 'BalancesucursalesController@nuevoValeAdministrativo']);
	$router->post('balanceSucursales/agregarSaldoValeAdministrativo', ['uses' => 'BalancesucursalesController@agregarSaldoValeAdministrativo']);
	$router->post('balanceSucursales/quitarSaldoValeAdministrativo', ['uses' => 'BalancesucursalesController@quitarSaldoValeAdministrativo']);
?>