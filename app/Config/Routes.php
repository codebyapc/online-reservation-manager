<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Home
$routes->get('/', 'Home::index');

// Authentication routes
$routes->get('/auth/login', 'Auth::login');
$routes->post('/auth/login', 'Auth::login');
$routes->get('/auth/register', 'Auth::register');
$routes->post('/auth/register', 'Auth::register');
$routes->get('/auth/logout', 'Auth::logout');

// Reservation routes
$routes->get('/reservations', 'Reservation::index');
$routes->get('/reservations/create', 'Reservation::create');
$routes->post('/reservations/create', 'Reservation::create');
$routes->get('/reservations/edit/(:num)', 'Reservation::edit/$1');
$routes->post('/reservations/edit/(:num)', 'Reservation::edit/$1');
$routes->get('/reservations/delete/(:num)', 'Reservation::delete/$1');
$routes->get('/reservations/calendar/(:num)', 'Reservation::calendar/$1');
$routes->get('/reservations/calendar', 'Reservation::calendar');
$routes->get('/api/reservations/(:num)/(:any)', 'Reservation::apiGetReservations/$1/$2');

// Admin routes
$routes->get('/admin/dashboard', 'Admin::dashboard');
$routes->get('/admin/reservations', 'Admin::reservations');
$routes->post('/admin/reservations/update-status/(:num)', 'Admin::updateReservationStatus/$1');
$routes->get('/admin/businesses', 'Admin::businesses');
$routes->get('/admin/businesses/create', 'Admin::createBusiness');
$routes->post('/admin/businesses/create', 'Admin::createBusiness');
$routes->get('/admin/users', 'Admin::users');
