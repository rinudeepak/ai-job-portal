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
$routes->get('candidate/applied_jobs', 'Candidate::appliedJobs');
$routes->post('candidate/resume_upload', 'Candidate::resumeUpload');
$routes->post('candidate/analyze_github', 'Candidate::analyzeGithubSkills');


// AI Interview Routes
$routes->group('interview',  function($routes) {
    $routes->get('start/(:num)', 'AiInterview::start/$1');
    $routes->post('begin/(:num)', 'AiInterview::begin/$1');
    $routes->get('chat/(:num)', 'AiInterview::chat/$1');
    $routes->post('submit/(:num)', 'AiInterview::submitAnswer/$1');
    $routes->get('trigger-evaluation/(:num)', 'AiInterview::triggerEvaluation/$1');
    $routes->get('results/(:num)', 'AiInterview::results/$1');
});


// Admin Evaluation Routes
//$routes->group('admin', ['filter' => 'auth:admin'], function($routes) {
    $routes->group('admin', function($routes) {
    $routes->get('evaluations', 'Admin\EvaluationController::index');
    $routes->get('evaluations/view/(:num)', 'Admin\EvaluationController::view/$1');
    $routes->get('evaluations/export-excel', 'Admin\EvaluationController::exportExcel');
    $routes->post('evaluations/update-status/(:num)', 'Admin\EvaluationController::updateStatus/$1');
});
