<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRecruiterOverrideFieldsToInterviewSessions extends Migration
{
    public function up(): void
    {
        $fields = [];

        if (!$this->db->fieldExists('recruiter_override_score', 'interview_sessions')) {
            $fields['recruiter_override_score'] = [
                'type'       => 'DECIMAL',
                'constraint' => '4,1',
                'null'       => true,
                'after'      => 'overall_rating',
            ];
        }

        if (!$this->db->fieldExists('recruiter_flag', 'interview_sessions')) {
            $fields['recruiter_flag'] = [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'recruiter_override_score',
            ];
        }

        if (!$this->db->fieldExists('recruiter_note', 'interview_sessions')) {
            $fields['recruiter_note'] = [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'recruiter_flag',
            ];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('interview_sessions', $fields);
        }
    }

    public function down(): void
    {
        foreach (['recruiter_override_score', 'recruiter_flag', 'recruiter_note'] as $col) {
            if ($this->db->fieldExists($col, 'interview_sessions')) {
                $this->forge->dropColumn('interview_sessions', $col);
            }
        }
    }
}
