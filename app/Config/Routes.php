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

// recruiter registration (restricted)
$routes->get('recruiter/register', 'Auth::registerAdmin');
$routes->post('recruiter/register', 'Auth::saveAdmin');

$routes->get('dashboard', 'Auth::dashboard');
$routes->get('recruiter/dashboard', 'Auth::dashboard');

$routes->get('jobs', 'Jobs::index');
$routes->get('job/(:num)', 'Jobs::jobDetail/$1');
$routes->post('job/apply/(:num)', 'Applications::apply/$1');

$routes->get('recruiter/post_job', 'Recruiter::postJob');
$routes->post('recruiter/post_job', 'Recruiter::saveJob');

$routes->get('candidate/profile', 'Candidate::profile');
$routes->post('candidate/profile', 'Candidate::saveProfile');

