<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInterviewSessionAnswersTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('interview_session_answers')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'interview_session_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'application_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'candidate_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'section_key' => [
                'type'       => 'ENUM',
                'constraint' => ['reasoning', 'logical', 'technical'],
            ],
            'question_index' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'question_text' => [
                'type' => 'TEXT',
            ],
            'answer_type' => [
                'type'       => 'ENUM',
                'constraint' => ['video', 'audio', 'text', 'mixed'],
                'default'    => 'mixed',
            ],
            'video_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'audio_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'transcript' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'duration_seconds' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'ai_score' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
            ],
            'ai_feedback' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'started_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'submitted_at' => [
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
        $this->forge->addKey('candidate_id');
        $this->forge->addKey(['interview_session_id', 'section_key']);

        $this->forge->createTable('interview_session_answers', true);
    }

    public function down()
    {
        $this->forge->dropTable('interview_session_answers', true);
    }
}
