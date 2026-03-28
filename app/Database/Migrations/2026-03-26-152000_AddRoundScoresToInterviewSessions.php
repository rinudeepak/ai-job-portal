<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoundScoresToInterviewSessions extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('interview_sessions')) {
            return;
        }

        $fields = [];
        if (!$this->db->fieldExists('round1_score', 'interview_sessions')) {
            $fields['round1_score'] = [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'after' => 'overall_rating',
            ];
        }
        if (!$this->db->fieldExists('round2_score', 'interview_sessions')) {
            $fields['round2_score'] = [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'after' => 'round1_score',
            ];
        }
        if (!$this->db->fieldExists('round1_answered', 'interview_sessions')) {
            $fields['round1_answered'] = [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'round2_score',
            ];
        }
        if (!$this->db->fieldExists('round1_total_questions', 'interview_sessions')) {
            $fields['round1_total_questions'] = [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'round1_answered',
            ];
        }
        if (!$this->db->fieldExists('interview_total_seconds', 'interview_sessions')) {
            $fields['interview_total_seconds'] = [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1800,
                'after' => 'round1_total_questions',
            ];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('interview_sessions', $fields);
        }
    }

    public function down()
    {
        if (!$this->db->tableExists('interview_sessions')) {
            return;
        }

        foreach (['round1_score', 'round2_score', 'round1_answered', 'round1_total_questions', 'interview_total_seconds'] as $column) {
            if ($this->db->fieldExists($column, 'interview_sessions')) {
                $this->forge->dropColumn('interview_sessions', $column);
            }
        }
    }
}

