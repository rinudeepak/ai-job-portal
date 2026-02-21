<?php

namespace App\Controllers;

use App\Models\CompanyModel;
use App\Models\JobModel;
use App\Models\UserModel;

class CompanyProfile extends BaseController
{
    public function show(int $id)
    {
        $userModel = new UserModel();
        $companyModel = new CompanyModel();
        $jobModel = new JobModel();

        // First treat route param as company_id (new model).
        $company = $companyModel->find($id);
        $companyId = (int) ($company['id'] ?? 0);

        // Backward compatibility: if not found, treat it as recruiter_id.
        if (!$company) {
            $recruiter = $userModel->where('id', $id)->where('role', 'recruiter')->first();
            if (!$recruiter) {
                return redirect()->back()->with('error', 'Company profile not found.');
            }
            $companyId = (int) ($recruiter['company_id'] ?? 0);
            if ($companyId <= 0) {
                return redirect()->back()->with('error', 'Company profile not found.');
            }
            $company = $companyModel->find($companyId);
        }

        $company = $companyModel->find($companyId);
        if (!$company) {
            return redirect()->back()->with('error', 'Company profile not found.');
        }

        $openJobs = $jobModel->where('company_id', $companyId)->where('status', 'open')->orderBy('created_at', 'DESC')->findAll(5);
        $openJobsCount = $jobModel->where('company_id', $companyId)->where('status', 'open')->countAllResults();

        return view('company/profile', [
            'company' => $company,
            'openJobs' => $openJobs,
            'openJobsCount' => $openJobsCount,
        ]);
    }

    public function edit()
    {
        $session = session();
        if (!$session->get('logged_in') || $session->get('role') !== 'recruiter') {
            return redirect()->to(base_url('login'))->with('error', 'Recruiter login required.');
        }

        $userModel = new UserModel();
        $companyModel = new CompanyModel();
        $recruiterId = (int) $session->get('user_id');

        $recruiter = $userModel->find($recruiterId);
        if (!$recruiter) {
            return redirect()->to(base_url('login'))->with('error', 'User not found.');
        }
        $companyId = (int) ($recruiter['company_id'] ?? 0);
        $company = $companyId > 0 ? ($companyModel->find($companyId) ?? []) : [];
        $company['company_name'] = $company['name'] ?? $recruiter['company_name'] ?? '';

        return view('recruiter/company_profile', ['company' => $company]);
    }

    public function update()
    {
        $session = session();
        if (!$session->get('logged_in') || $session->get('role') !== 'recruiter') {
            return redirect()->to(base_url('login'))->with('error', 'Recruiter login required.');
        }

        $userId = (int) $session->get('user_id');
        $userModel = new UserModel();
        $companyModel = new CompanyModel();
        $recruiter = $userModel->find($userId);
        if (!$recruiter) {
            return redirect()->to(base_url('login'))->with('error', 'User not found.');
        }

        $companyId = (int) ($recruiter['company_id'] ?? 0);
        if ($this->request->getPost('delete_logo')) {
            if ($companyId <= 0) {
                return redirect()->to(base_url('recruiter/company-profile'))->with('error', 'Company profile not found.');
            }

            $company = $companyModel->find($companyId);
            if (!$company) {
                return redirect()->to(base_url('recruiter/company-profile'))->with('error', 'Company profile not found.');
            }

            $logoPath = trim((string) ($company['logo'] ?? ''));
            if ($logoPath !== '' && str_starts_with($logoPath, 'uploads/company_logos/')) {
                $fullPath = FCPATH . str_replace('/', DIRECTORY_SEPARATOR, $logoPath);
                if (is_file($fullPath)) {
                    @unlink($fullPath);
                }
            }

            $companyModel->update($companyId, ['logo' => null]);
            return redirect()->to(base_url('recruiter/company-profile'))->with('success', 'Company logo removed.');
        }

        $data = [
            'name' => trim((string) $this->request->getPost('company_name')),
            'website' => trim((string) $this->request->getPost('company_website')),
            'industry' => trim((string) $this->request->getPost('company_industry')),
            'size' => trim((string) $this->request->getPost('company_size')),
            'hq' => trim((string) $this->request->getPost('company_hq')),
            'branches' => trim((string) $this->request->getPost('company_branches')),
            'short_description' => trim((string) $this->request->getPost('company_short_description')),
            'what_we_do' => trim((string) $this->request->getPost('company_what_we_do')),
            'mission_values' => trim((string) $this->request->getPost('company_mission_values')),
            'contact_email' => trim((string) $this->request->getPost('company_contact_email')),
            'contact_phone' => trim((string) $this->request->getPost('company_contact_phone')),
            'contact_public' => $this->request->getPost('company_contact_public') ? 1 : 0,
        ];

        if ($data['name'] === '' || mb_strlen($data['name']) < 2) {
            return redirect()->back()->withInput()->with('error', 'Company name is required (min 2 characters).');
        }

        if ($data['website'] !== '' && !filter_var($data['website'], FILTER_VALIDATE_URL)) {
            return redirect()->back()->withInput()->with('error', 'Company website must be a valid URL.');
        }

        if ($data['contact_email'] !== '' && !filter_var($data['contact_email'], FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('error', 'Contact email must be valid.');
        }

        $logo = $this->request->getFile('company_logo');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $allowed = ['image/png', 'image/jpeg', 'image/webp', 'image/gif'];
            if (!in_array($logo->getMimeType(), $allowed, true)) {
                return redirect()->back()->withInput()->with('error', 'Logo must be PNG/JPG/WEBP/GIF.');
            }

            $uploadDir = FCPATH . 'uploads/company_logos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $newName = $logo->getRandomName();
            $logo->move($uploadDir, $newName);
            $data['logo'] = 'uploads/company_logos/' . $newName;
        }

        if ($companyId > 0) {
            $companyModel->update($companyId, $data);
        } else {
            $existingByName = $companyModel->where('LOWER(name)', strtolower($data['name']))->first();
            if ($existingByName) {
                $companyId = (int) $existingByName['id'];
                $companyModel->update($companyId, $data);
            } else {
                $companyModel->insert($data);
                $companyId = (int) $companyModel->getInsertID();
            }
        }

        // Keep users.company_name snapshot and foreign key in sync.
        $userModel->update($userId, [
            'company_name' => $data['name'],
            'company_id' => $companyId > 0 ? $companyId : null,
        ]);

        // Keep existing jobs snapshot + company_id aligned for this recruiter's old posts.
        model('JobModel')
            ->where('recruiter_id', $userId)
            ->set(['company' => $data['name'], 'company_id' => $companyId > 0 ? $companyId : null])
            ->update();

        return redirect()->to(base_url('recruiter/company-profile'))->with('success', 'Company profile updated.');
    }
}
