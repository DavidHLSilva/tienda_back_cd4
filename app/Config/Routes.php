<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('api/prueba', 'Api\Prueba::index');

$routes->group('api', ['filter' => 'auth'], function($routes) {
    $routes->post('users/prueba', 'Api\Users::prueba');
    $routes->post('users/create', 'Api\Users::create');
    // Otras rutas protegidas...
});
// La ruta de login debe quedar FUERA del grupo protegido
$routes->post('auth/login', 'Api\Auth::login');
