<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    /* ================= LOGIN ================= */

    public function login()
    {
        return view('auth/login');
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

    /* ================= ADMIN REGISTRATION ================= */

    public function registerAdmin()
    {
        return view('auth/register_admin');
    }

    public function saveAdmin()
    {
        // Validate input
        if (!$this->validate([
            'name' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[password]'
        ])) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $model = new UserModel();
        $model->insert([
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash(
                $this->request->getPost('password'),
                PASSWORD_DEFAULT
            ),
            'role' => 'recruiter'
        ]);

        return redirect()->to(base_url('login'));
    }
}
