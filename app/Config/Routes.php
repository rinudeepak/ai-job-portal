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
$routes->get('admin/login', 'AdminAnalytics::login');
$routes->post('admin/login', 'AdminAnalytics::authenticate');
$routes->get('admin/logout', 'AdminAnalytics::logout', ['filter' => 'admin']);
$routes->get('admin/dashboard', 'AdminAnalytics::dashboard', ['filter' => 'admin']);
$routes->get('admin/company-ats-mappings', 'AdminCompanyAtsMappings::index', ['filter' => 'admin']);
$routes->post('admin/company-ats-mappings/save', 'AdminCompanyAtsMappings::save', ['filter' => 'admin']);
$routes->post('admin/company-ats-mappings/import', 'AdminCompanyAtsMappings::import', ['filter' => 'admin']);
$routes->get('admin/company-ats-mappings/template', 'AdminCompanyAtsMappings::template', ['filter' => 'admin']);
$routes->get('admin/company-ats-mappings/delete/(:num)', 'AdminCompanyAtsMappings::delete/$1', ['filter' => 'admin']);
$routes->get('forgot-password', 'Auth::forgotPassword');
$routes->post('forgot-password', 'Auth::sendPasswordResetLink');
$routes->get('reset-password/(:any)', 'Auth::resetPassword/$1');
$routes->post('reset-password/(:any)', 'Auth::updatePassword/$1');
$routes->get('account/change-password', 'Auth::changePassword', ['filter' => 'auth']);
$routes->post('account/change-password', 'Auth::saveChangedPassword', ['filter' => 'auth']);

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
$routes->post('company/(:num)/review', 'CompanyProfile::submitReview/$1', ['filter' => 'candidate']);
$routes->get('companies', 'CompanyProfile::index', ['filter' => 'candidate']);
$routes->post('companies/search-jobs', 'CompanyProfile::searchJobs', ['filter' => 'candidate']);
$routes->get('companies/my-targets', 'TargetCompanyController::myTargets', ['filter' => 'CandidateAuth']);
$routes->post('companies/refresh-target/(:num)', 'TargetCompanyController::refreshTarget/$1', ['filter' => 'CandidateAuth']);

// $routes->get('dashboard', 'Auth::dashboard');
// Candidate Dashboard Routes
$routes->group('candidate', ['namespace' => 'App\Controllers', 'filter' => 'candidate'], function($routes) {
    $routes->get('onboarding', 'CandidateOnboarding::index');
    $routes->get('onboarding/(:segment)', 'CandidateOnboarding::step/$1');
    $routes->post('onboarding/(:segment)', 'CandidateOnboarding::save/$1');
    $routes->get('dashboard', 'CandidateDashboardController::index');
    $routes->get('/', 'CandidateDashboardController::index'); // Default route
    $routes->get('applications', 'CandidateDashboardController::applications');
    $routes->get('job-search-strategy', 'CandidateDashboardController::jobSearchStrategy');
    $routes->get('applications/(:num)/mock-interview', 'CandidateDashboardController::mockInterview/$1');
    $routes->get('saved-jobs', 'SavedJobs::index');
    $routes->get('job-alerts', 'JobAlerts::index');
    $routes->post('job-alerts/settings', 'JobAlerts::updateSettings');
    $routes->post('job-alerts/create', 'JobAlerts::create');
    $routes->get('job-alerts/toggle/(:num)', 'JobAlerts::toggle/$1');
    $routes->get('job-alerts/delete/(:num)', 'JobAlerts::delete/$1');
    $routes->get('messages/(:num)', 'CandidateMessages::thread/$1');
    $routes->post('messages/(:num)/reply', 'CandidateMessages::reply/$1');
});

