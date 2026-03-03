<?php

namespace App\Controllers;

use App\Models\CompanyModel;
use App\Models\UserModel;

class Auth extends BaseController
{
    /* ================= LOGIN ================= */

    public function login()
    {
        $session = session();
        $next = (string) $this->request->getGet('next');

        if ($session->get('logged_in')) {
            $default = $session->get('role') === 'recruiter'
                ? base_url('recruiter/dashboard')
                : base_url('candidate/dashboard');

            return redirect()->to($this->resolveNextUrl($next, $default));
        }

        return view('Auth/login', ['next' => $next]);
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

        if ($user['role'] === 'recruiter' && !$this->isRecruiterFullyVerified($user)) {
            return redirect()->to(base_url('recruiter/verification?email=' . urlencode($user['email'])))
                ->with('error', $this->getRecruiterVerificationMessage($user));
        }

        // Regenerate session to prevent fixation
        $session->regenerate();

        $session->set([
            'user_id' => $user['id'],
            'user_name' => $user['name'],
            'role' => $user['role'],
            'logged_in' => true
        ]);

        $defaultTarget = ($user['role'] === 'recruiter')
            ? base_url('recruiter/dashboard')
            : base_url('candidate/dashboard');

        $next = (string) $this->request->getPost('next');

        return redirect()->to($this->resolveNextUrl($next, $defaultTarget));

    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'));
    }

