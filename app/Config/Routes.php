<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */


$routes->get('/', 'Auth::login');
$routes->get('logout','Auth::logout');
$routes->post('auth/processLogin', 'Auth::processLogin');

// Student routes (hanya student)
$routes->group('client', ['filter' => 'auth:dapur'], function($routes){
    $routes->get('home', 'Clients::client');
});

// Admin routes (hanya admin)
$routes->group('admin', ['filter' => 'auth:gudang'], function($routes){
    $routes->get('home','Admins::admin');
    $routes->get('bahan_baku','Admins::bahan_baku');
    $routes->get('bahan_baku/add','Admins::add_bahan_baku');
    $routes->post('bahan_baku/save','Admins::save_bahan_baku');
});