// Career Transition AI Routes
$routes->group('career-transition', ['filter' => 'candidate'], function($routes) {
    $routes->get('/', 'CareerTransition::index');
    $routes->post('create', 'CareerTransition::create');
    $routes->post('complete/(:num)', 'CareerTransition::completeTask/$1');
    $routes->get('course', 'CareerTransition::course');
    $routes->get('module/(:num)', 'CareerTransition::module/$1');
    $routes->post('dismiss-suggestion', 'CareerTransition::dismissSuggestion');
    $routes->get('reset', 'CareerTransition::reset');
});
// NEW: PDF Download Route
$routes->get('career-transition/download-pdf', 'CareerTransitionPDF_TCPDF::downloadCoursePDF', ['filter' => 'candidate']);
// Career Transition History Routes
$routes->get('career-transition/history', 'CareerTransition::history', ['filter' => 'candidate']);
$routes->get('career-transition/reactivate/(:num)', 'CareerTransition::reactivate/$1', ['filter' => 'candidate']);
// Dashboard Routes (Admin)
$routes->group('recruiter', ['namespace' => 'App\Controllers', 'filter' => 'recruiter'], function($routes) {
    
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
    $routes->get('applications/(:num)/ai-report', 'RecruiterApplications::aiInterviewReport/$1');
    $routes->post('applications/(:num)/ai-report/override', 'RecruiterApplications::overrideAiReport/$1');
    $routes->get('jobs/(:num)/applications', 'RecruiterApplications::viewByJob/$1');
    $routes->post('jobs/(:num)/applications/bulk', 'RecruiterApplications::bulkAction/$1');
    $routes->post('applications/shortlist/(:num)', 'RecruiterApplications::shortlist/$1');
    $routes->post('applications/reject/(:num)', 'RecruiterApplications::reject/$1');
    
    // Job Management
    $routes->get('jobs', 'RecruiterJobs::index');
    $routes->get('candidates', 'RecruiterCandidates::index');
    $routes->get('jobs/edit/(:num)', 'RecruiterJobs::edit/$1');
    $routes->post('jobs/update/(:num)', 'RecruiterJobs::update/$1');
    $routes->get('jobs/close/(:num)', 'RecruiterJobs::close/$1');
    $routes->get('jobs/reopen/(:num)', 'RecruiterJobs::reopen/$1');
    $routes->get('company-profile', 'CompanyProfile::edit');
    $routes->post('company-profile', 'CompanyProfile::update');
});

$routes->get('jobs', 'Jobs::index', ['filter' => 'candidate']);
$routes->get('job/(:num)', 'Jobs::jobDetail/$1', ['filter' => 'candidate']);
$routes->post('job/apply/(:num)', 'Applications::apply/$1', ['filter' => 'candidate']);
$routes->post('candidate/applications/withdraw/(:num)', 'Applications::withdraw/$1', ['filter' => 'candidate']);
$routes->get('job/save/(:num)', 'SavedJobs::save/$1', ['filter' => 'candidate']);
$routes->get('job/unsave/(:num)', 'SavedJobs::unsave/$1', ['filter' => 'candidate']);

$routes->get('recruiter/post_job', 'Recruiter::postJob', ['filter' => 'recruiter']);
$routes->post('recruiter/post_job', 'Recruiter::saveJob', ['filter' => 'recruiter']);