    public function changePassword()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('login'));
        }

        return view('Auth/change_password');
    }

    public function saveChangedPassword()
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return redirect()->to(base_url('login'));
        }

        if (!$this->validate([
            'current_password' => 'required',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $userId = (int) $session->get('user_id');
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user || empty($user['password']) || !password_verify((string) $this->request->getPost('current_password'), (string) $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Current password is incorrect.');
        }

        $newPassword = (string) $this->request->getPost('password');
        if (password_verify($newPassword, (string) $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'New password must be different from the current password.');
        }

        $userModel->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
        ]);

        return redirect()->to(base_url('account/change-password'))
            ->with('success', 'Password changed successfully.');
    }

    public function forgotPassword()
    {
        if (session()->get('logged_in')) {
            return redirect()->to(base_url('login'));
        }

        return view('Auth/forgot_password');
    }

    public function sendPasswordResetLink()
    {
        $email = strtolower(trim((string) $this->request->getPost('email')));

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('error', 'Enter a valid email address.');
        }

        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $userModel->update((int) $user['id'], [
                'password_reset_token' => $token,
                'password_reset_expires_at' => date('Y-m-d H:i:s', strtotime('+1 hour')),
            ]);

            $updatedUser = $userModel->find((int) $user['id']);
            $mailError = null;
            if ($updatedUser) {
                $this->sendPasswordResetEmail($updatedUser, $mailError);
            }
        }

        return redirect()->to(base_url('forgot-password'))
            ->with('success', 'If that email exists in our system, a password reset link has been sent.');
    }

    public function resetPassword($token)
    {
        $user = $this->findValidPasswordResetUser((string) $token);
        if (!$user) {
            return redirect()->to(base_url('forgot-password'))
                ->with('error', 'Invalid or expired password reset link.');
        }

        return view('Auth/reset_password', ['token' => (string) $token]);
    }

    public function updatePassword($token)
    {
        $user = $this->findValidPasswordResetUser((string) $token);
        if (!$user) {
            return redirect()->to(base_url('forgot-password'))
                ->with('error', 'Invalid or expired password reset link.');
        }

        if (!$this->validate([
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]',
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        (new UserModel())->update((int) $user['id'], [
            'password' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ]);

        return redirect()->to(base_url('login'))
            ->with('success', 'Password reset successful. You can now log in.');
    }

    /* ================= CANDIDATE REGISTRATION ================= */

    public function registerCandidate()
    {
        return view('Auth/register_candidate');
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
        return view('Auth/register_admin');
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

        $model = new UserModel();
        $verificationToken = bin2hex(random_bytes(32));

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
            'email_verification_token' => $verificationToken
        ]);

        $newRecruiterId = (int) $model->getInsertID();
        $recruiter = $model->find($newRecruiterId);
        $emailError = null;
        $emailSent = $recruiter ? $this->sendRecruiterVerificationEmail($recruiter, $emailError) : false;

        $redirect = redirect()->to(base_url('recruiter/verification?email=' . urlencode($email)));
        if (!$emailSent) {
            return $redirect->with(
                'error',
                'Account created, but the verification email could not be sent. '
                . ($emailError ?? 'Use the resend option below.')
            );
        }

        return redirect()->to(base_url('recruiter/verification?email=' . urlencode($email)))
            ->with('success', 'Account created. Check your inbox to verify your company email, then complete phone verification.');
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

        $firebaseConfig = [
            'apiKey' => trim((string) (env('firebase.apiKey') ?? env('FIREBASE_API_KEY') ?? '')),
            'authDomain' => trim((string) (env('firebase.authDomain') ?? env('FIREBASE_AUTH_DOMAIN') ?? '')),
            'projectId' => trim((string) (env('firebase.projectId') ?? env('FIREBASE_PROJECT_ID') ?? '')),
            'appId' => trim((string) (env('firebase.appId') ?? env('FIREBASE_APP_ID') ?? '')),
            'messagingSenderId' => trim((string) (env('firebase.messagingSenderId') ?? env('FIREBASE_MESSAGING_SENDER_ID') ?? '')),
        ];
        $firebaseConfigured = $firebaseConfig['apiKey'] !== '' && $firebaseConfig['authDomain'] !== '';

        return view('Auth/recruiter_verification', [
            'email' => $email,
            'phone' => $phone,
            'isEmailVerified' => $isEmailVerified,
            'isPhoneVerified' => $isPhoneVerified,
            'canVerifyPhone' => $isEmailVerified && !$isPhoneVerified,
            'firebaseConfig' => $firebaseConfig,
            'firebaseConfigured' => $firebaseConfigured,
            'phoneE164' => $this->normalizePhoneForFirebase($phone),
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

        $model->update($user['id'], [
            'email_verification_token' => null,
            'email_verified_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('recruiter/verification?email=' . urlencode($user['email'])))
            ->with('success', 'Email verified successfully. Please complete phone verification.');
    }

    public function verifyRecruiterPhone()
    {
        $email = strtolower(trim((string) $this->request->getPost('email')));
        $idToken = trim((string) $this->request->getPost('firebase_id_token'));

        $model = new UserModel();
        $user = $model->where('email', $email)->where('role', 'recruiter')->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Recruiter account not found.');
        }

        if (empty($user['email_verified_at'])) {
            return redirect()->back()->with('error', 'Verify your company email before phone verification.');
        }

        if ($idToken === '') {
            return redirect()->back()->with('error', 'Phone verification token missing. Please verify OTP again.');
        }

        $firebaseUser = [];
        $verifyError = null;
        if (!$this->verifyFirebasePhoneIdentityToken($idToken, $firebaseUser, $verifyError)) {
            return redirect()->back()->with('error', $verifyError ?? 'Phone verification failed. Please try again.');
        }

        $firebasePhone = (string) ($firebaseUser['phoneNumber'] ?? '');
        $storedPhone = (string) ($user['phone'] ?? '');
        if (!$this->phonesMatch($firebasePhone, $storedPhone)) {
            return redirect()->back()->with('error', 'Phone mismatch. Verify using the same phone number used during registration.');
        }

        $model->update($user['id'], [
            'phone_verified_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to(base_url('login'))
            ->with('success', 'Recruiter account verified successfully. You can now log in.');
    }

    public function resendRecruiterVerificationEmail()
    {
        $email = strtolower(trim((string) $this->request->getPost('email')));
        $model = new UserModel();
        $user = $model->where('email', $email)->where('role', 'recruiter')->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Recruiter account not found.');
        }

        if (!empty($user['email_verified_at'])) {
            return redirect()->to(base_url('recruiter/verification?email=' . urlencode($email)))
                ->with('success', 'Email is already verified. Continue with phone verification.');
        }

        $newToken = bin2hex(random_bytes(32));

        $model->update($user['id'], [
            'email_verification_token' => $newToken,
        ]);

        $updatedUser = $model->find($user['id']);
        $emailError = null;
        $emailSent = $this->sendRecruiterVerificationEmail($updatedUser, $emailError);

        if (!$emailSent) {
            return redirect()->to(base_url('recruiter/verification?email=' . urlencode($email)))
                ->with('error', 'Verification email could not be sent. ' . ($emailError ?? 'Please try again in a minute.'));
        }

        return redirect()->to(base_url('recruiter/verification?email=' . urlencode($email)))
            ->with('success', 'Verification email resent.');
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

        if ($this->shouldCaptureMailLinks()) {
            $this->captureMailPreview('Recruiter verification email', (string) $user['email'], $verifyUrl);
            return true;
        }

        try {
            $emailConfig = config('Email');
            $email = \Config\Services::email(null, false);
            $email->clear(true);

            if ($emailConfig->fromEmail !== '') {
                $email->setFrom($emailConfig->fromEmail, $emailConfig->fromName ?: 'JobBoard');
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

    private function sendPasswordResetEmail(array $user, ?string &$error = null): bool
    {
        if (empty($user['email']) || empty($user['password_reset_token'])) {
            $error = 'Missing recipient email or reset token.';
            return false;
        }

        $resetUrl = base_url('reset-password/' . $user['password_reset_token']);
        $subject = 'Reset your HireMatrix password';
        $message = "Hi " . ($user['name'] ?? 'User') . ",\n\n"
            . "We received a request to reset your password.\n"
            . "Use the link below within 1 hour:\n"
            . $resetUrl . "\n\n"
            . "If you did not request this, you can ignore this email.";

        if ($this->shouldCaptureMailLinks()) {
            $this->captureMailPreview('Password reset email', (string) $user['email'], $resetUrl);
            return true;
        }

        try {
            $emailConfig = config('Email');
            $email = \Config\Services::email(null, false);
            $email->clear(true);

            if ($emailConfig->fromEmail !== '') {
                $email->setFrom($emailConfig->fromEmail, $emailConfig->fromName ?: 'HireMatrix');
            }

            $email->setTo((string) $user['email']);
            $email->setSubject($subject);
            $email->setMessage($message);

            $sent = $email->send(false);
            if (!$sent) {
                $debug = $email->printDebugger(['headers', 'subject']);
                $error = trim(strip_tags($debug));
                log_message('error', 'Password reset email failed: ' . $debug);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            $error = $e->getMessage();
            log_message('error', 'Password reset email exception: ' . $e->getMessage());
            return false;
        }
    }

    private function findValidPasswordResetUser(string $token): ?array
    {
        $token = trim($token);
        if ($token === '') {
            return null;
        }

        $user = (new UserModel())
            ->where('password_reset_token', $token)
            ->first();

        if (!$user) {
            return null;
        }

        $expiresAt = (string) ($user['password_reset_expires_at'] ?? '');
        if ($expiresAt === '' || strtotime($expiresAt) < time()) {
            return null;
        }

        return $user;
    }

    private function shouldCaptureMailLinks(): bool
    {
        $capture = env('email.captureLinks');
        if ($capture !== null) {
            return filter_var($capture, FILTER_VALIDATE_BOOLEAN);
        }

        return defined('CI_ENVIRONMENT') && CI_ENVIRONMENT !== 'production';
    }

    private function captureMailPreview(string $title, string $recipient, string $url): void
    {
        session()->setFlashdata('mail_preview', [
            'title' => $title,
            'recipient' => $recipient,
            'url' => $url,
        ]);

        log_message('info', $title . ' preview for ' . $recipient . ': ' . $url);
    }

    private function verifyFirebasePhoneIdentityToken(string $idToken, ?array &$firebaseUser = null, ?string &$error = null): bool
    {
        $apiKey = trim((string) (env('firebase.apiKey') ?? env('FIREBASE_API_KEY') ?? ''));
        if ($apiKey === '') {
            $error = 'Firebase is not configured on server.';
            return false;
        }

        try {
            $client = \Config\Services::curlrequest();
            $response = $client->post(
                'https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=' . urlencode($apiKey),
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                    'body' => json_encode([
                        'idToken' => $idToken,
                    ], JSON_UNESCAPED_SLASHES),
                ]
            );

            $payload = json_decode((string) $response->getBody(), true) ?: [];
            $user = $payload['users'][0] ?? null;
            if (!is_array($user) || empty($user['phoneNumber'])) {
                $error = 'Firebase verification did not return a valid phone number.';
                return false;
            }

            $firebaseUser = $user;
            return true;
        } catch (\Throwable $e) {
            $error = 'Firebase phone verification failed.';
            log_message('error', 'Firebase phone token verification error: ' . $e->getMessage());
            return false;
        }
    }

    private function normalizePhoneForFirebase(string $phone): string
    {
        $phone = trim($phone);
        if ($phone === '') {
            return '';
        }

        $normalized = preg_replace('/[^\d+]/', '', $phone) ?? '';
        if (str_starts_with($normalized, '00')) {
            $normalized = '+' . substr($normalized, 2);
        }
        if (str_starts_with($normalized, '+')) {
            return '+' . preg_replace('/\D/', '', substr($normalized, 1));
        }

        $digits = preg_replace('/\D/', '', $normalized);
        if ($digits === '') {
            return '';
        }

        $defaultCountryCode = trim((string) (env('firebase.defaultCountryCode') ?? env('FIREBASE_DEFAULT_COUNTRY_CODE') ?? '+91'));
        $defaultCountryCode = '+' . preg_replace('/\D/', '', $defaultCountryCode);

        if (strlen($digits) <= 10) {
            return $defaultCountryCode . $digits;
        }

        return '+' . $digits;
    }

    private function normalizePhoneDigits(string $phone): string
    {
        return preg_replace('/\D/', '', $phone) ?? '';
    }

    private function phonesMatch(string $firebasePhone, string $storedPhone): bool
    {
        $firebaseDigits = $this->normalizePhoneDigits($firebasePhone);
        $storedDigits = $this->normalizePhoneDigits($storedPhone);

        if ($firebaseDigits === '' || $storedDigits === '') {
            return false;
        }

        if ($firebaseDigits === $storedDigits) {
            return true;
        }

        if (str_ends_with($firebaseDigits, $storedDigits) || str_ends_with($storedDigits, $firebaseDigits)) {
            return true;
        }

        if (strlen($firebaseDigits) >= 10 && strlen($storedDigits) >= 10) {
            return substr($firebaseDigits, -10) === substr($storedDigits, -10);
        }

        return false;
    }

    private function isRecruiterFullyVerified(array $user): bool
    {
        return !empty($user['phone_verified_at']);
    }

    private function getRecruiterVerificationMessage(array $user): string
    {
        $phoneVerified = !empty($user['phone_verified_at']);

        return $phoneVerified
            ? ''
            : 'Please verify your phone number before logging in.';
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

    private function resolveNextUrl(string $next, string $default): string
    {
        $next = trim($next);
        if ($next === '') {
            return $default;
        }

        $parsedNext = parse_url($next);
        $parsedBase = parse_url(base_url());
        
        if (isset($parsedNext['host']) && isset($parsedBase['host']) && $parsedNext['host'] === $parsedBase['host']) {
            return $next;
        }

        return $default;
    }
}

