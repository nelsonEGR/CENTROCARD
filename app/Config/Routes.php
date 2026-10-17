<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('/usuarios/verificar', 'Usuarios::verificar');

$routes->group('', ['filter' => 'AuthCheck'], function ($routes) {
	// Usuarios
	$routes->get('usuarios', 'Usuarios::index');
	$routes->get('usuarios/listar', 'Usuarios::listar');
	$routes->get('usuarios/nuevo', 'Usuarios::nuevo');
	$routes->post('usuarios/registrar', 'Usuarios::registrar');
	$routes->get('usuarios/editar/(:num)', 'Usuarios::editar/$1');
	$routes->post('usuarios/actualizar', 'Usuarios::actualizar');
	$routes->get('usuarios/eliminar/(:num)', 'Usuarios::eliminar/$1');
	$routes->get('usuarios/vaciar', 'Usuarios::vaciar');
	$routes->get('usuarios/reciclaje', 'Usuarios::reciclaje');
	$routes->get('usuarios/restaurar/(:num)', 'Usuarios::restaurar/$1');
	$routes->get('usuarios/perfil', 'Usuarios::perfil');
	$routes->post('usuarios/cambiar', 'Usuarios::cambiar');
	$routes->get('usuarios/salir', 'Usuarios::salir');

	// Cajas
	$routes->get('cajas', 'Cajas::index');
	$routes->get('cajas/listar', 'Cajas::listar');
	$routes->get('cajas/nuevo', 'Cajas::nuevo');
	$routes->post('cajas/registrar', 'Cajas::registrar');
	$routes->get('cajas/editar/(:num)', 'Cajas::editar/$1');
	$routes->post('cajas/actualizar', 'Cajas::actualizar');
	$routes->get('cajas/eliminar/(:num)', 'Cajas::eliminar/$1');
	$routes->get('cajas/vaciar', 'Cajas::vaciar');
	$routes->get('cajas/reciclaje', 'Cajas::reciclaje');
	$routes->get('cajas/restaurar/(:num)', 'Cajas::restaurar/$1');
	$routes->get('cajas/cierre', 'Cajas::cierre');
	$routes->get('cajas/abrir', 'Cajas::abrir');
	$routes->get('cajas/cerrar', 'Cajas::cerrar');
	$routes->post('cajas/registrarCajaCierre', 'Cajas::registrarCajaCierre');

	// Clientes
	$routes->get('clientes', 'Clientes::index');
	$routes->get('clientes/listar', 'Clientes::listar');
	$routes->get('clientes/nuevo', 'Clientes::nuevo');
	$routes->post('clientes/registrar', 'Clientes::registrar');
	$routes->get('clientes/editar/(:num)', 'Clientes::editar/$1');
	$routes->post('clientes/actualizar', 'Clientes::actualizar');
	$routes->get('clientes/eliminar/(:num)', 'Clientes::eliminar/$1');
	$routes->get('clientes/vaciar', 'Clientes::vaciar');
	$routes->get('clientes/reciclaje', 'Clientes::reciclaje');
	$routes->get('clientes/restaurar/(:num)', 'Clientes::restaurar/$1');

	// Categorias
	$routes->get('categorias', 'Categorias::index');
	$routes->get('categorias/listar', 'Categorias::listar');
	$routes->get('categorias/nuevo', 'Categorias::nuevo');
	$routes->post('categorias/registrar', 'Categorias::registrar');
	$routes->get('categorias/editar/(:num)', 'Categorias::editar/$1');
	$routes->post('categorias/actualizar', 'Categorias::actualizar');
	$routes->get('categorias/eliminar/(:num)', 'Categorias::eliminar/$1');
	$routes->get('categorias/vaciar', 'Categorias::vaciar');
	$routes->get('categorias/reciclaje', 'Categorias::reciclaje');
	$routes->get('categorias/restaurar/(:num)', 'Categorias::restaurar/$1');

	// Marcas
	$routes->get('marcas', 'Marcas::index');
	$routes->get('marcas/listar', 'Marcas::listar');
	$routes->get('marcas/nuevo', 'Marcas::nuevo');
	$routes->post('marcas/registrar', 'Marcas::registrar');
	$routes->get('marcas/editar/(:num)', 'Marcas::editar/$1');
	$routes->post('marcas/actualizar', 'Marcas::actualizar');
	$routes->get('marcas/eliminar/(:num)', 'Marcas::eliminar/$1');
	$routes->get('marcas/vaciar', 'Marcas::vaciar');
	$routes->get('marcas/reciclaje', 'Marcas::reciclaje');
	$routes->get('marcas/restaurar/(:num)', 'Marcas::restaurar/$1');

	// Productos
	$routes->get('productos', 'Productos::index');
	$routes->get('productos/listar', 'Productos::listar');
	$routes->get('productos/nuevo', 'Productos::nuevo');
	$routes->post('productos/registrar', 'Productos::registrar');
	$routes->get('productos/editar/(:num)', 'Productos::editar/$1');
	$routes->post('productos/actualizar', 'Productos::actualizar');
	$routes->get('productos/eliminar/(:num)', 'Productos::eliminar/$1');
	$routes->get('productos/vaciar', 'Productos::vaciar');
	$routes->get('productos/reciclaje', 'Productos::reciclaje');
	$routes->get('productos/restaurar/(:num)', 'Productos::restaurar/$1');

	// Roles
	$routes->get('roles', 'Roles::index');
	$routes->get('roles/listar', 'Roles::listar');
	$routes->get('roles/nuevo', 'Roles::nuevo');
	$routes->post('roles/registrar', 'Roles::registrar');
	$routes->get('roles/editar/(:num)', 'Roles::editar/$1');
	$routes->post('roles/actualizar', 'Roles::actualizar');
	$routes->get('roles/eliminar/(:num)', 'Roles::eliminar/$1');

	// Compras
	$routes->get('compras', 'Compras::index');
	$routes->get('compras/historial', 'Compras::historial');
	$routes->get('compras/listar', 'Compras::listar');
	$routes->get('compras/addCarrito/(:num)', 'Compras::addCarrito/$1');
	$routes->get('compras/deleteCarrito/(:num)', 'Compras::deleteCarrito/$1');
	$routes->get('compras/cantidadCompra/(:num)/(:segment)', 'Compras::cantidadCompra/$1/$2');
	$routes->get('compras/precioCompra/(:num)/(:segment)', 'Compras::precioCompra/$1/$2');
	$routes->get('compras/clearCart', 'Compras::clearCart');
	$routes->get('compras/detalle', 'Compras::detalle');
	$routes->get('compras/buscar', 'Compras::buscar');
	$routes->get('compras/buscarNombre', 'Compras::buscarNombre');
	$routes->get('compras/buscarPorCodigo/(:segment)', 'Compras::buscarPorCodigo/$1');
	$routes->get('compras/generar', 'Compras::generar');
	$routes->get('compras/generarTicket/(:num)', 'Compras::generarTicket/$1');
	$routes->get('compras/anular/(:num)', 'Compras::anular/$1');

	// Ventas
	$routes->get('ventas', 'Ventas::index');
	$routes->get('ventas/historial', 'Ventas::historial');
	$routes->get('ventas/listar', 'Ventas::listar');
	$routes->get('ventas/addCarrito/(:num)', 'Ventas::addCarrito/$1');
	$routes->get('ventas/deleteCarrito/(:num)', 'Ventas::deleteCarrito/$1');
	$routes->get('ventas/cantidadVenta/(:num)/(:segment)', 'Ventas::cantidadVenta/$1/$2');
	$routes->get('ventas/precioVenta/(:num)/(:segment)', 'Ventas::precioVenta/$1/$2');
	$routes->get('ventas/clearCart', 'Ventas::clearCart');
	$routes->get('ventas/detalle', 'Ventas::detalle');
	$routes->get('ventas/buscar', 'Ventas::buscar');
	$routes->get('ventas/buscarNombre', 'Ventas::buscarNombre');
	$routes->get('ventas/buscarPorCodigo/(:segment)', 'Ventas::buscarPorCodigo/$1');
	$routes->post('ventas/generar', 'Ventas::generar');
	$routes->get('ventas/generarTicket/(:num)', 'Ventas::generarTicket/$1');
	$routes->get('ventas/anular/(:num)', 'Ventas::anular/$1');

	// Cotizaciones
	$routes->get('cotizaciones', 'Cotizaciones::index');
	$routes->get('cotizaciones/historial', 'Cotizaciones::historial');
	$routes->get('cotizaciones/listar', 'Cotizaciones::listar');
	$routes->get('cotizaciones/addCarrito/(:num)', 'Cotizaciones::addCarrito/$1');
	$routes->get('cotizaciones/deleteCarrito/(:num)', 'Cotizaciones::deleteCarrito/$1');
	$routes->get('cotizaciones/cantidadCotizacion/(:num)/(:segment)', 'Cotizaciones::cantidadCotizacion/$1/$2');
	$routes->get('cotizaciones/precioCotizacion/(:num)/(:segment)', 'Cotizaciones::precioCotizacion/$1/$2');
	$routes->get('cotizaciones/clearCart', 'Cotizaciones::clearCart');
	$routes->get('cotizaciones/detalle', 'Cotizaciones::detalle');
	$routes->get('cotizaciones/buscar', 'Cotizaciones::buscar');
	$routes->get('cotizaciones/buscarNombre', 'Cotizaciones::buscarNombre');
	$routes->get('cotizaciones/buscarPorCodigo/(:segment)', 'Cotizaciones::buscarPorCodigo/$1');
	$routes->post('cotizaciones/generar', 'Cotizaciones::generar');
	$routes->get('cotizaciones/generarTicket/(:num)', 'Cotizaciones::generarTicket/$1');
	$routes->get('cotizaciones/anular/(:num)', 'Cotizaciones::anular/$1');

	// Creditos de ventas
	$routes->get('credventas', 'Credventas::index');
	$routes->get('credventas/listar', 'Credventas::listar');
	$routes->post('credventas/abonar', 'Credventas::abonar');
	$routes->get('credventas/(:num)/generarTicket', 'Credventas::generarTicket/$1');

	// Admin / Configuracion
	$routes->get('admin', 'Admin::index');
	$routes->get('admin/dashboard', 'Admin::dashboard');
	$routes->post('admin/modificar', 'Admin::modificar');
	$routes->get('admin/comparacion/(:num)', 'Admin::comparacion/$1');
	$routes->get('admin/stockMinimo', 'Admin::stockMinimo');

	// Backup
	$routes->get('backup', 'Admin::createBackup');
});
