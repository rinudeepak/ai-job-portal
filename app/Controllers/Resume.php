<?php

namespace App\Controllers;

use App\Libraries\ResumeParser;
use App\Models\CandidateSkillsModel;


class Resume extends BaseController
{
    public function upload()
    {
        $file = $this->request->getFile('resume');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'No file uploaded or invalid file'
            ]);
        }

        // Allow PDF, DOCX, TXT
        $allowedTypes = ['pdf', 'docx', 'txt'];

        if (!in_array($file->getExtension(), $allowedTypes)) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Only PDF, DOCX or TXT files allowed'
            ]);
        }

        // Move file to writable/uploads/resumes
        $uploadPath = WRITEPATH . 'uploads/resumes/';

        if (!$file->move($uploadPath)) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'File upload failed'
            ]);
        }

        $filePath = $uploadPath . $file->getName();

        // Parse resume
        $parser = new ResumeParser();
        $result = $parser->parse($filePath);
        $skillModel = new CandidateSkillsModel();

        $candidateId = session()->get('user_id');

        foreach ($result['skills'] as $skill) {
            $skillModel->insert([
                'candidate_id' => $candidateId,
                'skill_name'   => $skill['name']
                
            ]);
        }
        return redirect()->back()->with('upload_success', 'Resume Uploaded Successfully');
        
    }
}