$routes->get('candidate/profile', 'Candidate::profile', ['filter' => 'candidate']);
$routes->get('candidate/settings', 'Candidate::settings', ['filter' => 'candidate']);
$routes->post('candidate/update-notification-settings', 'Candidate::updateNotificationSettings', ['filter' => 'candidate']);
$routes->get('candidate/resume-studio', 'Candidate::resumeStudio', ['filter' => 'candidate']);
$routes->post('candidate/resume_upload', 'Candidate::resumeUpload', ['filter' => 'candidate']);
$routes->post('candidate/resume/generate', 'Candidate::generateAiResume', ['filter' => 'candidate']);
$routes->post('candidate/resume/sync-transition', 'Candidate::syncResumeFromTransition', ['filter' => 'candidate']);
$routes->post('candidate/resume-version/(:num)/primary', 'Candidate::setPrimaryResumeVersion/$1', ['filter' => 'candidate']);
$routes->post('candidate/resume-version/(:num)/delete', 'Candidate::deleteResumeVersion/$1', ['filter' => 'candidate']);
$routes->get('candidate/resume-version/(:num)/download', 'Candidate::downloadResumeVersion/$1', ['filter' => 'candidate']);
$routes->get('candidate/resume-version/(:num)/preview', 'Candidate::previewResumeVersion/$1', ['filter' => 'candidate']);
$routes->post('candidate/analyze_github', 'Candidate::analyzeGithubSkills', ['filter' => 'candidate']);
$routes->get('candidate/download-resume', 'Candidate::downloadResume', ['filter' => 'candidate']);
$routes->get('candidate/preview-resume', 'Candidate::previewResume', ['filter' => 'candidate']);
$routes->get('candidate/serve-resume', 'Candidate::serveResume', ['filter' => 'candidate']);
$routes->post('candidate/add-skill', 'Candidate::addSkill', ['filter' => 'candidate']);
$routes->post('candidate/update_personal', 'Candidate::updatePersonal', ['filter' => 'candidate']);
$routes->post('candidate/update-career-details', 'Candidate::updateCareerDetails', ['filter' => 'candidate']);
$routes->post('candidate/update-intro-video', 'Candidate::updateIntroVideo', ['filter' => 'candidate']);
$routes->post('candidate/update-preferences', 'Candidate::updatePreferences', ['filter' => 'candidate']);
$routes->post('candidate/update-settings', 'Candidate::updateSettings', ['filter' => 'candidate']);
$routes->post('candidate/upload-photo', 'Candidate::uploadPhoto', ['filter' => 'candidate']);
$routes->post('candidate/remove-photo', 'Candidate::removePhoto', ['filter' => 'candidate']);
$routes->post('candidate/upload-intro-video', 'Candidate::uploadIntroVideo', ['filter' => 'candidate']);
$routes->post('candidate/remove-intro-video', 'Candidate::removeIntroVideo', ['filter' => 'candidate']);
$routes->post('candidate/add-work-experience', 'Candidate::addWorkExperience', ['filter' => 'candidate']);
$routes->get('candidate/delete-work-experience/(:num)', 'Candidate::deleteWorkExperience/$1', ['filter' => 'candidate']);
$routes->post('candidate/add-education', 'Candidate::addEducation', ['filter' => 'candidate']);
$routes->get('candidate/delete-education/(:num)', 'Candidate::deleteEducation/$1', ['filter' => 'candidate']);
$routes->post('candidate/add-certification', 'Candidate::addCertification', ['filter' => 'candidate']);
$routes->get('candidate/delete-certification/(:num)', 'Candidate::deleteCertification/$1', ['filter' => 'candidate']);
$routes->post('candidate/add-project', 'Candidate::addProject', ['filter' => 'candidate']);
$routes->get('candidate/delete-project/(:num)', 'Candidate::deleteProject/$1', ['filter' => 'candidate']);
$routes->post('candidate/add-interest', 'Candidate::addInterest', ['filter' => 'candidate']);
$routes->get('candidate/delete-interest/(:any)', 'Candidate::deleteInterest/$1', ['filter' => 'candidate']);

$routes->get('recruiter/candidate/(:num)', 'RecruiterCandidates::viewProfile/$1', ['filter' => 'recruiter']);
$routes->get('recruiter/candidate/(:num)/view-contact', 'RecruiterCandidates::viewContact/$1', ['filter' => 'recruiter']);
$routes->get('recruiter/candidate/(:num)/download-resume', 'RecruiterCandidates::downloadResume/$1', ['filter' => 'recruiter']);
$routes->post('recruiter/candidate/(:num)/send-message', 'RecruiterCandidates::sendMessage/$1', ['filter' => 'recruiter']);
$routes->post('recruiter/candidate/(:num)/save-notes', 'RecruiterCandidates::saveNotes/$1', ['filter' => 'recruiter']);

// AI Interview Routes (browser-based flow)
$routes->group('interview', ['filter' => 'candidate'], function($routes) {
    $routes->get('start/(:num)', 'AiInterviewController::start/$1');
    $routes->get('begin/(:num)', 'AiInterviewController::startInterview/$1');
    $routes->post('begin/(:num)', 'AiInterviewController::startInterview/$1');
    $routes->post('round1-answer/(:num)', 'AiInterviewController::saveRound1Answer/$1');
    $routes->post('answer/(:num)', 'AiInterviewController::saveAnswer/$1');
    $routes->post('integrity/(:num)', 'AiInterviewController::logIntegrityEvent/$1');
    $routes->post('complete/(:num)', 'AiInterviewController::completeInterview/$1');
    $routes->get('chat/(:num)', 'AiInterviewController::legacyRedirect/$1');
    $routes->post('submit/(:num)', 'AiInterviewController::legacyRedirect/$1');
    $routes->get('trigger-evaluation/(:num)', 'AiInterviewController::legacyRedirect/$1');
    $routes->get('results/(:num)', 'AiInterviewController::legacyRedirect/$1');
});

