<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAiInterviewQuestionBankTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('ai_interview_question_bank')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'role_key' => [
                'type' => 'VARCHAR',
                'constraint' => 120,
                'default' => 'default',
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
            'options_json' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'correct_answer' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'difficulty' => [
                'type' => 'ENUM',
                'constraint' => ['easy', 'medium', 'hard'],
                'default' => 'medium',
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
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
        $this->forge->addKey('role_key');
        $this->forge->addKey(['role_key', 'section_key', 'question_type']);
        $this->forge->createTable('ai_interview_question_bank', true);
    }

    public function down()
    {
        $this->forge->dropTable('ai_interview_question_bank', true);
    }
}

