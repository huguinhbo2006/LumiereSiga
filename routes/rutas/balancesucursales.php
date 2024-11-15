<?php
	$router->post('balanceSucursales/mostrar', ['uses' => 'BalancesucursalesController@mostrar']);
	$router->post('balanceSucursales/corte', ['uses' => 'BalancesucursalesController@corte']);
	$router->post('balanceSucursales/nuevoVale', ['uses' => 'BalancesucursalesController@nuevoVale']);
	$router->post('balanceSucursales/saldoVale', ['uses' => 'BalancesucursalesController@saldoVale']);
	$router->post('balanceSucursales/saldoCaja', ['uses' => 'BalancesucursalesController@saldoCaja']);
?>