// Notification Routes
$routes->group('notifications', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'NotificationController::index');
    $routes->get('mark-read/(:num)', 'NotificationController::markAsRead/$1');
    $routes->get('mark-all-read', 'NotificationController::markAllAsRead');
    $routes->get('delete/(:num)', 'NotificationController::delete/$1');
});

// Interview Slot Booking Routes
$routes->group('candidate', ['filter' => 'candidate'], function($routes) {
    $routes->get('book-slot/(:num)', 'SlotBookingController::bookSlot/$1');
    $routes->post('process-booking', 'SlotBookingController::processBooking');
    $routes->get('reschedule-slot/(:num)', 'SlotBookingController::rescheduleSlot/$1');
    $routes->post('process-reschedule', 'SlotBookingController::processReschedule');
    $routes->get('my-bookings', 'SlotBookingController::myBookings');
});

// Interview Slot Management Routes (Admin)
$routes->group('recruiter', ['filter' => 'recruiter'], function($routes) {
    
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
    $routes->get('slots/review/(:num)', 'SlotManagementController::review/$1');
    $routes->post('slots/review/(:num)', 'SlotManagementController::saveReview/$1');
    
    // Bulk Actions
    $routes->post('slots/bulk-shortlist', 'SlotManagementController::bulkShortlist');
});

// Career Chatbot Routes
$routes->group('career-chatbot', ['filter' => 'candidate'], function($routes) {
    $routes->get('/', 'CareerChatbotController::index');
    $routes->post('chat', 'CareerChatbotController::chat');
    $routes->post('book-mentor', 'CareerChatbotController::bookMentor');
});

// Mentoring System Routes
$routes->group('mentoring', ['filter' => 'candidate'], function($routes) {
    $routes->get('book/(:num)', 'MentoringController::bookSession/$1');
    $routes->post('book/(:num)', 'MentoringController::processBooking/$1');
    $routes->get('sessions', 'MentoringController::mySessions');
    $routes->post('sessions/(:num)/feedback', 'MentoringController::submitFeedback/$1');
});

// Mentor Dashboard Routes
$routes->group('mentor', ['filter' => 'mentor'], function($routes) {
    $routes->get('dashboard', 'MentorController::dashboard');
    $routes->get('sessions', 'MentorController::sessions');
    $routes->post('sessions/(:num)/complete', 'MentorController::completeSession/$1');
    $routes->get('earnings', 'MentorController::earnings');
});

// Premium Career Mentor Routes
$routes->get('premium/plans', 'Premium::plans', ['filter' => 'candidate']);
$routes->group('premium-mentor', ['filter' => 'candidate'], function($routes) {
    $routes->get('/', 'PremiumCareerMentorController::index');
    $routes->get('plans', 'PremiumCareerMentorController::plans');
    $routes->post('chat', 'PremiumCareerMentorController::chat');
    $routes->post('create-career-plan', 'PremiumCareerMentorController::createCareerPlan');
});

// Payment Routes (Razorpay)
$routes->group('payment', ['filter' => 'candidate'], function($routes) {
    $routes->post('create-order', 'PaymentController::createOrder');
    $routes->post('verify',       'PaymentController::verify');
    $routes->get('history',       'PaymentController::history');
});
// Razorpay webhook — no auth filter, verified by signature
$routes->post('payment/webhook', 'PaymentController::webhook');

// Target Companies Routes
$routes->group('target-companies', ['filter' => 'candidate'], function($routes) {
    $routes->post('add', 'TargetCompanyController::add');
    $routes->get('remove/(:num)', 'TargetCompanyController::remove/$1');
    $routes->post('fetch-jobs', 'TargetCompanyController::fetchJobs');
    $routes->get('suggest', 'TargetCompanyController::suggest');
});
