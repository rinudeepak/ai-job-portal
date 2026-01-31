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

// $routes->get('dashboard', 'Auth::dashboard');
// Candidate Dashboard Routes
$routes->group('candidate', ['namespace' => 'App\Controllers', 'filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'CandidateDashboardController::index');
    $routes->get('/', 'CandidateDashboardController::index'); // Default route
});

// Dashboard Routes (Admin)
$routes->group('recruiter', ['namespace' => 'App\Controllers', 'filter' => 'auth'], function($routes) {
    
    // Main Dashboard
    $routes->get('dashboard', 'DashboardController::index');
    
    // Leaderboard
    $routes->get('dashboard/leaderboard', 'DashboardController::leaderboard');
    
    // Excel Exports
    $routes->get('dashboard/export-excel', 'DashboardController::exportExcel');
});

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

// Notification Routes
$routes->group('notifications', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'NotificationController::index');
    $routes->get('mark-read/(:num)', 'NotificationController::markAsRead/$1');
    $routes->get('mark-all-read', 'NotificationController::markAllAsRead');
    $routes->get('delete/(:num)', 'NotificationController::delete/$1');
});

// Interview Slot Booking Routes
$routes->group('candidate', ['filter' => 'auth'], function($routes) {
    $routes->get('book-slot/(:num)', 'SlotBookingController::bookSlot/$1');
    $routes->post('process-booking', 'SlotBookingController::processBooking');
    $routes->get('reschedule-slot/(:num)', 'SlotBookingController::rescheduleSlot/$1');
    $routes->post('process-reschedule', 'SlotBookingController::processReschedule');
    $routes->get('my-bookings', 'SlotBookingController::myBookings');
});

// Interview Slot Management Routes (Admin)
$routes->group('recruiter', ['filter' => 'auth'], function($routes) {
    
    // Slot Management
    $routes->get('slots', 'SlotManagementController::index');
    $routes->get('slots/create', 'SlotManagementController::create');
    $routes->post('slots/store', 'SlotManagementController::store');
    $routes->get('slots/edit/(:num)', 'SlotManagementController::edit/$1');
    $routes->post('slots/update/(:num)', 'SlotManagementController::update/$1');
    $routes->get('slots/delete/(:num)', 'SlotManagementController::delete/$1');
    
    // Booking Management
    $routes->get('slots/bookings', 'SlotManagementController::bookings');
    $routes->get('slots/reschedule/(:num)', 'SlotManagementController::adminReschedule/$1');
    $routes->post('slots/process-reschedule', 'SlotManagementController::processAdminReschedule');
    $routes->post('slots/mark-completed/(:num)', 'SlotManagementController::markCompleted/$1');
    
    // Bulk Actions
    $routes->post('slots/bulk-shortlist', 'SlotManagementController::bulkShortlist');
});



