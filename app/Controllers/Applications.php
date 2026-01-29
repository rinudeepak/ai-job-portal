<?php

namespace App\Controllers;

use App\Models\ApplicationModel;

class Applications extends BaseController
{
    public function apply($jobId)
    {
        $session = session();

        // 1️⃣ Check login
        if (!$session->get('logged_in')) {
            return redirect()->to(base_url('login'));
        }


        $candidateId = $session->get('user_id');

        $model = new ApplicationModel();
        // $notificationModel = model('NotificationModel');
        // $userModel = model('UserModel');


        // 3️⃣ Prevent duplicate application
        $alreadyApplied = $model
            ->where('job_id', $jobId)
            ->where('candidate_id', $candidateId)
            ->first();

        if ($alreadyApplied) {
            return redirect()->back()->with('error', 'You have already applied for this job');
        }

        // 4️⃣ Save application
        $model->insert([
            'job_id' => $jobId,
            'candidate_id' => $candidateId,
            'status' => 'applied',
            'applied_at' => date('Y-m-d H:i:s')
        ]);
        // Get inserted application id
        $applicationId = $model->getInsertID();

        // Track stage

        $stageModel = model('StageHistoryModel');
        $stageModel->moveToStage($applicationId, 'Applied');

        


        return redirect()->back()->with('success', 'Job applied successfully');
    }
}
