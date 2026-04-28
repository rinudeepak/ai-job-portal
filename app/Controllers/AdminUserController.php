<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgnator\Database\Config;

class AdminUserController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    // MAIN PAGE
    public function index()
    {
        $search = $this->request->getGet('search');
        $role   = $this->request->getGet('role');

        $builder = $this->db->table('users');

        if (!empty($search)) {
            $builder->like('name', $search);
        }

        if (!empty($role)) {
            $builder->where('role', $role);
        }

        $users = $builder->orderBy('id', 'DESC')->get()->getResultArray();

        return view('admin/users', [
            'users' => $users,
            'search' => $search,
            'role' => $role
        ]);
    }

    // AJAX SUGGESTIONS
    public function suggestions()
    {
        $term = $this->request->getGet('term');

        $builder = $this->db->table('users');

        if ($term) {
            $builder->like('name', $term);
        }

        $results = $builder
            ->select('name')
            ->limit(5)
            ->get()
            ->getResultArray();

        return $this->response->setJSON($results);
    }
}