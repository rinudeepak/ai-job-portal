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
        $model = new UserModel();
        $session = session();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $model->where('email', $email)->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->back()->with('error', 'Invalid email or password');
        }

        $session->set([
            'user_id'   => $user['id'],
            'user_name' => $user['name'],
            'role'      => $user['role'],
            'logged_in' => true
        ]);

        return ($user['role'] === 'admin')
            ?  view('recruiter/dashboard')
            :  view('candidate/dashboard');
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

        
        $role = $session->get('role');
        return ($role === 'admin')
            ?  view('recruiter/dashboard')
            :  view('candidate/dashboard');
    }

    /* ================= CANDIDATE REGISTRATION ================= */

    public function registerCandidate()
    {
        return view('auth/register_candidate');
    }

    public function saveCandidate()
    {
        $model = new UserModel();
        if ($this->request->getPost('password') !== $this->request->getPost('confirm_password')) {
            return redirect()->back()->with('error', 'Passwords do not match');
        }

        $model->insert([
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash(
                $this->request->getPost('password'),
                PASSWORD_DEFAULT
            ),
            'role'     => 'candidate'
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

        $model = new UserModel();
        if ($this->request->getPost('password') !== $this->request->getPost('confirm_password')) {
            return redirect()->back()->with('error', 'Passwords do not match');
        }
        $model->insert([
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash(
                $this->request->getPost('password'),
                PASSWORD_DEFAULT
            ),
            'role'     => 'admin'
        ]);

        return redirect()->to(base_url('login'));
    }
}
