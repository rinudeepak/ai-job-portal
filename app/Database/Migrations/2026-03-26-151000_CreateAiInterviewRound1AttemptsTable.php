<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAiInterviewRound1AttemptsTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('ai_interview_round1_attempts')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'interview_session_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'application_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'candidate_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'question_bank_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'section_key' => [
                'type' => 'ENUM',
                'constraint' => ['reasoning', 'logical', 'fill_blank'],
                'default' => 'reasoning',
            ],
            'question_type' => [
                'type' => 'ENUM',
                'constraint' => ['mcq', 'fill_blank'],
                'default' => 'mcq',
            ],
            'question_text' => [
                'type' => 'TEXT',
            ],
            'selected_answer' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'correct_answer' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_correct' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => true,
            ],
            'score' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 0,
            ],
            'max_score' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 10,
            ],
            'answered_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('interview_session_id');
        $this->forge->addKey('application_id');
        $this->forge->addKey(['interview_session_id', 'section_key']);
        $this->forge->addUniqueKey(['interview_session_id', 'question_text'], 'uq_interview_round1_question');
        $this->forge->createTable('ai_interview_round1_attempts', true);
    }

    public function down()
    {
        $this->forge->dropTable('ai_interview_round1_attempts', true);
    }
}

