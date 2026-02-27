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
$routes->get('auth/google', 'Auth::googleCandidateStart');
$routes->get('auth/google/callback', 'Auth::googleCandidateCallback');

// recruiter registration (restricted)
$routes->get('recruiter/register', 'Auth::registerAdmin');
$routes->post('recruiter/register', 'Auth::saveAdmin');
$routes->get('recruiter/verification', 'Auth::recruiterVerification');
$routes->get('recruiter/verify-email/(:any)', 'Auth::verifyRecruiterEmail/$1');
$routes->post('recruiter/verify-phone', 'Auth::verifyRecruiterPhone');
$routes->post('recruiter/resend-verification-email', 'Auth::resendRecruiterVerificationEmail');
$routes->get('company/(:num)', 'CompanyProfile::show/$1', ['filter' => 'auth']);

// $routes->get('dashboard', 'Auth::dashboard');
// Candidate Dashboard Routes
$routes->group('candidate', ['namespace' => 'App\Controllers', 'filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'CandidateDashboardController::index');
    $routes->get('/', 'CandidateDashboardController::index'); // Default route
    $routes->get('applications', 'CandidateDashboardController::applications');
    $routes->get('saved-jobs', 'SavedJobs::index');
    $routes->get('messages/(:num)', 'CandidateMessages::thread/$1');
    $routes->post('messages/(:num)/reply', 'CandidateMessages::reply/$1');
});

// Career Transition AI Routes
$routes->group('career-transition', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'CareerTransition::index');
    $routes->post('create', 'CareerTransition::create');
    $routes->post('complete/(:num)', 'CareerTransition::completeTask/$1');
    $routes->get('course', 'CareerTransition::course');
    $routes->get('module/(:num)', 'CareerTransition::module/$1');
    $routes->post('dismiss-suggestion', 'CareerTransition::dismissSuggestion');
    $routes->get('reset', 'CareerTransition::reset');
});
// NEW: PDF Download Route
$routes->get('career-transition/download-pdf', 'CareerTransitionPDF_TCPDF::downloadCoursePDF');
// Career Transition History Routes
$routes->get('career-transition/history', 'CareerTransition::history');
$routes->get('career-transition/reactivate/(:num)', 'CareerTransition::reactivate/$1');
// Dashboard Routes (Admin)
$routes->group('recruiter', ['namespace' => 'App\Controllers', 'filter' => 'auth'], function($routes) {
    
    // Main Dashboard
    $routes->get('dashboard', 'DashboardController::index');
    
    // Leaderboard
    $routes->get('dashboard/leaderboard', 'DashboardController::leaderboard');
    $routes->get('jobs/(:num)/leaderboard', 'DashboardController::leaderboard/$1');
    
    // Excel Exports
    $routes->get('dashboard/export-excel', 'DashboardController::exportExcel');
    
    // Applications by Job (legacy index kept as redirect)
    $routes->get('applications', 'RecruiterApplications::index');
    $routes->get('applications/job/(:num)', 'RecruiterApplications::viewByJob/$1');
    $routes->get('jobs/(:num)/applications', 'RecruiterApplications::viewByJob/$1');
    $routes->post('jobs/(:num)/applications/bulk', 'RecruiterApplications::bulkAction/$1');
    $routes->post('applications/shortlist/(:num)', 'RecruiterApplications::shortlist/$1');
    $routes->post('applications/reject/(:num)', 'RecruiterApplications::reject/$1');
    
    // Job Management
    $routes->get('jobs', 'RecruiterJobs::index');
    $routes->get('jobs/edit/(:num)', 'RecruiterJobs::edit/$1');
    $routes->post('jobs/update/(:num)', 'RecruiterJobs::update/$1');
    $routes->get('jobs/close/(:num)', 'RecruiterJobs::close/$1');
    $routes->get('jobs/reopen/(:num)', 'RecruiterJobs::reopen/$1');
    $routes->get('company-profile', 'CompanyProfile::edit');
    $routes->post('company-profile', 'CompanyProfile::update');
});

$routes->get('jobs', 'Jobs::index', ['filter' => 'auth']);
$routes->get('job/(:num)', 'Jobs::jobDetail/$1', ['filter' => 'auth']);
$routes->post('job/apply/(:num)', 'Applications::apply/$1', ['filter' => 'auth']);
$routes->get('job/save/(:num)', 'SavedJobs::save/$1', ['filter' => 'auth']);
$routes->get('job/unsave/(:num)', 'SavedJobs::unsave/$1', ['filter' => 'auth']);

$routes->get('recruiter/post_job', 'Recruiter::postJob', ['filter' => 'auth']);
$routes->post('recruiter/post_job', 'Recruiter::saveJob', ['filter' => 'auth']);

$routes->get('candidate/profile', 'Candidate::profile', ['filter' => 'auth']);
$routes->post('candidate/resume_upload', 'Candidate::resumeUpload', ['filter' => 'auth']);
$routes->post('candidate/analyze_github', 'Candidate::analyzeGithubSkills', ['filter' => 'auth']);
$routes->get('candidate/download-resume', 'Candidate::downloadResume', ['filter' => 'auth']);
$routes->get('candidate/preview-resume', 'Candidate::previewResume', ['filter' => 'auth']);
$routes->get('candidate/serve-resume', 'Candidate::serveResume', ['filter' => 'auth']);
$routes->post('candidate/add-skill', 'Candidate::addSkill', ['filter' => 'auth']);
$routes->post('candidate/update_personal', 'Candidate::updatePersonal', ['filter' => 'auth']);
$routes->post('candidate/upload-photo', 'Candidate::uploadPhoto', ['filter' => 'auth']);
$routes->post('candidate/remove-photo', 'Candidate::removePhoto', ['filter' => 'auth']);
$routes->post('candidate/add-work-experience', 'Candidate::addWorkExperience', ['filter' => 'auth']);
$routes->get('candidate/delete-work-experience/(:num)', 'Candidate::deleteWorkExperience/$1', ['filter' => 'auth']);
$routes->post('candidate/add-education', 'Candidate::addEducation', ['filter' => 'auth']);
$routes->get('candidate/delete-education/(:num)', 'Candidate::deleteEducation/$1', ['filter' => 'auth']);
$routes->post('candidate/add-certification', 'Candidate::addCertification', ['filter' => 'auth']);
$routes->get('candidate/delete-certification/(:num)', 'Candidate::deleteCertification/$1', ['filter' => 'auth']);
$routes->post('candidate/add-interest', 'Candidate::addInterest', ['filter' => 'auth']);
$routes->get('candidate/delete-interest/(:any)', 'Candidate::deleteInterest/$1', ['filter' => 'auth']);

$routes->get('recruiter/candidate/(:num)', 'RecruiterCandidates::viewProfile/$1', ['filter' => 'auth']);
$routes->get('recruiter/candidate/(:num)/view-contact', 'RecruiterCandidates::viewContact/$1', ['filter' => 'auth']);
$routes->get('recruiter/candidate/(:num)/download-resume', 'RecruiterCandidates::downloadResume/$1', ['filter' => 'auth']);
$routes->post('recruiter/candidate/(:num)/send-message', 'RecruiterCandidates::sendMessage/$1', ['filter' => 'auth']);
$routes->post('recruiter/candidate/(:num)/save-notes', 'RecruiterCandidates::saveNotes/$1', ['filter' => 'auth']);

// AI Interview Routes
$routes->group('interview', ['filter' => 'auth'], function($routes) {
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
