<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnsureInterviewSessionsTable extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('interview_sessions')) {
            $this->forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'user_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'application_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                ],
                'job_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                ],
                'resume_version_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                ],
                'session_id' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'unique' => true,
                ],
                'position' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                ],
                'conversation_history' => [
                    'type' => 'LONGTEXT',
                ],
                'turn' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 1,
                ],
                'max_turns' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 10,
                ],
                'status' => [
                    'type' => 'ENUM',
                    'constraint' => ['active', 'completed', 'evaluated'],
                    'default' => 'active',
                ],
                'evaluation_data' => [
                    'type' => 'LONGTEXT',
                    'null' => true,
                ],
                'section_scores' => [
                    'type' => 'LONGTEXT',
                    'null' => true,
                ],
                'strengths' => [
                    'type' => 'LONGTEXT',
                    'null' => true,
                ],
                'concerns' => [
                    'type' => 'LONGTEXT',
                    'null' => true,
                ],
                'recommendation_summary' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'evaluation_version' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'null' => true,
                ],
                'technical_score' => [
                    'type' => 'DECIMAL',
                    'constraint' => '5,2',
                    'null' => true,
                ],
                'communication_score' => [
                    'type' => 'DECIMAL',
                    'constraint' => '5,2',
                    'null' => true,
                ],
                'problem_solving_score' => [
                    'type' => 'DECIMAL',
                    'constraint' => '5,2',
                    'null' => true,
                ],
                'adaptability_score' => [
                    'type' => 'DECIMAL',
                    'constraint' => '5,2',
                    'null' => true,
                ],
                'enthusiasm_score' => [
                    'type' => 'DECIMAL',
                    'constraint' => '5,2',
                    'null' => true,
                ],
                'overall_rating' => [
                    'type' => 'DECIMAL',
                    'constraint' => '5,2',
                    'null' => true,
                ],
                'ai_decision' => [
                    'type' => 'ENUM',
                    'constraint' => ['pending', 'qualified', 'needs_review', 'rejected'],
                    'default' => 'pending',
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'completed_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);

            $this->forge->addKey('id', true);
            $this->forge->addKey('user_id');
            $this->forge->addKey('application_id');
            $this->forge->addKey('job_id');
            $this->forge->addKey('status');
            $this->forge->addKey('created_at');
            $this->forge->createTable('interview_sessions', true);
            return;
        }

        // If table already exists, keep enums aligned with current AI flow.
        $this->db->query(
            "ALTER TABLE interview_sessions MODIFY ai_decision ENUM('pending','qualified','needs_review','rejected') DEFAULT 'pending'"
        );
    }

    public function down()
    {
        // No destructive rollback for safety.
    }
}

