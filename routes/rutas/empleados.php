<?php
	$router->get('empleados/mostrar', ['uses' => 'EmpleadosController@mostrar']);
	$router->post('empleados/nuevo', ['uses' => 'EmpleadosController@nuevo']);
	$router->post('empleados/modificar', ['uses' => 'EmpleadosController@modificar']);
	$router->post('empleados/activar', ['uses' => 'EmpleadosController@activar']);
	$router->post('empleados/desactivar', ['uses' => 'EmpleadosController@desactivar']);
	$router->post('empleados/eliminar', ['uses' => 'EmpleadosController@eliminar']);
	$router->post('empleados/busquedaNombre', ['uses' => 'EmpleadosController@busquedaNombre']);
	$router->post('empleados/sucursales', ['uses' => 'EmpleadosController@sucursales']);
	$router->post('empleados/agregarSucursal', ['uses' => 'EmpleadosController@agregarSucursal']);
	$router->post('empleados/eliminarSucursal', ['uses' => 'EmpleadosController@eliminarSucursal']);
	$router->post('empleados/imagenes', ['uses' => 'EmpleadosController@imagenes']);
?>