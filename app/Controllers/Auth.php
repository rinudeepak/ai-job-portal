<?php

namespace App\Controllers;

use App\Models\CompanyModel;
use App\Models\UserModel;

class Auth extends BaseController
{
    /* ================= LOGIN ================= */

    public function login()
    {
        return view('Auth/login');
    }

    public function authenticate()
    {
        // Validate input
        if (!$this->validate([
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]'
        ])) {
            return redirect()->back()->with('error', 'Invalid input');
        }

        $model = new UserModel();
        $session = session();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $model->where('email', $email)->first();

        // Constant-time comparison to prevent timing attacks
        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Invalid email or password');
        }

        // Regenerate session to prevent fixation
        $session->regenerate();

        $session->set([
            'user_id' => $user['id'],
            'user_name' => $user['name'],
            'role' => $user['role'],
            'logged_in' => true
        ]);

        // Recruiters currently need phone verification before accessing recruiter features.
        if ($user['role'] === 'recruiter' && !$this->isRecruiterFullyVerified($user)) {
            $session->destroy();
            return redirect()->to(base_url('recruiter/verification?email=' . urlencode($user['email'])))
                ->with('error', 'Please verify your phone number before logging in.');
        }

        return ($user['role'] === 'recruiter')
            ? redirect()->to(base_url('recruiter/dashboard'))
            : redirect()->to(base_url('candidate/dashboard'));

    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'));
    }

    public function dashboard()
    {
        $session = session();
        // 1️⃣ Check login
        if (!$session->get('logged_in')) {
            return redirect()->to(base_url('login'));
        }

        $userId = $session->get('user_id');
        $role = $session->get('role');

        // CANDIDATE DASHBOARD WITH NOTIFICATIONS
        $applicationModel = model('ApplicationModel');
        $notificationModel = model('NotificationModel');
        $userModel = model('UserModel');


        // Get user data
        $user = $userModel->find($userId);

        // Get candidate applications
        $applications = $applicationModel
            ->select('applications.*, jobs.title as job_title')
            ->join('jobs', 'jobs.id = applications.job_id', 'left')
            ->where('applications.candidate_id', $userId)
            ->findAll();

        // Trigger notifications for each application
        foreach ($applications as $application) {
            // Check if notification already exists for this application status
            $existingNotification = $notificationModel->getNotificationByApplicationStatus(
                $userId,
                $application['id'],
                $application['status']
            );

            // Only create if doesn't exist
            if (!$existingNotification) {
                $notificationModel->triggerApplicationNotifications($userId, $application);
            }
        }

        // Check if resume is uploaded (only create once)
        if (empty($user['resume_path'])) {
            $resumeNotification = $notificationModel
                ->where('user_id', $userId)
                ->where('type', 'resume_not_uploaded')
                ->where('is_read', 0)
                ->first();

            if (!$resumeNotification) {
                $notificationModel->createNotification(
                    $userId,
                    null,
                    'resume_not_uploaded',
                    'Your profile is incomplete. Please upload your resume to apply for jobs.',
                    base_url('candidate/profile')
                );
            }
        }

        // Fetch all unread notifications
        $notifications = $notificationModel->getUnreadNotifications($userId, 20);

        // Get unread count
        $unreadCount = $notificationModel->getUnreadCount($userId);


        return ($role === 'admin')
            ? view('recruiter/dashboard')
            : view('candidate/dashboard', [
                'notifications' => $notifications,
                'unread_count' => $unreadCount,
                'applications' => $applications,
                'user' => $user
            ]);
    }

    /* ================= CANDIDATE REGISTRATION ================= */

    public function registerCandidate()
    {
        return view('auth/register_candidate');
    }

    public function saveCandidate()
    {
        // Validate input
        if (!$this->validate([
            'name' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'phone' => 'required|numeric|min_length[10]',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]'
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $model = new UserModel();
        $model->insert([
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'password' => password_hash(
                $this->request->getPost('password'),
                PASSWORD_DEFAULT
            ),
            'role' => 'candidate'
        ]);

        return redirect()->to(base_url('login'));
    }


    public function googleCandidateStart()
    {
        $clientId = trim((string) (env('google.clientId') ?? env('GOOGLE_CLIENT_ID') ?? ''));
        $redirectUri = base_url('auth/google/callback');

        if ($clientId === '') {
            return redirect()->to(base_url('register'))
                ->with('error', 'Google sign-up is not configured. Please contact support.');
        }

        $state = bin2hex(random_bytes(16));
        session()->set('google_oauth_state', $state);

        $query = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'access_type' => 'online',
            'prompt' => 'select_account',
        ]);

        return redirect()->to('https://accounts.google.com/o/oauth2/v2/auth?' . $query);
    }

    public function googleCandidateCallback()
    {
        $request = $this->request;
        $session = session();

        if ($request->getGet('error')) {
            return redirect()->to(base_url('register'))
                ->with('error', 'Google sign-up was cancelled or denied.');
        }

        $state = (string) $request->getGet('state');
        $expectedState = (string) $session->get('google_oauth_state');
        $session->remove('google_oauth_state');

        if ($state === '' || $expectedState === '' || !hash_equals($expectedState, $state)) {
            return redirect()->to(base_url('register'))
                ->with('error', 'Invalid Google sign-up state. Please try again.');
        }

        $code = (string) $request->getGet('code');
        if ($code === '') {
            return redirect()->to(base_url('register'))
                ->with('error', 'Missing Google authorization code.');
        }

        $clientId = trim((string) (env('google.clientId') ?? env('GOOGLE_CLIENT_ID') ?? ''));
        $clientSecret = trim((string) (env('google.clientSecret') ?? env('GOOGLE_CLIENT_SECRET') ?? ''));
        $redirectUri = base_url('auth/google/callback');

        if ($clientId === '' || $clientSecret === '') {
            return redirect()->to(base_url('register'))
                ->with('error', 'Google sign-up is not configured.');
        }

        try {
            $http = \Config\Services::curlrequest();

            $tokenResponse = $http->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'code' => $code,
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'redirect_uri' => $redirectUri,
                    'grant_type' => 'authorization_code',
                ],
            ]);

            $tokenData = json_decode((string) $tokenResponse->getBody(), true) ?: [];
            $accessToken = (string) ($tokenData['access_token'] ?? '');

            if ($accessToken === '') {
                return redirect()->to(base_url('register'))
                    ->with('error', 'Google sign-up failed while getting access token.');
            }

            $userResponse = $http->get('https://www.googleapis.com/oauth2/v3/userinfo', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
            ]);

            $googleUser = json_decode((string) $userResponse->getBody(), true) ?: [];
        } catch (\Throwable $e) {
            log_message('error', 'Google OAuth callback failed: ' . $e->getMessage());
            return redirect()->to(base_url('register'))
                ->with('error', 'Unable to connect to Google right now. Please try again.');
        }

        $googleId = trim((string) ($googleUser['sub'] ?? ''));
        $email = strtolower(trim((string) ($googleUser['email'] ?? '')));
        $name = trim((string) ($googleUser['name'] ?? ''));
        $emailVerified = (bool) ($googleUser['email_verified'] ?? false);

        if ($googleId === '' || $email === '' || !$emailVerified) {
            return redirect()->to(base_url('register'))
                ->with('error', 'Google did not return a verified email address.');
        }

        $userModel = new UserModel();

        $user = $userModel
            ->groupStart()
                ->where('google_id', $googleId)
                ->orWhere('email', $email)
            ->groupEnd()
            ->first();

        if ($user) {
            if (($user['role'] ?? '') !== 'candidate') {
                return redirect()->to(base_url('login'))
                    ->with('error', 'This Google account is linked to a recruiter account. Please use recruiter login.');
            }

            $updates = [];
            if (empty($user['google_id'])) {
                $updates['google_id'] = $googleId;
            }
            if (empty($user['name']) && $name !== '') {
                $updates['name'] = $name;
            }
            if (!empty($updates)) {
                $userModel->update((int) $user['id'], $updates);
                $user = $userModel->find((int) $user['id']) ?? $user;
            }
        } else {
            $insertData = [
                'name' => $name !== '' ? $name : 'Google User',
                'email' => $email,
                'phone' => '',
                'password' => password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT),
                'role' => 'candidate',
                'google_id' => $googleId,
            ];

            $userModel->insert($insertData);
            $newId = (int) $userModel->getInsertID();
            $user = $userModel->find($newId);

            if (!$user) {
                return redirect()->to(base_url('register'))
                    ->with('error', 'Failed to create candidate account from Google profile.');
            }
        }

        $session->regenerate();
        $session->set([
            'user_id' => $user['id'],
            'user_name' => $user['name'],
            'role' => $user['role'],
            'logged_in' => true,
        ]);

        return redirect()->to(base_url('candidate/dashboard'));
    }
    /* ================= ADMIN REGISTRATION ================= */

    public function registerAdmin()
    {
        return view('auth/register_admin');
    }

    public function saveAdmin()
    {
        // Validate input
        if (!$this->validate([
            'company_name' => 'required|min_length[2]',
            'name' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'phone' => 'required|numeric|min_length[10]|max_length[15]',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]'
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $email = strtolower(trim((string) $this->request->getPost('email')));
        $companyName = trim((string) $this->request->getPost('company_name'));
        $domain = substr(strrchr($email, "@"), 1);
        if ($this->isFreeEmailDomain($domain)) {
            return redirect()->back()->withInput()->with(
                'error',
                'Please use your company email address (free email providers are not allowed for recruiters).'
            );
        }

        $companyModel = new CompanyModel();
        $company = $companyModel->where('LOWER(name)', strtolower($companyName))->first();
        if (!$company) {
            $companyModel->insert([
                'name' => $companyName,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $companyId = (int) $companyModel->getInsertID();
        } else {
            $companyId = (int) $company['id'];
        }

        $otp = (string) random_int(100000, 999999);
        $otpExpiresAt = date('Y-m-d H:i:s', time() + (10 * 60));
        $model = new UserModel();
        $model->insert([
            'company_name' => $companyName,
            'company_id' => $companyId,
            'name' => $this->request->getPost('name'),
            'email' => $email,
            'phone' => $this->request->getPost('phone'),
            'password' => password_hash(
                $this->request->getPost('password'),
                PASSWORD_DEFAULT
            ),
            'role' => 'recruiter',
            'email_verification_token' => null,
            'phone_otp' => $otp,
            'phone_otp_expires_at' => $otpExpiresAt
        ]);

        $userId = $model->getInsertID();
        $user = $model->find($userId);
        $this->sendRecruiterOtpSms((string) ($user['phone'] ?? ''), $otp);

        return redirect()->to(base_url('recruiter/verification?email=' . urlencode($email)))
            ->with('success', 'Account created. Phone OTP has been sent for verification.');
    }

    public function recruiterVerification()
    {
        $email = strtolower(trim((string) $this->request->getGet('email')));
        $phone = '';
        $isEmailVerified = false;
        $isPhoneVerified = false;

        if ($email !== '') {
            $model = new UserModel();
            $user = $model->where('email', $email)->where('role', 'recruiter')->first();
            $phone = (string) ($user['phone'] ?? '');
            $isEmailVerified = !empty($user['email_verified_at']);
            $isPhoneVerified = !empty($user['phone_verified_at']);
        }

        return view('Auth/recruiter_verification', [
            'email' => $email,
            'phone' => $phone,
            'isEmailVerified' => $isEmailVerified,
            'isPhoneVerified' => $isPhoneVerified
        ]);
    }

    public function verifyRecruiterEmail($token)
    {
        $model = new UserModel();
        $user = $model->where('email_verification_token', $token)
            ->where('role', 'recruiter')
            ->first();

        if (!$user) {
            return redirect()->to(base_url('login'))->with('error', 'Invalid or expired email verification link.');
        }

        $otp = (string) random_int(100000, 999999);
        $otpExpiresAt = date('Y-m-d H:i:s', time() + (10 * 60));

        $model->update($user['id'], [
            'email_verification_token' => null,
            'email_verified_at' => date('Y-m-d H:i:s'),
            'phone_otp' => $otp,
            'phone_otp_expires_at' => $otpExpiresAt
        ]);

        $this->sendRecruiterOtpSms((string) ($user['phone'] ?? ''), $otp);

        return redirect()->to(base_url('recruiter/verification?email=' . urlencode($user['email'])))
            ->with('success', 'Email verified successfully. Phone OTP has been sent. Please complete phone verification.');
    }

    public function verifyRecruiterPhone()
    {
        $email = strtolower(trim((string) $this->request->getPost('email')));
        $otp = trim((string) $this->request->getPost('otp'));

        $model = new UserModel();
        $user = $model->where('email', $email)->where('role', 'recruiter')->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Recruiter account not found.');
        }

        if (empty($user['phone_otp']) || empty($user['phone_otp_expires_at'])) {
            return redirect()->back()->with('error', 'OTP not generated. Please resend OTP.');
        }

        if ($user['phone_otp'] !== $otp) {
            return redirect()->back()->with('error', 'Invalid OTP.');
        }

        if (strtotime((string) $user['phone_otp_expires_at']) < time()) {
            return redirect()->back()->with('error', 'OTP expired. Please resend OTP.');
        }

        $model->update($user['id'], [
            'phone_verified_at' => date('Y-m-d H:i:s'),
            'phone_otp' => null,
            'phone_otp_expires_at' => null
        ]);

        return redirect()->to(base_url('login'))
            ->with('success', 'Phone verified successfully. You can now log in.');
    }

    public function resendRecruiterVerificationEmail()
    {
        $email = strtolower(trim((string) $this->request->getPost('email')));
        log_message('error', 'Resend verification email requested for: ' . $email);
        $model = new UserModel();
        $user = $model->where('email', $email)->where('role', 'recruiter')->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Recruiter account not found.');
        }

        $newToken = bin2hex(random_bytes(32));

        $model->update($user['id'], [
            'email_verification_token' => $newToken,
        ]);

        $updatedUser = $model->find($user['id']);
        $emailError = null;
        $emailSent = $this->sendRecruiterVerificationEmail($updatedUser, $emailError);

        if (!$emailSent) {
            log_message('error', 'Resend verification email failed for: ' . $email . ' | ' . ($emailError ?? 'unknown'));
            return redirect()->to(base_url('recruiter/verification?email=' . urlencode($email)))
                ->with('error', 'Verification email could not be sent. ' . ($emailError ?? 'Please try again in a minute.'));
        }

        log_message('error', 'Resend verification email send() returned success for: ' . $email);
        return redirect()->to(base_url('recruiter/verification?email=' . urlencode($email)))
            ->with('success', 'Verification email resent.');
    }

    public function resendRecruiterPhoneOtp()
    {
        $email = strtolower(trim((string) $this->request->getPost('email')));
        $model = new UserModel();
        $user = $model->where('email', $email)->where('role', 'recruiter')->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Recruiter account not found.');
        }

        $newOtp = (string) random_int(100000, 999999);
        $otpExpiresAt = date('Y-m-d H:i:s', time() + (10 * 60));

        $model->update($user['id'], [
            'phone_otp' => $newOtp,
            'phone_otp_expires_at' => $otpExpiresAt
        ]);

        $updatedUser = $model->find($user['id']);
        $this->sendRecruiterOtpSms((string) ($updatedUser['phone'] ?? ''), $newOtp);

        return redirect()->to(base_url('recruiter/verification?email=' . urlencode($email)))
            ->with('success', 'Phone OTP resent.');
    }

    private function sendRecruiterVerificationEmail(array $user, ?string &$error = null): bool
    {
        if (empty($user['email']) || empty($user['email_verification_token'])) {
            $error = 'Missing recipient email or verification token.';
            return false;
        }

        $verifyUrl = base_url('recruiter/verify-email/' . $user['email_verification_token']);
        $subject = 'Verify your recruiter account';
        $message = "Hi " . ($user['name'] ?? 'Recruiter') . ",\n\n"
            . "Please verify your company email by clicking this link:\n"
            . $verifyUrl . "\n\n"
            . "If you did not create this account, ignore this email.";

        try {
            $email = \Config\Services::email(null, false);
            $fromEmail = trim((string) env('email.fromEmail'));
            $fromName = trim((string) env('email.fromName')) ?: 'JobBoard';
            $smtpHost = trim((string) env('email.SMTPHost'));
            $smtpUser = trim((string) env('email.SMTPUser'));
            $smtpPass = trim((string) env('email.SMTPPass'));
            $smtpPort = (int) env('email.SMTPPort');
            $smtpCrypto = trim((string) env('email.SMTPCrypto'));
            $mailType = trim((string) env('email.mailType')) ?: 'text';

            $email->initialize([
                'protocol' => 'smtp',
                'SMTPHost' => $smtpHost,
                'SMTPUser' => $smtpUser,
                'SMTPPass' => $smtpPass,
                'SMTPPort' => $smtpPort > 0 ? $smtpPort : 587,
                'SMTPCrypto' => $smtpCrypto !== '' ? $smtpCrypto : 'tls',
                'SMTPTimeout' => 30,
                'mailType' => $mailType,
                'charset' => 'UTF-8',
                'newline' => "\r\n",
                'CRLF' => "\r\n",
            ]);
            $email->clear(true);

            if ($fromEmail !== '') {
                $email->setFrom($fromEmail, $fromName);
            }
            $email->setTo($user['email']);
            $email->setSubject($subject);
            $email->setMessage($message);
            log_message('info', 'Recruiter verification email send attempt to: ' . (string) $user['email']);
            $sent = $email->send(false);
            log_message('info', 'Recruiter verification email send status: ' . ($sent ? 'sent' : 'failed'));

            if (!$sent) {
                $debug = $email->printDebugger(['headers', 'subject']);
                $error = trim(strip_tags($debug));
                log_message('error', 'Email send failed for recruiter verification (send=false): ' . $debug);
                return false;
            }
            return true;
        } catch (\Throwable $e) {
            $error = $e->getMessage();
            log_message('error', 'Email send failed for recruiter verification: ' . $e->getMessage());
            return false;
        }
    }

    private function sendRecruiterOtpSms(string $phone, string $otp): void
    {
        $sid = (string) env('twilio.accountSid');
        $token = (string) env('twilio.authToken');
        $from = (string) env('twilio.fromNumber');

        if ($sid === '' || $token === '' || $from === '' || trim($phone) === '') {
            log_message('warning', 'Twilio SMS not configured; OTP SMS not sent.');
            return;
        }

        try {
            $client = \Config\Services::curlrequest();
            $client->post(
                'https://api.twilio.com/2010-04-01/Accounts/' . $sid . '/Messages.json',
                [
                    'auth' => [$sid, $token],
                    'form_params' => [
                        'To' => $phone,
                        'From' => $from,
                        'Body' => 'Your JobBoard recruiter OTP is: ' . $otp . '. Valid for 10 minutes.',
                    ],
                ]
            );
        } catch (\Throwable $e) {
            log_message('error', 'Failed to send OTP SMS: ' . $e->getMessage());
        }
    }

    private function isRecruiterFullyVerified(array $user): bool
    {
        return !empty($user['phone_verified_at']);
    }

    private function isFreeEmailDomain(string $domain): bool
    {
        $freeDomains = [
            'gmail.com',
            'yahoo.com',
            'hotmail.com',
            'outlook.com',
            'live.com',
            'aol.com',
            'icloud.com',
            'protonmail.com',
            'gmx.com',
            'mail.com'
        ];

        return in_array(strtolower(trim($domain)), $freeDomains, true);
    }
}
