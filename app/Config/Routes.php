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
$routes->post('candidate/resume_upload', 'Candidate::resumeUpload');
$routes->post('candidate/analyze_github', 'Candidate::analyzeGithubSkills');

// AI Interview Routes
// $routes->group('ai-interview', function ($routes) {
//     $routes->post('create', 'AiInterview::create');
//     $routes->post('start/(:num)', 'AiInterview::start/$1');
//     $routes->post('submit/(:num)', 'AiInterview::submit/$1');
//     $routes->get('results/(:num)', 'AiInterview::results/$1');
//     $routes->get('candidate/(:num)', 'AiInterview::candidateInterviews/$1');
// });

$routes->group('ai-interview', function ($routes) {

    // Overview page
    $routes->get('overview', 'AiInterview::overview');
    $routes->get('/', 'AiInterview::interview');
    // Start interview (generate questions + session)
    $routes->post('start', 'AiInterview::start');

    // Question page
    $routes->get('question', 'AiInterview::question');

    // Submit answer
    $routes->post('submit', 'AiInterview::submit');

    // Save draft (AJAX)
    $routes->post('save-draft', 'AiInterview::saveDraft');

    // Result page
    $routes->get('result', 'AiInterview::result');
});



