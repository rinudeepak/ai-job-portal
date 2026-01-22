<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateInterviewSessionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],

            'session_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'unique'     => true,
            ],

            'position' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],

            'conversation_history' => [
                'type' => 'LONGTEXT',
            ],

            'turn' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],

            'max_turns' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 10,
            ],

            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'completed', 'evaluated'],
                'default'    => 'active',
            ],

            'evaluation_data' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],

            'technical_score' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
            ],

            'communication_score' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
            ],

            'problem_solving_score' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
            ],

            'adaptability_score' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
            ],

            'enthusiasm_score' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
            ],

            'overall_rating' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
            ],

            'ai_decision' => [
                'type'       => 'ENUM',
                'constraint' => ['qualified', 'rejected'],
                'null'       => true,
            ],

            'created_at' => [
                'type' => 'DATETIME',
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

        // Primary Key
        $this->forge->addKey('id', true);

        // Indexes
        $this->forge->addKey('user_id');
        $this->forge->addKey('status');
        $this->forge->addKey(['user_id', 'status']);
        $this->forge->addKey('created_at');

        // Create Table
        $this->forge->createTable('interview_sessions', true);
    }

    public function down()
    {
        $this->forge->dropTable('interview_sessions', true);
    }

}
