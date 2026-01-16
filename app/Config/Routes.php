<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');
// Login
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::authenticate');
$routes->get('logout', 'Auth::logout');

// Candidate registration
$routes->get('register', 'Auth::registerCandidate');
$routes->post('register', 'Auth::saveCandidate');

// Admin registration (restricted)
$routes->get('recruiter/register', 'Auth::registerAdmin');
$routes->post('recruiter/register', 'Auth::saveAdmin');

$routes->get('jobs', 'Jobs::index');
$routes->get('job/(:num)', 'Jobs::jobDetail/$1');
$routes->post('job/apply/(:num)', 'JobApplications::apply/$1');
