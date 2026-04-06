<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddExpiredStatusToInterviewSessions extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('interview_sessions')) {
            return;
        }

        $this->db->query(
            "ALTER TABLE interview_sessions MODIFY status ENUM(
                'active',
                'submitted',
                'under_review',
                'finalized',
                'candidate_notified',
                'pending_evaluation',
                'completed',
                'evaluated',
                'expired'
            ) DEFAULT 'active'"
        );
    }

    public function down()
    {
        if (!$this->db->tableExists('interview_sessions')) {
            return;
        }

        $this->db->query(
            "ALTER TABLE interview_sessions MODIFY status ENUM(
                'active',
                'submitted',
                'under_review',
                'finalized',
                'candidate_notified',
                'pending_evaluation',
                'completed',
                'evaluated'
            ) DEFAULT 'active'"
        );
    }
}
