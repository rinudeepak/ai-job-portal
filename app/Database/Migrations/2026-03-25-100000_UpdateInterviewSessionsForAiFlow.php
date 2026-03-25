<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateInterviewSessionsForAiFlow extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('interview_sessions')) {
            return;
        }

        $fields = [];

        if (!$this->db->fieldExists('application_id', 'interview_sessions')) {
            $fields['application_id'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'user_id',
            ];
        }

        if (!$this->db->fieldExists('job_id', 'interview_sessions')) {
            $fields['job_id'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'application_id',
            ];
        }

        if (!$this->db->fieldExists('resume_version_id', 'interview_sessions')) {
            $fields['resume_version_id'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'job_id',
            ];
        }

        if (!$this->db->fieldExists('section_scores', 'interview_sessions')) {
            $fields['section_scores'] = [
                'type' => 'LONGTEXT',
                'null' => true,
                'after' => 'evaluation_data',
            ];
        }

        if (!$this->db->fieldExists('strengths', 'interview_sessions')) {
            $fields['strengths'] = [
                'type' => 'LONGTEXT',
                'null' => true,
                'after' => 'section_scores',
            ];
        }

        if (!$this->db->fieldExists('concerns', 'interview_sessions')) {
            $fields['concerns'] = [
                'type' => 'LONGTEXT',
                'null' => true,
                'after' => 'strengths',
            ];
        }

        if (!$this->db->fieldExists('recommendation_summary', 'interview_sessions')) {
            $fields['recommendation_summary'] = [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'concerns',
            ];
        }

        if (!$this->db->fieldExists('evaluation_version', 'interview_sessions')) {
            $fields['evaluation_version'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'recommendation_summary',
            ];
        }

        if ($fields) {
            $this->forge->addColumn('interview_sessions', $fields);
        }

        $this->db->query(
            "ALTER TABLE interview_sessions MODIFY ai_decision ENUM('pending','qualified','needs_review','rejected') DEFAULT 'pending'"
        );
    }

    public function down()
    {
        if (!$this->db->tableExists('interview_sessions')) {
            return;
        }

        foreach ([
            'application_id',
            'job_id',
            'resume_version_id',
            'section_scores',
            'strengths',
            'concerns',
            'recommendation_summary',
            'evaluation_version',
        ] as $column) {
            if ($this->db->fieldExists($column, 'interview_sessions')) {
                $this->forge->dropColumn('interview_sessions', $column);
            }
        }

        $this->db->query(
            "ALTER TABLE interview_sessions MODIFY ai_decision ENUM('qualified','rejected') NULL"
        );
    }
}
